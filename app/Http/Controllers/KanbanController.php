<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderComment;
use App\Models\OrderLog;
use App\Models\Payment;
use App\Models\Status;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class KanbanController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search');
        
        $statuses = Status::withCount('orders')->orderBy('position')->get();
        
        $query = Order::with(['client', 'user', 'items', 'items.files', 'pendingCancellation', 'pendingEditRequest'])
            ->where('is_draft', false); // Não mostrar rascunhos no kanban
        
        // Aplicar busca se fornecida
        if ($search) {
            $query->where(function($q) use ($search) {
                // Buscar por ID do pedido (com e sem zeros à esquerda)
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereRaw("CAST(id AS CHAR) LIKE ?", ["%{$search}%"]);
                
                // Buscar por dados do cliente
                $q->orWhereHas('client', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('phone_primary', 'like', "%{$search}%");
                  });
                
                // Buscar por nome da arte nos itens
                $q->orWhereHas('items', function($q3) use ($search) {
                      $q3->where('art_name', 'like', "%{$search}%");
                  });
                
                // Buscar por nome da arte nas personalizações (sublimations)
                $q->orWhereHas('items.sublimations', function($q4) use ($search) {
                      $q4->where('art_name', 'like', "%{$search}%");
                  });
            });
        }
        
        $ordersByStatus = $query->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('status_id');

        return view('kanban.index', compact('statuses', 'ordersByStatus', 'search'));
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'status_id' => 'required|exists:statuses,id',
        ]);

        $order = Order::with('status')->findOrFail($validated['order_id']);
        $oldStatus = $order->status;
        $newStatus = Status::findOrFail($validated['status_id']);
        
        $order->update(['status_id' => $validated['status_id']]);

        // Criar log de mudança de status
        $user = Auth::user();
        OrderLog::create([
            'order_id' => $order->id,
            'user_id' => $user->id ?? null,
            'user_name' => $user->name ?? 'Sistema',
            'action' => 'status_changed',
            'description' => "Status alterado de '{$oldStatus->name}' para '{$newStatus->name}'",
            'old_value' => ['status' => $oldStatus->name],
            'new_value' => ['status' => $newStatus->name],
        ]);

        // Se o novo status for "Pronto", confirmar transações do caixa
        if (strtolower($newStatus->name) === 'pronto') {
            \App\Models\CashTransaction::where('order_id', $order->id)
                ->where('status', 'pendente')
                ->update(['status' => 'confirmado']);
                
            OrderLog::create([
                'order_id' => $order->id,
                'user_id' => $user->id ?? null,
                'user_name' => $user->name ?? 'Sistema',
                'action' => 'cash_confirmed',
                'description' => "Valores do pedido confirmados no caixa (Pedido movido para Pronto)",
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status atualizado com sucesso',
        ]);
    }

    public function getOrderDetails($id): JsonResponse
    {
        $order = Order::with([
            'client',
            'items',
            'items.sublimations.size',
            'items.sublimations.location',
            'items.sublimations.files',
            'items.files',
            'comments',
            'logs',
            'pendingDeliveryRequest'
        ])->findOrFail($id);

        $payment = Payment::where('order_id', $id)->first();

        return response()->json([
            'id' => $order->id,
            'client' => $order->client,
            'items' => $order->items,
            'payment' => $payment,
            'comments' => $order->comments,
            'logs' => $order->logs,
            'total' => $order->total,
            'created_at' => $order->created_at,
            'delivery_date' => $order->delivery_date,
            'pending_delivery_request' => $order->pendingDeliveryRequest,
        ]);
    }

    public function addComment(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        $order = Order::findOrFail($id);
        $user = Auth::user();
        $userName = $user ? $user->name : 'Anônimo';

        $comment = OrderComment::create([
            'order_id' => $order->id,
            'user_id' => $user->id ?? null,
            'user_name' => $userName,
            'comment' => $validated['comment'],
        ]);

        // Criar log
        OrderLog::create([
            'order_id' => $order->id,
            'user_id' => $user->id ?? null,
            'user_name' => $userName,
            'action' => 'comment_added',
            'description' => 'Comentário adicionado',
            'new_value' => ['comment' => $validated['comment']],
        ]);

        return response()->json([
            'success' => true,
            'comment' => $comment,
            'message' => 'Comentário adicionado com sucesso',
        ]);
    }

    public function downloadCostura($id)
    {
        try {
            // Aumentar limite de memória para processamento de PDF
            ini_set('memory_limit', '1024M');
            
            \Log::info('Iniciando download de costura para pedido: ' . $id);
            
            $order = Order::with(['client', 'items'])->findOrFail($id);
            \Log::info('Pedido carregado com sucesso', ['order_id' => $order->id, 'items_count' => $order->items->count()]);

            // Processar imagens dos itens com otimizações para evitar problemas de memória
            foreach ($order->items as $item) {
                $item->hasCoverImage = false;
                $item->coverImageInfo = null;
                $item->coverImageUrl = null;
                
                if ($item->cover_image) {
                    $coverImagePath = storage_path('app/public/' . $item->cover_image);
                    
                    // Verificar se o arquivo existe usando múltiplas abordagens
                    $fileExists = false;
                    $actualPath = null;
                    
                    // Tentar 1: Caminho direto
                    if (file_exists($coverImagePath)) {
                        $fileExists = true;
                        $actualPath = $coverImagePath;
                    } else {
                        // Tentar 2: Buscar arquivo com nome similar (para lidar com caracteres especiais)
                        $directory = dirname($coverImagePath);
                        $filename = basename($coverImagePath);
                        
                        if (is_dir($directory)) {
                            $files = scandir($directory);
                            foreach ($files as $file) {
                                if ($file !== '.' && $file !== '..') {
                                    // Verificar se o arquivo tem o mesmo prefixo (ID + timestamp)
                                    if (strpos($file, substr($filename, 0, 20)) === 0) {
                                        $actualPath = $directory . DIRECTORY_SEPARATOR . $file;
                                        $fileExists = true;
                                        \Log::info('Arquivo encontrado com nome similar', [
                                            'original' => $filename,
                                            'encontrado' => $file,
                                            'path' => $actualPath
                                        ]);
                                        break;
                                    }
                                }
                            }
                        }
                    }
                    
                    if ($fileExists && $actualPath) {
                        // Verificar se o arquivo não é muito grande (limite de 1MB para economizar memória)
                        $fileSize = filesize($actualPath);
                        if ($fileSize > 1 * 1024 * 1024) {
                            // Tentar criar uma versão otimizada da imagem
                            $optimizedPath = $this->optimizeImageForPDF($actualPath);
                            if ($optimizedPath && file_exists($optimizedPath)) {
                                $actualPath = $optimizedPath;
                                $fileSize = filesize($actualPath);
                                \Log::info('Imagem otimizada criada', [
                                    'original' => $actualPath,
                                    'otimizada' => $optimizedPath,
                                    'size' => $this->formatFileSize($fileSize)
                                ]);
                            } else {
                                \Log::warning('Imagem muito grande e não foi possível otimizar, pulando', [
                                    'path' => $actualPath,
                                    'size' => $this->formatFileSize($fileSize)
                                ]);
                                continue;
                            }
                        }
                        
                        $item->hasCoverImage = true;
                        $item->coverImageInfo = [
                            'name' => basename($actualPath),
                            'size' => $this->formatFileSize($fileSize),
                            'extension' => strtoupper(pathinfo($actualPath, PATHINFO_EXTENSION)),
                            'path' => $item->cover_image
                        ];
                        
                        // Usar caminho local absoluto
                        $item->coverImageUrl = $actualPath;
                        
                        \Log::info('Imagem costura processada!', [
                            'path' => $actualPath,
                            'size' => $item->coverImageInfo['size']
                        ]);
                    } else {
                        \Log::warning('Arquivo não existe:', [
                            'path' => $coverImagePath,
                            'directory' => dirname($coverImagePath),
                            'filename' => basename($coverImagePath)
                        ]);
                    }
                } else {
                    \Log::info('Item sem imagem de capa');
                }
            }

            \Log::info('Iniciando renderização da view');
            $html = view('kanban.pdf.costura', compact('order'))->render();
            \Log::info('View renderizada com sucesso', ['html_length' => strlen($html)]);
            
            // Limpar memória antes de criar o PDF
            if (function_exists('gc_collect_cycles')) {
                gc_collect_cycles();
            }
            
            \Log::info('Iniciando criação do PDF');
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'portrait');
            
            // Configuração otimizada do DomPDF para reduzir uso de memória
            $pdf->setOptions([
                'isRemoteEnabled' => false,
                'isHtml5ParserEnabled' => false, // Desabilitar para economizar memória
                'defaultFont' => 'Arial',
                'isPhpEnabled' => false,
                'isJavascriptEnabled' => false,
                'debugKeepTemp' => false,
                'debugCss' => false,
                'debugLayout' => false,
                'debugLayoutLines' => false,
                'debugLayoutBlocks' => false,
                'debugLayoutInline' => false,
                'debugLayoutPaddingBox' => false,
                'fontDir' => storage_path('fonts'),
                'fontCache' => storage_path('fonts'),
                'tempDir' => sys_get_temp_dir(),
                'chroot' => realpath(base_path()),
                'logOutputFile' => null,
                'defaultMediaType' => 'screen',
                'defaultPaperSize' => 'a4',
                'defaultPaperOrientation' => 'portrait',
                'defaultFont' => 'Arial',
                'enable_font_subsetting' => true, // Habilitar subsetting para reduzir tamanho
                'isFontSubsettingEnabled' => true
            ]);
            
            \Log::info('Iniciando download do PDF');
            return $pdf->download("pedido_{$order->id}_costura.pdf");
            
        } catch (\Exception $e) {
            \Log::error('Erro ao gerar PDF de costura', [
                'order_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Erro ao gerar PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadPersonalizacao($id)
    {
        try {
            // Aumentar limite de memória para processamento de PDF
            ini_set('memory_limit', '1024M');
            
            \Log::info('Iniciando download de personalização para pedido: ' . $id);
            
            $order = Order::with(['client', 'items.sublimations.size', 'items.sublimations.location'])->findOrFail($id);
            \Log::info('Pedido carregado com sucesso', ['order_id' => $order->id, 'items_count' => $order->items->count()]);

            // Processar imagens dos itens com otimizações para evitar problemas de memória
            foreach ($order->items as $item) {
                $item->hasCoverImage = false;
                $item->coverImageInfo = null;
                $item->coverImageUrl = null;
                
                if ($item->cover_image) {
                    $coverImagePath = storage_path('app/public/' . $item->cover_image);
                    
                    // Verificar se o arquivo existe usando múltiplas abordagens
                    $fileExists = false;
                    $actualPath = null;
                    
                    // Tentar 1: Caminho direto
                    if (file_exists($coverImagePath)) {
                        $fileExists = true;
                        $actualPath = $coverImagePath;
                    } else {
                        // Tentar 2: Buscar arquivo com nome similar (para lidar com caracteres especiais)
                        $directory = dirname($coverImagePath);
                        $filename = basename($coverImagePath);
                        
                        if (is_dir($directory)) {
                            $files = scandir($directory);
                            foreach ($files as $file) {
                                if ($file !== '.' && $file !== '..') {
                                    // Verificar se o arquivo tem o mesmo prefixo (ID + timestamp)
                                    if (strpos($file, substr($filename, 0, 20)) === 0) {
                                        $actualPath = $directory . DIRECTORY_SEPARATOR . $file;
                                        $fileExists = true;
                                        \Log::info('Arquivo encontrado com nome similar (personalização)', [
                                            'original' => $filename,
                                            'encontrado' => $file,
                                            'path' => $actualPath
                                        ]);
                                        break;
                                    }
                                }
                            }
                        }
                    }
                    
                    if ($fileExists && $actualPath) {
                        // Verificar se o arquivo não é muito grande (limite de 1MB para economizar memória)
                        $fileSize = filesize($actualPath);
                        if ($fileSize > 1 * 1024 * 1024) {
                            // Tentar criar uma versão otimizada da imagem
                            $optimizedPath = $this->optimizeImageForPDF($actualPath);
                            if ($optimizedPath && file_exists($optimizedPath)) {
                                $actualPath = $optimizedPath;
                                $fileSize = filesize($actualPath);
                                \Log::info('Imagem personalização otimizada criada', [
                                    'original' => $actualPath,
                                    'otimizada' => $optimizedPath,
                                    'size' => $this->formatFileSize($fileSize)
                                ]);
                            } else {
                                \Log::warning('Imagem personalização muito grande e não foi possível otimizar, pulando', [
                                    'path' => $actualPath,
                                    'size' => $this->formatFileSize($fileSize)
                                ]);
                                continue;
                            }
                        }
                        
                        $item->hasCoverImage = true;
                        $item->coverImageInfo = [
                            'name' => basename($actualPath),
                            'size' => $this->formatFileSize($fileSize),
                            'extension' => strtoupper(pathinfo($actualPath, PATHINFO_EXTENSION)),
                            'path' => $item->cover_image
                        ];
                        
                        // Usar caminho local absoluto
                        $item->coverImageUrl = $actualPath;
                        
                        \Log::info('Imagem personalização processada!', [
                            'path' => $actualPath,
                            'size' => $item->coverImageInfo['size']
                        ]);
                    } else {
                        \Log::warning('Arquivo não existe (personalização):', [
                            'path' => $coverImagePath,
                            'directory' => dirname($coverImagePath),
                            'filename' => basename($coverImagePath)
                        ]);
                    }
                } else {
                    \Log::info('Item sem imagem de capa (personalização)');
                }
            }

            \Log::info('Iniciando renderização da view de personalização');
            $html = view('kanban.pdf.personalizacao', compact('order'))->render();
            \Log::info('View de personalização renderizada com sucesso', ['html_length' => strlen($html)]);
            
            // Limpar memória antes de criar o PDF
            if (function_exists('gc_collect_cycles')) {
                gc_collect_cycles();
            }
            
            \Log::info('Iniciando criação do PDF de personalização');
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'portrait');
            
            // Configuração otimizada do DomPDF para reduzir uso de memória
            $pdf->setOptions([
                'isRemoteEnabled' => false,
                'isHtml5ParserEnabled' => false, // Desabilitar para economizar memória
                'defaultFont' => 'Arial',
                'isPhpEnabled' => false,
                'isJavascriptEnabled' => false,
                'debugKeepTemp' => false,
                'debugCss' => false,
                'debugLayout' => false,
                'debugLayoutLines' => false,
                'debugLayoutBlocks' => false,
                'debugLayoutInline' => false,
                'debugLayoutPaddingBox' => false,
                'fontDir' => storage_path('fonts'),
                'fontCache' => storage_path('fonts'),
                'tempDir' => sys_get_temp_dir(),
                'chroot' => realpath(base_path()),
                'logOutputFile' => null,
                'defaultMediaType' => 'screen',
                'defaultPaperSize' => 'a4',
                'defaultPaperOrientation' => 'portrait',
                'defaultFont' => 'Arial',
                'enable_font_subsetting' => true, // Habilitar subsetting para reduzir tamanho
                'isFontSubsettingEnabled' => true
            ]);
            
            \Log::info('Iniciando download do PDF de personalização');
            return $pdf->download("pedido_{$order->id}_personalizacao.pdf");
            
        } catch (\Exception $e) {
            \Log::error('Erro ao gerar PDF de personalização', [
                'order_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Erro ao gerar PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Formatar tamanho do arquivo em formato legível
     */
    private function formatFileSize($bytes)
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 1) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Otimizar imagem para PDF reduzindo tamanho
     */
    private function optimizeImageForPDF($imagePath)
    {
        try {
            // Verificar se a extensão GD está disponível
            if (!extension_loaded('gd')) {
                \Log::warning('Extensão GD não disponível para otimização de imagem');
                return null;
            }

            // Criar diretório temporário se não existir
            $tempDir = storage_path('app/temp/optimized');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            // Nome do arquivo otimizado
            $filename = basename($imagePath);
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $optimizedPath = $tempDir . DIRECTORY_SEPARATOR . 'opt_' . $filename;

            // Carregar imagem original
            $image = null;
            switch ($extension) {
                case 'jpg':
                case 'jpeg':
                    $image = imagecreatefromjpeg($imagePath);
                    break;
                case 'png':
                    $image = imagecreatefrompng($imagePath);
                    break;
                default:
                    \Log::warning('Formato de imagem não suportado para otimização: ' . $extension);
                    return null;
            }

            if (!$image) {
                \Log::warning('Não foi possível carregar a imagem para otimização');
                return null;
            }

            // Obter dimensões originais
            $originalWidth = imagesx($image);
            $originalHeight = imagesy($image);

            // Calcular novas dimensões (máximo 800px de largura, mantendo proporção)
            $maxWidth = 800;
            $maxHeight = 600;
            
            if ($originalWidth > $maxWidth || $originalHeight > $maxHeight) {
                $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
                $newWidth = intval($originalWidth * $ratio);
                $newHeight = intval($originalHeight * $ratio);
            } else {
                $newWidth = $originalWidth;
                $newHeight = $originalHeight;
            }

            // Criar nova imagem redimensionada
            $optimizedImage = imagecreatetruecolor($newWidth, $newHeight);
            
            // Preservar transparência para PNG
            if ($extension === 'png') {
                imagealphablending($optimizedImage, false);
                imagesavealpha($optimizedImage, true);
                $transparent = imagecolorallocatealpha($optimizedImage, 255, 255, 255, 127);
                imagefill($optimizedImage, 0, 0, $transparent);
            }

            // Redimensionar imagem
            imagecopyresampled($optimizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

            // Salvar imagem otimizada
            $success = false;
            switch ($extension) {
                case 'jpg':
                case 'jpeg':
                    $success = imagejpeg($optimizedImage, $optimizedPath, 85); // Qualidade 85%
                    break;
                case 'png':
                    $success = imagepng($optimizedImage, $optimizedPath, 8); // Compressão 8
                    break;
            }

            // Limpar memória
            imagedestroy($image);
            imagedestroy($optimizedImage);

            if ($success && file_exists($optimizedPath)) {
                \Log::info('Imagem otimizada com sucesso', [
                    'original' => $imagePath,
                    'otimizada' => $optimizedPath,
                    'dimensões_originais' => $originalWidth . 'x' . $originalHeight,
                    'dimensões_otimizadas' => $newWidth . 'x' . $newHeight,
                    'tamanho_original' => $this->formatFileSize(filesize($imagePath)),
                    'tamanho_otimizado' => $this->formatFileSize(filesize($optimizedPath))
                ]);
                return $optimizedPath;
            } else {
                \Log::warning('Falha ao salvar imagem otimizada');
                return null;
            }

        } catch (\Exception $e) {
            \Log::error('Erro ao otimizar imagem', [
                'path' => $imagePath,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function downloadFiles($id)
    {
        $order = Order::with('items.sublimations.files')->findOrFail($id);
        
        // Coletar todos os arquivos de todas as personalizações de todos os itens
        $allFiles = collect();
        foreach ($order->items as $item) {
            foreach ($item->sublimations as $sublimation) {
                if ($sublimation->files) {
                    $allFiles = $allFiles->merge($sublimation->files);
                }
            }
        }

        if ($allFiles->isEmpty()) {
            return back()->with('error', 'Nenhum arquivo encontrado para este pedido.');
        }

        // Se for apenas um arquivo, fazer download direto
        if ($allFiles->count() === 1) {
            $file = $allFiles->first();
            $filePath = storage_path('app/public/' . $file->file_path);
            
            if (file_exists($filePath)) {
                return response()->download($filePath, $file->file_name);
            } else {
                return back()->with('error', 'Arquivo não encontrado.');
            }
        }

        // Se forem múltiplos arquivos, criar um ZIP
        $zipFileName = "pedido_{$order->id}_arquivos_arte.zip";
        $zipPath = storage_path("app/temp/{$zipFileName}");
        
        // Criar diretório temp se não existir
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $zip = new \ZipArchive;
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            foreach ($allFiles as $file) {
                $filePath = storage_path('app/public/' . $file->file_path);
                
                if (file_exists($filePath)) {
                    $zip->addFile($filePath, $file->file_name);
                }
            }
            $zip->close();
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function addPayment(Request $request, $id)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:dinheiro,pix,cartao,transferencia,boleto',
            'payment_date' => 'required|date',
        ]);

        $order = Order::with('client')->findOrFail($id);
        $payment = Payment::where('order_id', $order->id)->firstOrFail();

        // Verificar se o valor não excede o restante
        if ($validated['amount'] > $payment->remaining_amount) {
            return response()->json([
                'success' => false,
                'message' => 'O valor informado excede o valor restante.'
            ], 400);
        }

        // Calcular novos totais
        $currentPaid = $payment->entry_amount;
        $newEntryAmount = $currentPaid + $validated['amount'];
        $newRemainingAmount = $payment->remaining_amount - $validated['amount'];

        // Adicionar novo método de pagamento ao array
        $paymentMethods = $payment->payment_methods ?? [];
        $paymentMethods[] = [
            'id' => time() . rand(1000, 9999),
            'method' => $validated['payment_method'],
            'amount' => $validated['amount']
        ];

        // Atualizar o pagamento existente
        $payment->update([
            'entry_amount' => $newEntryAmount,
            'remaining_amount' => $newRemainingAmount,
            'payment_methods' => $paymentMethods,
            'status' => $newRemainingAmount <= 0 ? 'pago' : 'pendente',
        ]);

        // Registrar no caixa
        $user = Auth::user();
        \App\Models\CashTransaction::create([
            'type' => 'entrada',
            'category' => 'Venda',
            'description' => "Pagamento Adicional do Pedido #" . str_pad($order->id, 6, '0', STR_PAD_LEFT) . " - Cliente: " . $order->client->name,
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'transaction_date' => $validated['payment_date'],
            'order_id' => $order->id,
            'user_id' => $user->id ?? null,
            'user_name' => $user->name ?? 'Sistema',
            'notes' => 'Pagamento adicional registrado via Kanban',
        ]);

        // Criar log
        \App\Models\OrderLog::create([
            'order_id' => $order->id,
            'user_id' => $user->id ?? null,
            'user_name' => $user->name ?? 'Sistema',
            'action' => 'payment_added',
            'description' => "Pagamento adicional de R$ " . number_format($validated['amount'], 2, ',', '.') . " registrado via " . $validated['payment_method'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pagamento registrado com sucesso!'
        ]);
    }
}
