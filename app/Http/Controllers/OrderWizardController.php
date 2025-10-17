<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Status;
use App\Models\CashTransaction;
use App\Helpers\DateHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OrderWizardController extends Controller
{
    public function start(Request $request): View
    {
        return view('orders.wizard.client');
    }

    public function storeClient(Request $request): RedirectResponse
    {
        \Log::info('=== STORE CLIENT DEBUG START ===');
        \Log::info('Request method:', ['method' => $request->method()]);
        \Log::info('Request data:', $request->all());
        \Log::info('User authenticated:', ['auth' => auth()->check(), 'user_id' => auth()->id()]);
        
        try {
            $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'name' => 'required|string|max:255',
            'phone_primary' => 'required|string|max:50',
            'phone_secondary' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'cpf_cnpj' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'zip_code' => 'nullable|string|max:12',
            'category' => 'nullable|string|max:50',
        ]);

        // Se client_id foi enviado, atualiza o cliente existente
        if (!empty($validated['client_id'])) {
            $client = Client::findOrFail($validated['client_id']);
            $client->update($validated);
        } else {
            // Senão, cria um novo cliente
            $client = Client::create($validated);
        }

        $status = Status::orderBy('position')->first();

        // Calcular data de entrega (15 dias úteis)
        $deliveryDate = DateHelper::calculateDeliveryDate(Carbon::now(), 15);

        $order = Order::create([
            'client_id' => $client->id,
            'user_id' => Auth::id(),
            'status_id' => $status?->id ?? 1,
            'order_date' => now()->toDateString(),
            'delivery_date' => $deliveryDate->toDateString(),
            'is_draft' => true, // Criar como rascunho
        ]);

        session(['current_order_id' => $order->id]);

        \Log::info('=== STORE CLIENT DEBUG END - SUCCESS ===');
        return redirect()->route('orders.wizard.sewing');
        
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('=== VALIDATION ERROR ===', ['errors' => $e->errors()]);
            throw $e;
        } catch (\Exception $e) {
            \Log::error('=== GENERAL ERROR ===', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            throw $e;
        }
    }

    public function sewing(Request $request)
    {
        if ($request->isMethod('get')) {
            $order = Order::with('items')->findOrFail(session('current_order_id'));
            return view('orders.wizard.sewing', compact('order'));
        }

        $action = $request->input('action', 'add');

        if ($action === 'add_item') {
            return $this->addItem($request);
        } elseif ($action === 'update_item') {
            return $this->updateItem($request);
        } elseif ($action === 'finish') {
            return $this->finishSewing($request);
        } elseif ($action === 'delete_item') {
            return $this->deleteItem($request);
        }

        return $this->addItem($request);
    }

    private function addItem(Request $request)
    {
        $validated = $request->validate([
            'personalizacao' => 'required|array|min:1',
            'personalizacao.*' => 'exists:product_options,id',
            'tecido' => 'required|exists:product_options,id',
            'tipo_tecido' => 'nullable|exists:product_options,id',
            'cor' => 'required|exists:product_options,id',
            'tipo_corte' => 'required|exists:product_options,id',
            'detalhe' => 'nullable|exists:product_options,id',
            'gola' => 'required|exists:product_options,id',
            'tamanhos' => 'required|array',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'item_cover_image' => 'nullable|image|max:10240',
            'art_notes' => 'nullable|string|max:1000',
        ]);

        $order = Order::with('items')->findOrFail(session('current_order_id'));

        // Processar upload da imagem de capa do item
        $coverImagePath = null;
        if ($request->hasFile('item_cover_image')) {
            $coverImage = $request->file('item_cover_image');
            $coverImageName = time() . '_' . uniqid() . '_' . $coverImage->getClientOriginalName();
            $coverImagePath = $coverImage->storeAs('orders/items', $coverImageName, 'public');
        }

        // Buscar nomes das opções
        $personalizacoes = \App\Models\ProductOption::whereIn('id', $validated['personalizacao'])->get();
        $personalizacaoNames = $personalizacoes->pluck('name')->join(', ');
        
        $tecido = \App\Models\ProductOption::find($validated['tecido']);
        $cor = \App\Models\ProductOption::find($validated['cor']);
        $tipoCorte = \App\Models\ProductOption::find($validated['tipo_corte']);
        $gola = \App\Models\ProductOption::find($validated['gola']);
        $detalhe = !empty($validated['detalhe']) ? \App\Models\ProductOption::find($validated['detalhe']) : null;
        $tipoTecido = !empty($validated['tipo_tecido']) ? \App\Models\ProductOption::find($validated['tipo_tecido']) : null;

        $itemNumber = $order->items()->count() + 1;

        $item = new OrderItem([
            'item_number' => $itemNumber,
            'fabric' => $tecido->name . ($tipoTecido ? ' - ' . $tipoTecido->name : ''),
            'color' => $cor->name,
            'collar' => $gola->name,
            'model' => $tipoCorte->name,
            'detail' => $detalhe ? $detalhe->name : null,
            'print_type' => $personalizacaoNames,
            'sizes' => $validated['tamanhos'],
            'quantity' => $validated['quantity'],
            'unit_price' => $validated['unit_price'],
            'total_price' => $validated['unit_price'] * $validated['quantity'],
            'cover_image' => $coverImagePath,
            'art_notes' => $validated['art_notes'] ?? null,
        ]);
        $order->items()->save($item);

        $order->update([
            'subtotal' => $order->items()->sum('total_price'),
            'total_items' => $order->items()->sum('quantity'),
        ]);

        // Salvar IDs de personalização vinculadas a este item
        session()->push('item_personalizations.' . $item->id, $validated['personalizacao']);

        return redirect()->route('orders.wizard.sewing')->with('success', 'Item ' . $itemNumber . ' adicionado com sucesso!');
    }

    private function updateItem(Request $request)
    {
        $validated = $request->validate([
            'editing_item_id' => 'required|integer',
            'personalizacao' => 'required|array|min:1',
            'personalizacao.*' => 'exists:product_options,id',
            'tecido' => 'required|exists:product_options,id',
            'tipo_tecido' => 'nullable|exists:product_options,id',
            'cor' => 'required|exists:product_options,id',
            'tipo_corte' => 'required|exists:product_options,id',
            'detalhe' => 'nullable|exists:product_options,id',
            'gola' => 'required|exists:product_options,id',
            'tamanhos' => 'required|array',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'item_cover_image' => 'nullable|image|max:10240',
            'art_notes' => 'nullable|string|max:1000',
        ]);

        $order = Order::with('items')->findOrFail(session('current_order_id'));
        $item = $order->items()->findOrFail($validated['editing_item_id']);

        // Processar upload da imagem de capa do item
        $coverImagePath = $item->cover_image; // Manter imagem atual se não houver nova
        if ($request->hasFile('item_cover_image')) {
            $coverImage = $request->file('item_cover_image');
            $coverImageName = time() . '_' . uniqid() . '_' . $coverImage->getClientOriginalName();
            $coverImagePath = $coverImage->storeAs('orders/items', $coverImageName, 'public');
        }

        // Buscar nomes das opções
        $personalizacoes = \App\Models\ProductOption::whereIn('id', $validated['personalizacao'])->get();
        $personalizacaoNames = $personalizacoes->pluck('name')->join(', ');

        $tecido = \App\Models\ProductOption::find($validated['tecido']);
        $tipoTecido = $validated['tipo_tecido'] ? \App\Models\ProductOption::find($validated['tipo_tecido']) : null;
        $cor = \App\Models\ProductOption::find($validated['cor']);
        $tipoCorte = \App\Models\ProductOption::find($validated['tipo_corte']);
        $detalhe = $validated['detalhe'] ? \App\Models\ProductOption::find($validated['detalhe']) : null;
        $gola = \App\Models\ProductOption::find($validated['gola']);

        // Calcular preço base
        $basePrice = $tipoCorte->price ?? 0;
        if ($detalhe) {
            $basePrice += $detalhe->price ?? 0;
        }
        if ($gola) {
            $basePrice += $gola->price ?? 0;
        }

        // Processar tamanhos
        $sizes = [];
        $totalQuantity = 0;
        foreach ($validated['tamanhos'] as $size => $quantity) {
            if ($quantity > 0) {
                $sizes[$size] = $quantity;
                $totalQuantity += $quantity;
            }
        }

        // Atualizar item
        $item->update([
            'print_type' => $personalizacaoNames,
            'fabric' => $tecido->name . ($tipoTecido ? ' - ' . $tipoTecido->name : ''),
            'color' => $cor->name,
            'collar' => $gola->name,
            'model' => $tipoCorte->name,
            'detail' => $detalhe ? $detalhe->name : null,
            'sizes' => json_encode($sizes),
            'quantity' => $totalQuantity,
            'unit_price' => $basePrice,
            'total_price' => $totalQuantity * $basePrice,
            'cover_image' => $coverImagePath,
            'art_notes' => $validated['art_notes'] ?? null,
        ]);

        $order->update([
            'subtotal' => $order->items()->sum('total_price'),
            'total_items' => $order->items()->sum('quantity'),
        ]);

        // Atualizar personalizações na sessão
        session(['item_personalizations.' . $item->id => [$validated['personalizacao']]]);

        \Log::info('Item atualizado com sucesso', [
            'item_id' => $item->id,
            'order_id' => $order->id,
            'new_quantity' => $totalQuantity,
            'new_unit_price' => $basePrice
        ]);

        return redirect()->route('orders.wizard.sewing')->with('success', 'Item atualizado com sucesso!');
    }

    private function deleteItem(Request $request)
    {
        $itemId = $request->input('item_id');
        $order = Order::with('items')->findOrFail(session('current_order_id'));
        
        $item = $order->items()->findOrFail($itemId);
        $item->delete();

        // Renumerar itens
        $items = $order->items()->orderBy('id')->get();
        foreach ($items as $index => $it) {
            $it->update(['item_number' => $index + 1]);
        }

        $order->update([
            'subtotal' => $order->items()->sum('total_price'),
            'total_items' => $order->items()->sum('quantity'),
        ]);

        return redirect()->route('orders.wizard.sewing')->with('success', 'Item removido com sucesso!');
    }

    private function finishSewing(Request $request)
    {
        $order = Order::with('items')->findOrFail(session('current_order_id'));
        
        if ($order->items()->count() === 0) {
            return redirect()->route('orders.wizard.sewing')->with('error', 'Adicione pelo menos um item antes de continuar.');
        }

        // Coletar todas as personalizações únicas de todos os itens
        $allPersonalizations = [];
        foreach ($order->items as $item) {
            $itemPersonalizations = session('item_personalizations.' . $item->id, [[]]);
            $allPersonalizations = array_merge($allPersonalizations, $itemPersonalizations[0] ?? []);
        }
        $allPersonalizations = array_unique($allPersonalizations);

        // Salvar total de camisas na sessão para usar na personalização
        session(['total_shirts' => $order->items()->sum('quantity')]);
        
        // Salvar personalizações selecionadas na sessão
        session(['selected_personalizations' => $allPersonalizations]);

        return redirect()->route('orders.wizard.customization');
    }

    public function customization(Request $request)
    {
        if ($request->isMethod('get')) {
            $order = Order::with('items')->findOrFail(session('current_order_id'));
            
            // Coletar personalizações por item
            $itemPersonalizations = [];
            foreach ($order->items as $item) {
                $itemPers = session('item_personalizations.' . $item->id, [[]]);
                $persIds = $itemPers[0] ?? [];
                
                if (!empty($persIds)) {
                    $persNames = \App\Models\ProductOption::whereIn('id', $persIds)
                        ->pluck('name', 'id')
                        ->toArray();
                    
                    $itemPersonalizations[$item->id] = [
                        'item' => $item,
                        'personalization_ids' => $persIds,
                        'personalization_names' => $persNames,
                    ];
                }
            }
            
            // Buscar tamanhos e localizações para cada tipo de personalização
            $personalizationData = [];
            $allTypes = ['DTF', 'SERIGRAFIA', 'BORDADO', 'SUBLIMACAO'];
            
            foreach ($allTypes as $type) {
                $sizes = \App\Models\PersonalizationPrice::where('personalization_type', $type)
                    ->where('active', true)
                    ->select('size_name', 'size_dimensions')
                    ->distinct()
                    ->orderBy('order')
                    ->get();
                
                $personalizationData[$type] = [
                    'sizes' => $sizes
                ];
            }
            
            // Localizações disponíveis
            $locations = \App\Models\SublimationLocation::where('active', true)
                ->orderBy('order')
                ->get();
            
            // Usar view unificada que mostra todas as personalizações
            return view('orders.wizard.customization-multiple', compact('order', 'itemPersonalizations', 'personalizationData', 'locations'));
        }

        // Validação dos campos do formulário
        $validated = $request->validate([
            'item_id' => 'required|exists:order_items,id',
            'personalization_type' => 'required|string',
            'personalization_id' => 'required|integer',
            'art_name' => 'required|string|max:255',
            'location' => 'required',
            'size' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'color_count' => 'nullable|integer|min:1',
            'unit_price' => 'nullable|numeric|min:0',
            'final_price' => 'nullable|numeric|min:0',
            'application_image' => 'nullable|image|max:10240',
            'art_files' => 'required|array|min:1',
            'art_files.*' => 'required|file|max:51200', // Máximo 50MB por arquivo
            'color_details' => 'nullable|string|max:500',
            'seller_notes' => 'nullable|string|max:1000',
        ]);

        $order = Order::with('items')->findOrFail(session('current_order_id'));
        $item = $order->items()->findOrFail($validated['item_id']);

        // Processar imagem da aplicação
        $applicationImagePath = null;
        if ($request->hasFile('application_image')) {
            $appImage = $request->file('application_image');
            $appImageName = time() . '_' . uniqid() . '_' . $appImage->getClientOriginalName();
            $applicationImagePath = $appImage->storeAs('orders/applications', $appImageName, 'public');
        }

        // Buscar localização
        $location = \App\Models\SublimationLocation::find($validated['location']);
        $locationId = $location ? $location->id : null;
        $locationName = $location ? $location->name : $validated['location'];

        // Criar a personalização
        $personalization = \App\Models\OrderSublimation::create([
            'order_item_id' => $item->id,
            'application_type' => strtolower($validated['personalization_type']),
            'art_name' => $validated['art_name'] ?? null,
            'size_id' => null,
            'size_name' => $validated['size'],
            'location_id' => $locationId,
            'location_name' => $locationName,
            'quantity' => $validated['quantity'],
            'color_count' => $validated['color_count'] ?? 0,
            'has_neon' => false,
            'neon_surcharge' => 0,
            'unit_price' => $validated['unit_price'] ?? 0,
            'discount_percent' => 0,
            'final_price' => $validated['final_price'] ?? 0,
            'application_image' => $applicationImagePath,
            'color_details' => $validated['color_details'] ?? null,
            'seller_notes' => $validated['seller_notes'] ?? null,
        ]);

        // Processar arquivos da arte (CDR, PDF, etc.)
        if ($request->hasFile('art_files')) {
            foreach ($request->file('art_files') as $file) {
                $originalName = $file->getClientOriginalName();
                $fileName = time() . '_' . uniqid() . '_' . $originalName;
                $filePath = $file->storeAs('orders/art_files', $fileName, 'public');
                
                \App\Models\OrderSublimationFile::create([
                    'order_sublimation_id' => $personalization->id,
                    'file_name' => $originalName,
                    'file_path' => $filePath,
                    'file_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        // Atualizar o preço total do item somando todas as personalizações
        $totalPersonalizations = \App\Models\OrderSublimation::where('order_item_id', $item->id)
            ->sum('final_price');
        
        // Calcular novo total do item (preço base + personalizações)
        $basePrice = $item->unit_price * $item->quantity;
        $newTotalPrice = $basePrice + $totalPersonalizations;
        
        $item->update([
            'total_price' => $newTotalPrice
        ]);

        // Atualizar subtotal do pedido
        $order->update([
            'subtotal' => $order->items()->sum('total_price'),
        ]);

        // Retornar resposta JSON
        return response()->json([
            'success' => true,
            'message' => 'Personalização adicionada com sucesso!'
        ]);
    }

    public function payment(Request $request)
    {
        if ($request->isMethod('get')) {
            $order = Order::with('client', 'items')->findOrFail(session('current_order_id'));
            return view('orders.wizard.payment', compact('order'));
        }

        $validated = $request->validate([
            'entry_date' => 'required|date',
            'delivery_fee' => 'nullable|numeric|min:0',
            'payment_methods' => 'required|json',
            'size_surcharges' => 'nullable|json',
            'order_cover_image' => 'nullable|image|max:10240',
        ]);

        $order = Order::with('items')->findOrFail(session('current_order_id'));
        
        // Processar upload da imagem de capa do pedido
        $orderCoverImagePath = null;
        if ($request->hasFile('order_cover_image')) {
            $orderCoverImage = $request->file('order_cover_image');
            $orderCoverImageName = time() . '_' . uniqid() . '_order_' . $orderCoverImage->getClientOriginalName();
            $orderCoverImagePath = $orderCoverImage->storeAs('orders/covers', $orderCoverImageName, 'public');
        }
        
        $subtotal = $order->items()->sum('total_price');
        $delivery = (float)($validated['delivery_fee'] ?? 0);
        
        // Processar acréscimos por tamanho
        $sizeSurcharges = json_decode($validated['size_surcharges'] ?? '{}', true);
        $totalSurcharges = array_sum($sizeSurcharges);
        
        // Processar múltiplas formas de pagamento
        $paymentMethods = json_decode($validated['payment_methods'], true);
        $totalPaid = array_sum(array_column($paymentMethods, 'amount'));
        
        $total = $subtotal + $totalSurcharges + $delivery;

        $order->update([
            'delivery_fee' => $delivery,
            'total' => $total,
            'cover_image' => $orderCoverImagePath,
        ]);

        // Criar registro de pagamento
        // Se houver apenas um método, usar ele; senão, usar o primeiro como padrão
        $primaryMethod = count($paymentMethods) === 1 ? $paymentMethods[0]['method'] : 'pix';
        
        Payment::create([
            'order_id' => $order->id,
            'method' => $primaryMethod, // Método principal (obrigatório)
            'payment_method' => count($paymentMethods) > 1 ? 'multiplo' : $primaryMethod,
            'payment_methods' => $paymentMethods, // JSON com todos os métodos
            'amount' => $total,
            'entry_amount' => $totalPaid,
            'remaining_amount' => max(0, $total - $totalPaid),
            'entry_date' => $validated['entry_date'],
            'payment_date' => $validated['entry_date'],
            'status' => $totalPaid >= $total ? 'pago' : 'pendente',
        ]);

        // Registrar entrada(s) no caixa para cada forma de pagamento
        $user = Auth::user();
        foreach ($paymentMethods as $method) {
            CashTransaction::create([
                'type' => 'entrada',
                'category' => 'Venda',
                'description' => "Pagamento do Pedido #" . str_pad($order->id, 6, '0', STR_PAD_LEFT) . " - Cliente: " . $order->client->name,
                'amount' => $method['amount'],
                'payment_method' => $method['method'],
                'transaction_date' => $validated['entry_date'],
                'order_id' => $order->id,
                'user_id' => $user->id ?? null,
                'user_name' => $user->name ?? 'Sistema',
                'notes' => count($paymentMethods) > 1 ? 'Pagamento parcial (múltiplas formas)' : null,
            ]);
        }

        // Salvar acréscimos na sessão para exibir no resumo
        session(['size_surcharges' => $sizeSurcharges]);

        return redirect()->route('orders.wizard.confirm');
    }

    public function confirm(): View
    {
        $order = Order::with(['client', 'items.sublimations.size', 'items.sublimations.location', 'items.files', 'status'])
            ->findOrFail(session('current_order_id'));
        
        $payment = Payment::where('order_id', $order->id)->first();
        $sizeSurcharges = session('size_surcharges', []);
        
        return view('orders.wizard.confirm', compact('order', 'payment', 'sizeSurcharges'));
    }

    public function finalize(Request $request): RedirectResponse
    {
        try {
            $orderId = session('current_order_id');
            
            if (!$orderId) {
                return redirect()->route('orders.wizard.start')->with('error', 'Sessão expirada. Por favor, inicie um novo pedido.');
            }
            
            $order = Order::findOrFail($orderId);
            
            // Processar imagem de capa se fornecida
            $coverImagePath = null;
            if ($request->hasFile('order_cover_image')) {
                $coverImage = $request->file('order_cover_image');
                $coverImageName = time() . '_' . uniqid() . '_' . $coverImage->getClientOriginalName();
                $coverImagePath = $coverImage->storeAs('orders/covers', $coverImageName, 'public');
            }
            
            // Confirmar o pedido (tirar do modo rascunho) e atualizar imagem de capa
            $updateData = ['is_draft' => false];
            if ($coverImagePath) {
                $updateData['cover_image'] = $coverImagePath;
            }
            
            // Processar checkbox de evento
            if ($request->has('is_event') && $request->input('is_event') == '1') {
                $updateData['contract_type'] = 'EVENTO';
            }
            
            $order->update($updateData);
            
            // Criar log de confirmação
            \App\Models\OrderLog::create([
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name ?? 'Sistema',
                'action' => 'PEDIDO_CONFIRMADO',
                'description' => 'Pedido confirmado e enviado para produção.',
            ]);
            
            // Limpar sessão
            session()->forget(['current_order_id', 'item_personalizations', 'size_surcharges']);
            
            return redirect()->route('kanban.index')->with('success', 'Pedido #' . str_pad($order->id, 6, '0', STR_PAD_LEFT) . ' confirmado com sucesso!');
            
        } catch (\Exception $e) {
            \Log::error('Erro ao finalizar pedido: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao confirmar pedido. Tente novamente.');
        }
    }

    public function deletePersonalization($id)
    {
        try {
            $personalization = \App\Models\OrderSublimation::findOrFail($id);
            $itemId = $personalization->order_item_id;
            $personalization->delete();
            
            // Recalcular total do item
            $item = \App\Models\OrderItem::findOrFail($itemId);
            $totalPersonalizations = \App\Models\OrderSublimation::where('order_item_id', $itemId)
                ->sum('final_price');
            
            $basePrice = $item->unit_price * $item->quantity;
            $newTotalPrice = $basePrice + $totalPersonalizations;
            
            $item->update([
                'total_price' => $newTotalPrice
            ]);
            
            // Recalcular subtotal do pedido
            $order = $item->order;
            $order->update([
                'subtotal' => $order->items()->sum('total_price'),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Personalização removida com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover personalização'
            ], 500);
        }
    }
}
