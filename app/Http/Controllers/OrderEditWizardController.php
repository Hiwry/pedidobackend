<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Client;
use App\Models\OrderItem;
use App\Models\OrderLog;
use App\Models\OrderEditHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderEditWizardController extends Controller
{
    public function start(Order $order)
    {
        // Verificar se o pedido pode ser editado
        if ($order->is_cancelled) {
            return redirect()->back()->with('error', 'Pedidos cancelados não podem ser editados.');
        }

        // Verificar se já existe uma solicitação pendente
        if ($order->has_pending_edit) {
            return redirect()->back()->with('error', 'Já existe uma solicitação de edição pendente para este pedido.');
        }

        // Limpar sessão anterior se existir
        session()->forget('edit_order_data');
        session()->forget('edit_order_id');

        // Inicializar dados da edição
        $items = $order->items->map(function($item) {
            $itemArray = $item->toArray();
            $itemArray['id'] = $item->id; // Garantir que o ID está presente
            return $itemArray;
        })->toArray();

        session([
            'edit_order_id' => $order->id,
            'edit_order_data' => [
                'client' => $order->client->toArray(),
                'items' => $items,
                'contract_type' => $order->contract_type,
                'seller' => $order->seller,
                'delivery_date' => $order->delivery_date,
                'subtotal' => $order->subtotal,
                'discount' => $order->discount,
                'delivery_fee' => $order->delivery_fee,
                'total' => $order->total,
                'notes' => $order->notes,
            ]
        ]);

        return redirect()->route('orders.edit-wizard.client');
    }

    public function client(Request $request)
    {
        $editData = session('edit_order_data', []);
        $orderId = session('edit_order_id');

        if (!$orderId) {
            return redirect()->route('orders.index')->with('error', 'Sessão de edição expirada.');
        }

        $order = Order::with('client')->findOrFail($orderId);

        if ($request->isMethod('post')) {
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

            // Atualizar dados do cliente na sessão
            $editData['client'] = $validated;
            session(['edit_order_data' => $editData]);

            // Log da edição
            $this->logEdit('client', 'Dados do cliente atualizados', $validated);

            return redirect()->route('orders.edit-wizard.sewing');
        }

        return view('orders.edit-wizard.client', compact('order', 'editData'));
    }

    public function sewing(Request $request)
    {
        $editData = session('edit_order_data', []);
        $orderId = session('edit_order_id');

        if (!$orderId) {
            return redirect()->route('orders.index')->with('error', 'Sessão de edição expirada.');
        }

        $order = Order::with('items')->findOrFail($orderId);
        
        // Recarregar os dados do pedido para garantir que estão atualizados
        $order->refresh();
        $order->load('items');

        if ($request->isMethod('post')) {
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

        return view('orders.edit-wizard.sewing', compact('order', 'editData'));
    }

    public function customization(Request $request)
    {
        $editData = session('edit_order_data', []);
        $orderId = session('edit_order_id');

        if (!$orderId) {
            return redirect()->route('orders.index')->with('error', 'Sessão de edição expirada.');
        }

        $order = Order::with(['items.sublimations', 'items.files'])->findOrFail($orderId);
        
        // Carregar dados de personalização existentes se não estiverem na sessão
        if (!isset($editData['personalization'])) {
            $editData['personalization'] = [
                'art_name' => $order->items->first()->art_name ?? '',
                'sublimations' => []
            ];
            
            // Carregar sublimações de todos os itens
            foreach ($order->items as $item) {
                foreach ($item->sublimations as $sublimation) {
                    $editData['personalization']['sublimations'][] = [
                        'item_id' => $item->id,
                        'size_name' => $sublimation->size_name,
                        'location_name' => $sublimation->location_name,
                        'application_size' => $sublimation->application_size ?? '',
                        'color_quantity' => $sublimation->color_quantity ?? 1,
                        'quantity' => $sublimation->quantity,
                        'unit_price' => $sublimation->unit_price,
                        'subtotal' => $sublimation->final_price,
                    ];
                }
            }
            
            // Log para debug
            \Log::info('Sublimações carregadas:', $editData['personalization']['sublimations']);
            
            session(['edit_order_data' => $editData]);
        }

        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'art_name' => 'required|string|max:255',
                'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'art_files' => 'nullable|array',
                'art_files.*' => 'file|mimes:pdf,jpg,jpeg,png,ai,cdr|max:10240',
                'sublimations' => 'required|string',
            ]);

            // Processar dados de personalização
            $sublimations = json_decode($validated['sublimations'], true);
            
            $editData['personalization'] = [
                'art_name' => $validated['art_name'],
                'sublimations' => $sublimations,
            ];

            session(['edit_order_data' => $editData]);

            // Log da edição
            $this->logEdit('personalization', 'Personalização atualizada', $validated);

            return redirect()->route('orders.edit-wizard.payment');
        }

        // Buscar dados para os selects da tabela personalization_prices para SERIGRAFIA
        $sizes = \App\Models\PersonalizationPrice::getSizesForType('SERIGRAFIA');
        $sublimationLocations = \App\Models\SublimationLocation::where('active', true)->orderBy('order')->get();
        
        return view('orders.edit-wizard.customization', compact('order', 'editData', 'sizes', 'sublimationLocations'));
    }

    public function payment(Request $request)
    {
        $editData = session('edit_order_data', []);
        $orderId = session('edit_order_id');

        if (!$orderId) {
            return redirect()->route('orders.index')->with('error', 'Sessão de edição expirada.');
        }

        $order = Order::with(['client', 'items'])->findOrFail($orderId);

        if ($request->isMethod('post')) {
            \Log::info('=== PROCESSANDO PAGAMENTO ===');
            \Log::info('Dados recebidos:', $request->all());
            
            $validated = $request->validate([
                'delivery_date' => 'required|date',
                'subtotal' => 'required|numeric|min:0',
                'discount' => 'nullable|numeric|min:0',
                'delivery_fee' => 'nullable|numeric|min:0',
                'total' => 'required|numeric|min:0',
                'payment_method' => 'required|string',
                'entry_date' => 'required|date',
                'notes' => 'nullable|string|max:1000',
            ]);

            \Log::info('Dados validados:', $validated);

            // Atualizar dados de pagamento na sessão
            $editData['payment'] = $validated;
            session(['edit_order_data' => $editData]);

            \Log::info('Dados salvos na sessão:', $editData['payment']);

            // Log da edição
            $this->logEdit('payment', 'Dados de pagamento atualizados', $validated);

            return redirect()->route('orders.edit-wizard.confirm')->with('success', 'Dados de pagamento atualizados com sucesso!');
        }

        return view('orders.edit-wizard.payment', compact('order', 'editData'));
    }

    public function confirm(Request $request)
    {
        \Log::info('=== ETAPA DE CONFIRMAÇÃO ===');
        \Log::info('Método da requisição:', $request->method());
        \Log::info('Dados da sessão:', session('edit_order_data', []));
        
        $editData = session('edit_order_data', []);
        $orderId = session('edit_order_id');

        if (!$orderId) {
            return redirect()->route('orders.index')->with('error', 'Sessão de edição expirada.');
        }

        $order = Order::with(['client', 'items', 'payments'])->findOrFail($orderId);

        if ($request->isMethod('post')) {
            \Log::info('POST recebido na confirmação, chamando finalizeEdit...');
            return $this->finalizeEdit($request);
        }

        \Log::info('Exibindo página de confirmação...');
        return view('orders.edit-wizard.confirm', compact('order', 'editData'));
    }

    public function finalizeEdit(Request $request)
    {
        \Log::info('=== FINALIZANDO EDIÇÃO ===');
        \Log::info('Dados da sessão:', session('edit_order_data', []));
        
        $editData = session('edit_order_data', []);
        $orderId = session('edit_order_id');

        if (!$orderId) {
            return redirect()->route('orders.index')->with('error', 'Sessão de edição expirada.');
        }

        $order = Order::findOrFail($orderId);
        \Log::info('Pedido encontrado:', $order->toArray());

        try {
            DB::beginTransaction();
            \Log::info('Transação iniciada...');

            // Aplicar todas as alterações
            \Log::info('Aplicando mudanças do cliente...');
            $this->applyClientChanges($order, $editData['client'] ?? []);
            
            \Log::info('Aplicando mudanças dos itens...');
            $this->applyItemsChanges($order, $editData['items'] ?? []);
            
            \Log::info('Aplicando mudanças da personalização...');
            $this->applyPersonalizationChanges($order, $editData['personalization'] ?? []);
            
            \Log::info('Aplicando mudanças do pagamento...');
            $this->applyPaymentChanges($order, $editData['payment'] ?? []);

            // Marcar pedido como editado
            \Log::info('Marcando pedido como editado...');
                $order->update([
                'is_editing' => false,
                'edit_status' => 'completed',
                'edited_at' => now(),
                'edited_by' => Auth::id(),
                ]);

            // Log final da edição
            $this->logEdit('finalize', 'Edição finalizada com sucesso', [
                    'order_id' => $order->id,
                'changes_applied' => array_keys($editData)
            ]);

            // Limpar sessão
            session()->forget(['edit_order_data', 'edit_order_id']);
            \Log::info('Sessão limpa...');

            DB::commit();
            \Log::info('Transação commitada com sucesso!');

            return redirect()->route('orders.show', $order->id)
                ->with('success', 'Pedido editado com sucesso! Todas as alterações foram aplicadas.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erro ao finalizar edição do pedido: ' . $e->getMessage());
            \Log::error('Stack trace:', $e->getTraceAsString());
            
            return redirect()->back()
                ->with('error', 'Erro ao finalizar edição. Tente novamente.');
        }
    }

    private function applyClientChanges(Order $order, array $clientData)
    {
        if (empty($clientData)) return;

        $client = $order->client;
        $oldData = $client->toArray();
        
        $client->update($clientData);
        
        // Log das mudanças do cliente
        $changes = $this->getChanges($oldData, $clientData);
        if (!empty($changes)) {
            $this->logEdit('client_changes', 'Dados do cliente alterados', $changes);
        }
    }

    private function applyItemsChanges(Order $order, array $itemsData)
    {
        if (empty($itemsData)) return;

        foreach ($itemsData as $itemData) {
            if (isset($itemData['id'])) {
                $item = OrderItem::find($itemData['id']);
                if ($item) {
                    $oldData = $item->toArray();
                    $item->update($itemData);
                    
                    // Log das mudanças do item
                    $changes = $this->getChanges($oldData, $itemData);
                    if (!empty($changes)) {
                        $this->logEdit('item_changes', "Item {$item->item_number} alterado", $changes);
                    }
                }
            }
        }
    }

    private function applyPersonalizationChanges(Order $order, array $personalizationData)
    {
        if (empty($personalizationData)) return;

        // Aqui você pode implementar a lógica específica para personalização
        // Por exemplo, atualizar sublimações, arquivos, etc.
        
        $this->logEdit('personalization_changes', 'Personalização alterada', $personalizationData);
    }

    private function applyPaymentChanges(Order $order, array $paymentData)
    {
        \Log::info('=== APLICANDO MUDANÇAS DE PAGAMENTO ===');
        \Log::info('Dados de pagamento recebidos:', $paymentData);
        
        if (empty($paymentData)) {
            \Log::info('Dados de pagamento vazios, retornando...');
            return;
        }

        $oldData = $order->toArray();
        \Log::info('Dados antigos do pedido:', $oldData);
        
        $updateData = [
            'delivery_date' => $paymentData['delivery_date'] ?? $order->delivery_date,
            'subtotal' => $paymentData['subtotal'] ?? $order->subtotal,
            'discount' => $paymentData['discount'] ?? $order->discount,
            'delivery_fee' => $paymentData['delivery_fee'] ?? $order->delivery_fee,
            'total' => $paymentData['total'] ?? $order->total,
            'notes' => $paymentData['notes'] ?? $order->notes,
        ];
        
        \Log::info('Dados para atualização:', $updateData);
        
        $order->update($updateData);
        \Log::info('Pedido atualizado com sucesso!');

        // Log das mudanças de pagamento
        $changes = $this->getChanges($oldData, $paymentData);
        if (!empty($changes)) {
            \Log::info('Mudanças detectadas:', $changes);
            $this->logEdit('payment_changes', 'Dados de pagamento alterados', $changes);
        } else {
            \Log::info('Nenhuma mudança detectada nos dados de pagamento.');
        }
    }

    private function getChanges(array $oldData, array $newData): array
    {
        $changes = [];
        
        foreach ($newData as $key => $newValue) {
            $oldValue = $oldData[$key] ?? null;
            
            if ($oldValue !== $newValue) {
                $changes[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue
                ];
            }
        }

        return $changes;
    }

    private function logEdit(string $action, string $description, array $data = [])
    {
        $orderId = session('edit_order_id');
        
        if (!$orderId) return;

        OrderEditHistory::create([
            'order_id' => $orderId,
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name,
            'action' => $action,
            'description' => $description,
            'changes' => $data,
        ]);

        OrderLog::create([
            'order_id' => $orderId,
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name,
            'action' => 'edit_' . $action,
            'description' => $description,
            'old_value' => null,
            'new_value' => $data
        ]);
    }

    private function addItem(Request $request)
    {
        // Implementar lógica de adicionar item (similar ao OrderWizardController)
        // Por enquanto, redirecionar de volta
        return redirect()->back()->with('success', 'Funcionalidade de adicionar item será implementada.');
    }

    private function updateItem(Request $request)
    {
        \Log::info('=== INICIANDO ATUALIZAÇÃO DE ITEM ===');
        \Log::info('Dados recebidos:', $request->all());
        
        // Verificar se o método está sendo chamado
        \Log::info('Método updateItem chamado com sucesso!');
        
        try {
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
                'unit_price' => 'required|numeric|min:0',
                'item_cover_image' => 'nullable|image|max:10240',
            ]);
            
            // Calcular quantidade total a partir dos tamanhos
            $totalQuantity = 0;
            foreach ($validated['tamanhos'] as $size => $quantity) {
                if ($quantity && $quantity > 0) {
                    $totalQuantity += (int)$quantity;
                }
            }
            
            // Adicionar quantidade calculada aos dados validados
            $validated['quantity'] = $totalQuantity;
            
            // Validar se a quantidade total é maior que 0
            if ($totalQuantity <= 0) {
                throw new \Illuminate\Validation\ValidationException(
                    validator([], []), 
                    ['tamanhos' => ['Pelo menos um tamanho deve ter quantidade maior que 0.']]
                );
            }
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Erro de validação:', $e->errors());
            throw $e;
        }
        
        \Log::info('Dados validados:', $validated);

        $orderId = session('edit_order_id');
        $order = Order::with('items')->findOrFail($orderId);
        $item = $order->items()->findOrFail($validated['editing_item_id']);
        
        \Log::info('Item encontrado:', $item->toArray());

        // Processar upload da imagem de capa do item
        $coverImagePath = $item->cover_image; // Manter imagem atual se não houver nova
        if ($request->hasFile('item_cover_image')) {
            $coverImage = $request->file('item_cover_image');
            $coverImageName = time() . '_' . uniqid() . '_' . $coverImage->getClientOriginalName();
            $coverImagePath = $coverImage->storeAs('orders/items', $coverImageName, 'public');
            \Log::info('Nova imagem de capa salva:', $coverImagePath);
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
        foreach ($validated['tamanhos'] as $size => $quantity) {
            if ($quantity && $quantity > 0) {
                $sizes[$size] = (int)$quantity;
            }
        }
        
        // Usar a quantidade já calculada na validação
        $totalQuantity = $validated['quantity'];

        // Dados antigos para log
        $oldData = $item->toArray();
        
        \Log::info('Dados antigos do item:', $oldData);
        \Log::info('Novos dados calculados:', [
            'print_type' => $personalizacaoNames,
            'fabric' => $tecido->name . ($tipoTecido ? ' - ' . $tipoTecido->name : ''),
            'color' => $cor->name,
            'collar' => $gola->name,
            'model' => $tipoCorte->name,
            'detail' => $detalhe ? $detalhe->name : null,
            'sizes' => $sizes,
            'quantity' => $totalQuantity,
            'unit_price' => $basePrice,
            'total_price' => $totalQuantity * $basePrice,
        ]);

        // Atualizar item
        $updateData = [
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
        ];
        
        \Log::info('Tentando atualizar item com dados:', $updateData);
        
        $item->update($updateData);
        
        \Log::info('Item atualizado com sucesso!');

        // Recalcular totais do pedido
        $newSubtotal = $order->items()->sum('total_price');
        $newTotalItems = $order->items()->sum('quantity');
        
        $order->update([
            'subtotal' => $newSubtotal,
            'total_items' => $newTotalItems,
        ]);
        
        // Log da atualização dos totais
        \Log::info("Totais atualizados - Subtotal: {$newSubtotal}, Total Items: {$newTotalItems}");

        // Log da edição do item
        $changes = $this->getChanges($oldData, $item->toArray());
        $this->logEdit('item_updated', "Item {$item->item_number} atualizado", $changes);

        // Recarregar o pedido com os dados atualizados
        $order->refresh();
        $order->load('items');
        
        return redirect()->route('orders.edit-wizard.sewing')->with('success', "Item {$item->item_number} atualizado com sucesso!");
    }

    private function finishSewing(Request $request)
    {
        // Implementar lógica de finalizar costura
        return redirect()->route('orders.edit-wizard.customization');
    }

    private function deleteItem(Request $request)
    {
        // Implementar lógica de deletar item
        return redirect()->back()->with('success', 'Item removido com sucesso.');
    }

    public function getItemData($id)
    {
        try {
            $item = OrderItem::findOrFail($id);
            
            // Log para debug
            \Log::info('Item encontrado:', ['id' => $id, 'item' => $item->toArray()]);
            
            return response()->json($item->toArray());
        } catch (\Exception $e) {
            \Log::error('Erro ao buscar item:', ['id' => $id, 'error' => $e->getMessage()]);
            
            return response()->json([
                'error' => 'Item não encontrado',
                'message' => $e->getMessage()
            ], 404);
        }
    }
}