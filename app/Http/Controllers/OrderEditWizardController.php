<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Client;
use App\Models\OrderItem;
use App\Models\OrderLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        $order = Order::findOrFail($orderId);

        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'phone_primary' => [
                    'required',
                    'string',
                    'max:50',
                    'regex:/^\(\d{2}\)\s\d{4,5}-\d{4}$/'
                ],
                'phone_secondary' => [
                    'nullable',
                    'string',
                    'max:50',
                    'regex:/^\(\d{2}\)\s\d{4,5}-\d{4}$/'
                ],
                'email' => 'nullable|email|max:255',
                'cpf_cnpj' => [
                    'nullable',
                    'string',
                    'max:20',
                    function ($attribute, $value, $fail) {
                        if (!empty($value)) {
                            $cpfPattern = '/^\d{3}\.\d{3}\.\d{3}-\d{2}$/';
                            $cnpjPattern = '/^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$/';
                            
                            if (!preg_match($cpfPattern, $value) && !preg_match($cnpjPattern, $value)) {
                                $fail('O campo :attribute deve ter o formato de CPF (000.000.000-00) ou CNPJ (00.000.000/0000-00).');
                            }
                        }
                    }
                ],
                'address' => 'nullable|string|max:500',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:2',
                'zip_code' => [
                    'nullable',
                    'string',
                    'max:12',
                    'regex:/^\d{5}-\d{3}$/'
                ],
                'category' => 'nullable|string|max:50',
            ], [
                'phone_primary.regex' => 'O telefone principal deve ter o formato (00) 00000-0000.',
                'phone_secondary.regex' => 'O telefone secundário deve ter o formato (00) 00000-0000.',
                'zip_code.regex' => 'O CEP deve ter o formato 00000-000.',
            ]);

            $editData['client'] = $validated;
            session(['edit_order_data' => $editData]);

            return redirect()->route('orders.edit-wizard.sewing');
        }

        return view('orders.wizard.client', compact('order', 'editData'));
    }

    public function sewing(Request $request)
    {
        \Log::info('=== INÍCIO MÉTODO SEWING ===', [
            'method' => $request->method(),
            'url' => $request->url(),
            'is_ajax' => $request->ajax(),
            'wants_json' => $request->wantsJson(),
            'all_input' => $request->all()
        ]);

        $editData = session('edit_order_data', []);
        $orderId = session('edit_order_id');

        \Log::info('Dados da sessão', [
            'order_id' => $orderId,
            'edit_data' => $editData
        ]);

        if (!$orderId) {
            \Log::warning('Sessão de edição expirada');
            return redirect()->route('orders.index')->with('error', 'Sessão de edição expirada.');
        }

        $order = Order::findOrFail($orderId);
        \Log::info('Pedido carregado', [
            'order_id' => $order->id,
            'items_count' => $order->items->count()
        ]);

        if ($request->isMethod('post')) {
            $action = $request->input('action');
            
            // Verificar se é uma requisição AJAX
            if ($request->wantsJson() || $request->ajax()) {
                \Log::info('Requisição AJAX detectada', [
                    'action' => $action,
                    'content_type' => $request->header('Content-Type'),
                    'accept' => $request->header('Accept')
                ]);

                if ($action === 'update_items') {
                    $items = $request->input('items', []);
                    
                    \Log::info('Atualizando itens via AJAX', [
                        'items_count' => count($items),
                        'items_data' => $items
                    ]);
                    
                    $editData['items'] = $items;
                    session(['edit_order_data' => $editData]);
                    
                    \Log::info('Itens salvos na sessão', [
                        'session_edit_data' => session('edit_order_data')
                    ]);
                    
                    return response()->json(['success' => true, 'message' => 'Itens atualizados com sucesso']);
                }
            }
            
            \Log::info('Processando ação normal', [
                'action' => $action,
                'edit_data_before' => $editData
            ]);

            if ($action === 'add_item') {
                \Log::info('Adicionando novo item');
                $validated = $request->validate([
                    'print_type' => 'required|string|max:255',
                    'art_name' => 'nullable|string|max:255',
                    'quantity' => 'required|integer|min:1',
                    'fabric' => 'required|string|max:255',
                    'color' => 'required|string|max:100',
                    'unit_price' => 'required|numeric|min:0',
                    'collar' => 'nullable|string|max:255',
                    'model' => 'nullable|string|max:255',
                    'detail' => 'nullable|string|max:255',
                    'tipo_tecido' => 'nullable|string|max:255',
                ]);

                // Adicionar novo item aos dados de edição
                if (!isset($editData['items'])) {
                    $editData['items'] = [];
                }
                
                $newItem = array_merge($validated, [
                    'item_number' => count($editData['items']) + 1,
                    'total_price' => $validated['quantity'] * $validated['unit_price']
                ]);
                
                $editData['items'][] = $newItem;
                session(['edit_order_data' => $editData]);

                return redirect()->back()->with('success', 'Item adicionado com sucesso!');
                
            } elseif ($action === 'update_item') {
                \Log::info('Atualizando item existente');
                $validated = $request->validate([
                    'editing_item_id' => 'required|integer',
                    'print_type' => 'required|string|max:255',
                    'art_name' => 'nullable|string|max:255',
                    'quantity' => 'required|integer|min:1',
                    'fabric' => 'required|string|max:255',
                    'color' => 'required|string|max:100',
                    'unit_price' => 'required|numeric|min:0',
                    'collar' => 'nullable|string|max:255',
                    'model' => 'nullable|string|max:255',
                    'detail' => 'nullable|string|max:255',
                    'tipo_tecido' => 'nullable|string|max:255',
                ]);

                $itemId = $validated['editing_item_id'];
                unset($validated['editing_item_id']);

                // Atualizar item nos dados de edição
                if (!isset($editData['items'])) {
                    $editData['items'] = [];
                }
                
                $updatedItem = array_merge($validated, [
                    'total_price' => $validated['quantity'] * $validated['unit_price']
                ]);
                
                // Encontrar e atualizar o item
                foreach ($editData['items'] as $index => $item) {
                    if ($item['id'] == $itemId) {
                        $editData['items'][$index] = array_merge($item, $updatedItem);
                        break;
                    }
                }
                
                session(['edit_order_data' => $editData]);

                return redirect()->back()->with('success', 'Item atualizado com sucesso!');
                
            } elseif ($action === 'delete_item') {
                $itemId = $request->input('item_id');
                
                // Remover item dos dados de edição
                if (isset($editData['items'])) {
                    $editData['items'] = array_filter($editData['items'], function($item) use ($itemId) {
                        return $item['id'] != $itemId;
                    });
                    $editData['items'] = array_values($editData['items']); // Reindexar array
                    session(['edit_order_data' => $editData]);
                }

                return redirect()->back()->with('success', 'Item removido com sucesso!');
                
            } elseif ($action === 'finish') {
                \Log::info('Finalizando etapa de costura');
                return redirect()->route('orders.edit-wizard.customization');
            }
        }

        \Log::info('Retornando view de costura', [
            'edit_data_final' => $editData,
            'order_items_count' => $order->items->count()
        ]);

        return view('orders.edit-wizard.sewing', compact('order', 'editData'));
    }

    public function customization(Request $request)
    {
        $editData = session('edit_order_data', []);
        $orderId = session('edit_order_id');

        if (!$orderId) {
            return redirect()->route('orders.index')->with('error', 'Sessão de edição expirada.');
        }

        $order = Order::findOrFail($orderId);

        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'contract_type' => 'required|in:costura,personalizacao,ambos',
                'seller' => 'nullable|string|max:255',
            ]);

            $editData = array_merge($editData, $validated);
            session(['edit_order_data' => $editData]);

            return redirect()->route('orders.edit-wizard.payment');
        }

        return view('orders.wizard.customization', compact('order', 'editData'));
    }

    public function payment(Request $request)
    {
        $editData = session('edit_order_data', []);
        $orderId = session('edit_order_id');

        if (!$orderId) {
            return redirect()->route('orders.index')->with('error', 'Sessão de edição expirada.');
        }

        $order = Order::findOrFail($orderId);

        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'delivery_date' => 'required|date',
                'subtotal' => 'required|numeric|min:0',
                'discount' => 'nullable|numeric|min:0',
                'delivery_fee' => 'nullable|numeric|min:0',
                'total' => 'required|numeric|min:0',
                'notes' => 'nullable|string|max:1000',
            ]);

            $editData = array_merge($editData, $validated);
            session(['edit_order_data' => $editData]);

            return redirect()->route('orders.edit-wizard.confirm');
        }

        return view('orders.wizard.payment', compact('order', 'editData'));
    }

    public function confirm()
    {
        $editData = session('edit_order_data', []);
        $orderId = session('edit_order_id');

        if (!$orderId) {
            return redirect()->route('orders.index')->with('error', 'Sessão de edição expirada.');
        }

        $order = Order::findOrFail($orderId);

        return view('orders.wizard.confirm', compact('order', 'editData'));
    }

    public function finalize(Request $request)
    {
        $editData = session('edit_order_data', []);
        $orderId = session('edit_order_id');

        if (!$orderId) {
            return redirect()->route('orders.index')->with('error', 'Sessão de edição expirada.');
        }

        $request->validate([
            'edit_reason' => 'required|string|max:1000',
        ]);

        $order = Order::findOrFail($orderId);

        // Verificar se o pedido ainda pode ser editado
        if ($order->is_cancelled) {
            return redirect()->route('orders.index')->with('error', 'Este pedido foi cancelado e não pode ser editado.');
        }

        if ($order->has_pending_edit) {
            return redirect()->route('orders.index')->with('error', 'Já existe uma solicitação de edição pendente para este pedido.');
        }

        // Verificar se o usuário é administrador
        $isAdmin = Auth::user()->isAdmin();
        
        \Log::info('Verificação de administrador', [
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name,
            'user_role' => Auth::user()->role,
            'is_admin' => $isAdmin
        ]);

        DB::beginTransaction();
        try {
            if ($isAdmin) {
                // Edição direta para administradores
                \Log::info('Aplicando edição direta para administrador');
                $this->applyChangesDirectly($order, $editData, $request->edit_reason);
                
                $message = 'Pedido editado com sucesso!';
            } else {
                // Criar solicitação de edição para usuários normais
                $editRequest = \App\Models\OrderEditRequest::create([
                    'order_id' => $order->id,
                    'user_id' => Auth::id(),
                    'reason' => $request->edit_reason,
                    'changes' => $this->prepareChanges($order, $editData),
                    'status' => 'pending'
                ]);

                // Atualizar pedido
                $order->update([
                    'has_pending_edit' => true
                ]);

                // Criar log
                OrderLog::create([
                    'order_id' => $order->id,
                    'user_id' => Auth::id(),
                    'action' => 'edit_requested',
                    'description' => 'Solicitação de edição completa enviada via wizard',
                    'details' => json_encode([
                        'reason' => $request->edit_reason,
                        'edit_request_id' => $editRequest->id,
                        'changes_summary' => 'Edição completa do pedido via wizard'
                    ])
                ]);
                
                $message = 'Solicitação de edição completa enviada com sucesso! Aguarde a aprovação do administrador.';
            }

            // Limpar sessão
            session()->forget('edit_order_data');
            session()->forget('edit_order_id');

            DB::commit();

            return redirect()->route('orders.show', ['id' => $order->id, 't' => time()])
                ->with('success', $message)
                ->with('refresh', true);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erro ao processar solicitação de edição: ' . $e->getMessage());
        }
    }

    private function prepareChanges($order, $editData)
    {
        $changes = [];

        // Dados do Cliente
        $clientChanges = [];
        $client = $order->client;
        
        if ($client->name !== $editData['client']['name']) {
            $clientChanges['name'] = ['old' => $client->name, 'new' => $editData['client']['name']];
        }
        if ($client->phone_primary !== $editData['client']['phone_primary']) {
            $clientChanges['phone_primary'] = ['old' => $client->phone_primary, 'new' => $editData['client']['phone_primary']];
        }
        if ($client->email !== $editData['client']['email']) {
            $clientChanges['email'] = ['old' => $client->email, 'new' => $editData['client']['email']];
        }
        if ($client->cpf_cnpj !== $editData['client']['cpf_cnpj']) {
            $clientChanges['cpf_cnpj'] = ['old' => $client->cpf_cnpj, 'new' => $editData['client']['cpf_cnpj']];
        }
        if ($client->address !== $editData['client']['address']) {
            $clientChanges['address'] = ['old' => $client->address, 'new' => $editData['client']['address']];
        }
        
        if (!empty($clientChanges)) {
            $changes['client'] = $clientChanges;
        }

        // Itens do Pedido
        $itemsChanges = [];
        foreach ($editData['items'] as $index => $itemData) {
            $item = $order->items->get($index);
            if ($item) {
                $itemChanges = [];
                if ($item->print_type !== $itemData['print_type']) {
                    $itemChanges['print_type'] = ['old' => $item->print_type, 'new' => $itemData['print_type']];
                }
                if ($item->art_name !== $itemData['art_name']) {
                    $itemChanges['art_name'] = ['old' => $item->art_name, 'new' => $itemData['art_name']];
                }
                if ($item->quantity != $itemData['quantity']) {
                    $itemChanges['quantity'] = ['old' => $item->quantity, 'new' => $itemData['quantity']];
                }
                if ($item->fabric !== $itemData['fabric']) {
                    $itemChanges['fabric'] = ['old' => $item->fabric, 'new' => $itemData['fabric']];
                }
                if ($item->color !== $itemData['color']) {
                    $itemChanges['color'] = ['old' => $item->color, 'new' => $itemData['color']];
                }
                if ($item->unit_price != $itemData['unit_price']) {
                    $itemChanges['unit_price'] = ['old' => $item->unit_price, 'new' => $itemData['unit_price']];
                }
                
                if (!empty($itemChanges)) {
                    $itemsChanges[$item->id] = $itemChanges;
                }
            }
        }
        if (!empty($itemsChanges)) {
            $changes['items'] = $itemsChanges;
        }

        // Personalização
        $personalizationChanges = [];
        if ($order->contract_type !== $editData['contract_type']) {
            $personalizationChanges['contract_type'] = ['old' => $order->contract_type, 'new' => $editData['contract_type']];
        }
        if ($order->seller !== $editData['seller']) {
            $personalizationChanges['seller'] = ['old' => $order->seller, 'new' => $editData['seller']];
        }
        if (!empty($personalizationChanges)) {
            $changes['personalization'] = $personalizationChanges;
        }

        // Pagamento e Valores
        $paymentChanges = [];
        if ($order->delivery_date !== $editData['delivery_date']) {
            $paymentChanges['delivery_date'] = ['old' => $order->delivery_date, 'new' => $editData['delivery_date']];
        }
        if ($order->subtotal != $editData['subtotal']) {
            $paymentChanges['subtotal'] = ['old' => $order->subtotal, 'new' => $editData['subtotal']];
        }
        if ($order->discount != $editData['discount']) {
            $paymentChanges['discount'] = ['old' => $order->discount, 'new' => $editData['discount']];
        }
        if ($order->delivery_fee != $editData['delivery_fee']) {
            $paymentChanges['delivery_fee'] = ['old' => $order->delivery_fee, 'new' => $editData['delivery_fee']];
        }
        if ($order->total != $editData['total']) {
            $paymentChanges['total'] = ['old' => $order->total, 'new' => $editData['total']];
        }
        if (!empty($paymentChanges)) {
            $changes['payment'] = $paymentChanges;
        }

        // Observações
        if ($order->notes !== $editData['notes']) {
            $changes['notes'] = ['old' => $order->notes, 'new' => $editData['notes']];
        }

        return $changes;
    }

    private function applyChangesDirectly($order, $editData, $reason)
    {
        \Log::info('Iniciando aplicação direta de alterações', [
            'order_id' => $order->id,
            'edit_data_keys' => array_keys($editData),
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name
        ]);
        
        $changes = [];
        
        // Atualizar dados do cliente
        $client = $order->client;
        $clientChanges = [];
        
        if ($client->name !== $editData['client']['name']) {
            $clientChanges['name'] = ['old' => $client->name, 'new' => $editData['client']['name']];
            $client->name = $editData['client']['name'];
        }
        if ($client->phone_primary !== $editData['client']['phone_primary']) {
            $clientChanges['phone_primary'] = ['old' => $client->phone_primary, 'new' => $editData['client']['phone_primary']];
            $client->phone_primary = $editData['client']['phone_primary'];
        }
        if ($client->email !== $editData['client']['email']) {
            $clientChanges['email'] = ['old' => $client->email, 'new' => $editData['client']['email']];
            $client->email = $editData['client']['email'];
        }
        if ($client->cpf_cnpj !== $editData['client']['cpf_cnpj']) {
            $clientChanges['cpf_cnpj'] = ['old' => $client->cpf_cnpj, 'new' => $editData['client']['cpf_cnpj']];
            $client->cpf_cnpj = $editData['client']['cpf_cnpj'];
        }
        if ($client->address !== $editData['client']['address']) {
            $clientChanges['address'] = ['old' => $client->address, 'new' => $editData['client']['address']];
            $client->address = $editData['client']['address'];
        }
        
        if (!empty($clientChanges)) {
            $client->save();
            $changes['client'] = $clientChanges;
            \Log::info('Alterações do cliente aplicadas', ['changes' => $clientChanges]);
        }

        // Atualizar itens do pedido
        $itemsChanges = [];
        
        // Recarregar os itens do pedido para garantir que temos os dados mais recentes
        $order->load('items');
        $currentItems = $order->items->keyBy('id');
        
        \Log::info('Itens atuais carregados', [
            'count' => $currentItems->count(),
            'item_ids' => $currentItems->keys()->toArray(),
            'items_data' => $currentItems->toArray()
        ]);
        
        foreach ($editData['items'] as $index => $itemData) {
            \Log::info('Processando item', [
                'index' => $index,
                'item_data' => $itemData,
                'has_id' => isset($itemData['id']),
                'item_id' => $itemData['id'] ?? 'N/A'
            ]);
            
            if (isset($itemData['id']) && $currentItems->has($itemData['id'])) {
                // Item existente - atualizar
                $item = $currentItems->get($itemData['id']);
                $itemChanges = [];
                \Log::info('Atualizando item existente', ['item_id' => $item->id]);
                
                if ($item->print_type !== $itemData['print_type']) {
                    $itemChanges['print_type'] = ['old' => $item->print_type, 'new' => $itemData['print_type']];
                    $item->print_type = $itemData['print_type'];
                }
                if ($item->art_name !== $itemData['art_name']) {
                    $itemChanges['art_name'] = ['old' => $item->art_name, 'new' => $itemData['art_name']];
                    $item->art_name = $itemData['art_name'];
                }
                if ($item->quantity != $itemData['quantity']) {
                    $itemChanges['quantity'] = ['old' => $item->quantity, 'new' => $itemData['quantity']];
                    $item->quantity = $itemData['quantity'];
                }
                if ($item->fabric !== $itemData['fabric']) {
                    $itemChanges['fabric'] = ['old' => $item->fabric, 'new' => $itemData['fabric']];
                    $item->fabric = $itemData['fabric'];
                }
                if ($item->color !== $itemData['color']) {
                    $itemChanges['color'] = ['old' => $item->color, 'new' => $itemData['color']];
                    $item->color = $itemData['color'];
                }
                if ($item->unit_price != $itemData['unit_price']) {
                    $itemChanges['unit_price'] = ['old' => $item->unit_price, 'new' => $itemData['unit_price']];
                    $item->unit_price = $itemData['unit_price'];
                }
                
                // Recalcular total_price se quantidade ou preço mudaram
                $newTotalPrice = $item->quantity * $item->unit_price;
                if ($item->total_price != $newTotalPrice) {
                    $itemChanges['total_price'] = ['old' => $item->total_price, 'new' => $newTotalPrice];
                    $item->total_price = $newTotalPrice;
                }
                
                if (!empty($itemChanges)) {
                    $item->save();
                    $itemsChanges[$item->id] = $itemChanges;
                    \Log::info('Item atualizado', [
                        'item_id' => $item->id,
                        'changes' => $itemChanges,
                        'new_values' => $item->toArray()
                    ]);
                } else {
                    \Log::info('Nenhuma alteração detectada para o item', [
                        'item_id' => $item->id,
                        'current_values' => $item->toArray(),
                        'new_values' => $itemData
                    ]);
                }
            } else {
                // Novo item - criar
                $newItem = new OrderItem([
                    'order_id' => $order->id,
                    'print_type' => $itemData['print_type'],
                    'art_name' => $itemData['art_name'],
                    'quantity' => $itemData['quantity'],
                    'fabric' => $itemData['fabric'],
                    'color' => $itemData['color'],
                    'unit_price' => $itemData['unit_price'],
                    'item_number' => $order->items->count() + 1
                ]);
                $newItem->save();
                
                $itemsChanges['new_' . $newItem->id] = [
                    'action' => 'created',
                    'data' => $itemData
                ];
            }
        }
        
        // Remover itens que não estão mais na lista
        $newItemIds = collect($editData['items'])->pluck('id')->filter()->toArray();
        foreach ($currentItems as $item) {
            if (!in_array($item->id, $newItemIds)) {
                $itemsChanges['deleted_' . $item->id] = [
                    'action' => 'deleted',
                    'data' => $item->toArray()
                ];
                $item->delete();
            }
        }
        
        if (!empty($itemsChanges)) {
            $changes['items'] = $itemsChanges;
            \Log::info('Alterações dos itens aplicadas', ['changes' => $itemsChanges]);
        }

        // Atualizar dados do pedido
        $orderChanges = [];
        
        if ($order->contract_type !== $editData['contract_type']) {
            $orderChanges['contract_type'] = ['old' => $order->contract_type, 'new' => $editData['contract_type']];
            $order->contract_type = $editData['contract_type'];
        }
        if ($order->seller !== $editData['seller']) {
            $orderChanges['seller'] = ['old' => $order->seller, 'new' => $editData['seller']];
            $order->seller = $editData['seller'];
        }
        if ($order->delivery_date !== $editData['delivery_date']) {
            $orderChanges['delivery_date'] = ['old' => $order->delivery_date, 'new' => $editData['delivery_date']];
            $order->delivery_date = $editData['delivery_date'];
        }
        if ($order->subtotal != $editData['subtotal']) {
            $orderChanges['subtotal'] = ['old' => $order->subtotal, 'new' => $editData['subtotal']];
            $order->subtotal = $editData['subtotal'];
        }
        if ($order->discount != $editData['discount']) {
            $orderChanges['discount'] = ['old' => $order->discount, 'new' => $editData['discount']];
            $order->discount = $editData['discount'];
        }
        if ($order->delivery_fee != $editData['delivery_fee']) {
            $orderChanges['delivery_fee'] = ['old' => $order->delivery_fee, 'new' => $editData['delivery_fee']];
            $order->delivery_fee = $editData['delivery_fee'];
        }
        if ($order->total != $editData['total']) {
            $orderChanges['total'] = ['old' => $order->total, 'new' => $editData['total']];
            $order->total = $editData['total'];
        }
        if ($order->notes !== $editData['notes']) {
            $orderChanges['notes'] = ['old' => $order->notes, 'new' => $editData['notes']];
            $order->notes = $editData['notes'];
        }
        
        if (!empty($orderChanges)) {
            $order->last_updated_at = now();
            $order->save();
            $changes['order'] = $orderChanges;
            \Log::info('Alterações do pedido aplicadas', ['changes' => $orderChanges]);
        }

        // Criar log de edição
        \App\Models\OrderEditHistory::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name,
            'action' => 'edit_completed',
            'description' => 'Edição completa aplicada diretamente por administrador',
            'changes' => $changes
        ]);

        // Criar log geral
        OrderLog::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name,
            'action' => 'edit_completed',
            'description' => 'Edição completa aplicada diretamente por administrador',
            'old_value' => null,
            'new_value' => $changes
        ]);
        
        // Recarregar o modelo para garantir que as alterações sejam refletidas
        $order->refresh();
        $order->load(['client', 'items']);
        
        // Forçar atualização do timestamp
        $order->touch();
        
        // Verificar se as alterações foram realmente aplicadas
        $order->refresh();
        $order->load(['client', 'items']);
        
        // Forçar atualização do cache do relacionamento
        $order->unsetRelation('items');
        $order->load('items');
        
        // Verificar se as alterações foram realmente aplicadas
        $order->refresh();
        $order->load(['client', 'items']);
        
        \Log::info('Edição direta concluída com sucesso', [
            'order_id' => $order->id,
            'total_changes' => count($changes),
            'changes_summary' => array_keys($changes),
            'order_updated_at' => $order->last_updated_at,
            'items_count' => $order->items->count(),
            'final_items' => $order->items->toArray()
        ]);
    }
}
