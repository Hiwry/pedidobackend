<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kanban - Sistema de Pedidos</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <x-app-header />

    <div class="max-w-[1800px] mx-auto p-6">
        @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
        @endif

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
        @endif

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold">Kanban de Produção</h1>
            <div class="flex items-center space-x-4">
                <div class="text-sm text-gray-600">
                    Total de Pedidos: <strong>{{ $ordersByStatus->flatten()->count() }}</strong>
                </div>
                <a href="{{ route('kanban.columns.index') }}" 
                   class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Gerenciar Colunas</span>
                </a>
            </div>
        </div>

        <!-- Barra de Busca -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <form method="GET" action="{{ route('kanban.index') }}" class="flex gap-3">
                <div class="flex-1">
                    <input type="text" 
                           name="search" 
                           value="{{ $search ?? '' }}"
                           placeholder="🔍 Buscar por nº do pedido, nome do cliente, telefone ou nome da arte..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <button type="submit" 
                        class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 whitespace-nowrap">
                    Buscar
                </button>
                @if($search)
                <a href="{{ route('kanban.index') }}" 
                   class="px-6 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 whitespace-nowrap">
                    Limpar
                </a>
                @endif
            </form>
            @if($search)
            <div class="mt-2 text-sm text-gray-600">
                Mostrando resultados para: <strong>"{{ $search }}"</strong>
            </div>
            @endif
        </div>

        <div class="flex gap-4 overflow-x-auto pb-4">
            @foreach($statuses as $status)
                <div class="bg-gray-50 rounded-lg border border-gray-200 flex-shrink-0" style="min-width: 320px; max-width: 320px;">
                    <div class="px-4 py-3 font-semibold flex justify-between items-center" 
                         style="background: {{ $status->color }}; color: #fff">
                        <span>{{ $status->name }}</span>
                        <span class="bg-white bg-opacity-30 px-2 py-1 rounded-full text-xs">
                            {{ ($ordersByStatus[$status->id] ?? collect())->count() }}
                        </span>
                    </div>
                    <div class="kanban-column p-3 space-y-3 overflow-y-auto" style="height: calc(100vh - 300px); max-height: 800px;" data-status-id="{{ $status->id }}">
                        @foreach(($ordersByStatus[$status->id] ?? collect()) as $order)
                            @php
                                $firstItem = $order->items->first();
                                $coverImage = $firstItem && $firstItem->cover_image 
                                    ? asset('storage/' . $firstItem->cover_image) 
                                    : null;
                                // Buscar o nome da arte da primeira personalização
                                $artName = null;
                                if ($firstItem && $firstItem->sublimations) {
                                    $firstSublimation = $firstItem->sublimations->first();
                                    if ($firstSublimation && $firstSublimation->art_name) {
                                        $artName = $firstSublimation->art_name;
                                    }
                                }
                                $displayName = $artName ?? $order->client->name;
                            @endphp
                            <div class="kanban-card bg-white shadow rounded-lg overflow-hidden cursor-move hover:shadow-xl transition-all duration-200 border border-gray-200" 
                                 draggable="true" 
                                 data-order-id="{{ $order->id }}"
                                 onclick="event.stopPropagation(); openOrderModal({{ $order->id }})">
                                
                                <!-- Imagem de Capa -->
                                @if($coverImage)
                                <div class="h-32 bg-gray-200 overflow-hidden">
                                    <img src="{{ $coverImage }}" 
                                         alt="Capa do Pedido" 
                                         class="w-full h-full object-cover">
                                </div>
                                @else
                                <div class="h-32 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                @endif

                                <!-- Conteúdo do Card -->
                                <div class="p-4">
                                    <!-- Número do Pedido e Cliente -->
                                    <div class="mb-3">
                                        <div class="flex items-center justify-between mb-1">
                                            <div class="flex items-center space-x-2">
                                                <a href="{{ route('orders.show', $order->id) }}" 
                                                   class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded hover:bg-indigo-100 hover:text-indigo-800 transition-colors">
                                                    #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                                                </a>
                                                @if($order->edit_status === 'requested')
                                                <span class="text-xs font-medium bg-orange-100 text-orange-800 px-2 py-1 rounded-full">
                                                    ⏳ Aguardando Aprovação
                                                </span>
                                                @elseif($order->edit_status === 'approved')
                                                <span class="text-xs font-medium bg-green-100 text-green-800 px-2 py-1 rounded-full">
                                                    ✅ Aprovado para Edição
                                                </span>
                                                @elseif($order->edit_status === 'rejected')
                                                <span class="text-xs font-medium bg-red-100 text-red-800 px-2 py-1 rounded-full">
                                                    ❌ Edição Rejeitada
                                                </span>
                                                @elseif($order->is_modified)
                                                <span class="text-xs font-medium bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                                                    🔄 Modificado
                                                </span>
                                                @endif
                                                
                                                <!-- Indicadores de Cancelamento e Edição -->
                                                @if($order->has_pending_cancellation)
                                                <span class="text-xs font-medium bg-red-100 text-red-800 px-2 py-1 rounded-full">
                                                    ⏳ Cancelamento Pendente
                                                </span>
                                                @elseif($order->is_cancelled)
                                                <span class="text-xs font-medium bg-gray-100 text-gray-800 px-2 py-1 rounded-full">
                                                    ❌ Cancelado
                                                </span>
                                                @endif
                                                
                                                @if($order->has_pending_edit)
                                                <span class="text-xs font-medium bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">
                                                    ✏️ Edição Pendente
                                                </span>
                                                @endif
                                                
                                                @if($order->last_updated_at && $order->last_updated_at > $order->updated_at)
                                                <span class="text-xs font-medium bg-purple-100 text-purple-800 px-2 py-1 rounded-full">
                                                    🔄 Atualizado
                                                </span>
                                                @endif
                                            </div>
                                            <span class="text-xs text-gray-500">
                                                {{ $order->items->sum('quantity') }} pçs
                                            </span>
                                        </div>
                                        <h3 class="font-semibold text-gray-900 text-sm truncate" title="{{ $displayName }}">
                                            @if($artName)
                                                🎨 {{ $displayName }}
                                            @else
                                                {{ $displayName }}
                                            @endif
                                        </h3>
                                    </div>

                                    <!-- Informações do Produto -->
                                    @if($firstItem)
                                    <div class="space-y-2 mb-3 text-xs">
                                        <div class="flex items-center text-gray-600">
                                            <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                                            </svg>
                                            <span class="truncate" title="{{ $firstItem->fabric }}">
                                                <strong>Tecido:</strong> {{ $firstItem->fabric }}
                                            </span>
                                        </div>

                                        @if($firstItem->model)
                                        <div class="flex items-center text-gray-600">
                                            <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243 4.243 3 3 0 004.243-4.243zm0-5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z"></path>
                                            </svg>
                                            <span class="truncate" title="{{ $firstItem->model }}">
                                                <strong>Corte:</strong> {{ $firstItem->model }}
                                            </span>
                                        </div>
                                        @endif

                                        @if($firstItem->print_type)
                                        <div class="flex items-center text-gray-600">
                                            <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                                            </svg>
                                            <span class="truncate" title="{{ $firstItem->print_type }}">
                                                <strong>Personalização:</strong> {{ $firstItem->print_type }}
                                            </span>
                                        </div>
                                        @endif
                                    </div>
                                    @endif

                                    <!-- Vendedor e Criador -->
                                    <div class="mb-3 space-y-1">
                                        @if($order->seller)
                                        <div class="flex items-center text-xs text-gray-600">
                                            <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            <span class="truncate" title="{{ $order->seller }}">
                                                <strong>Vendedor:</strong> {{ $order->seller }}
                                            </span>
                                        </div>
                                        @endif
                                        
                                        @if($order->user)
                                        <div class="flex items-center text-xs text-gray-600">
                                            <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                            </svg>
                                            <span class="truncate" title="{{ $order->user->name }}">
                                                <strong>Criado por:</strong> {{ $order->user->name }}
                                            </span>
                                        </div>
                                        @endif
                                    </div>

                                    <!-- Datas -->
                                    <div class="border-t pt-3 space-y-1 text-xs text-gray-600">
                                        @if($order->created_at)
                                        <div class="flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span><strong>Pedido:</strong> {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y') }}</span>
                                        </div>
                                        @endif

                                        @if($order->delivery_date)
                                        <div class="flex items-center text-orange-600">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <span><strong>Entrega:</strong> {{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') }}</span>
                                        </div>
                                        @endif
                                    </div>

                                    <!-- Total -->
                                    <div class="mt-3 pt-3 border-t">
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs text-gray-600">Total:</span>
                                            <span class="text-sm font-bold text-green-600">
                                                R$ {{ number_format($order->total, 2, ',', '.') }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Botões de Ação -->
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Modal de Detalhes do Pedido -->
    <div id="order-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-10 mx-auto p-5 border w-full max-w-6xl shadow-lg rounded-md bg-white mb-10">
            <div class="flex justify-between items-center mb-4 pb-4 border-b">
                <h3 class="text-2xl font-bold text-gray-900" id="modal-title">Detalhes do Pedido</h3>
                <button onclick="closeOrderModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div id="modal-content" class="space-y-6">
                <!-- Será preenchido via JavaScript -->
            </div>
        </div>
    </div>

    <!-- Modal de Pagamento Adicional -->
    <div id="payment-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4 pb-4 border-b">
                <h3 class="text-xl font-bold text-gray-900">💵 Registrar Pagamento</h3>
                <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="payment-form" onsubmit="submitPayment(event)">
                <input type="hidden" id="payment-order-id" name="order_id">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Valor Restante</label>
                    <div class="text-2xl font-bold text-orange-600" id="remaining-amount">R$ 0,00</div>
                </div>

                <div class="mb-4">
                    <label for="payment-amount" class="block text-sm font-medium text-gray-700 mb-2">Valor a Pagar *</label>
                    <input type="number" 
                           id="payment-amount" 
                           name="amount" 
                           step="0.01" 
                           min="0.01"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                           placeholder="0,00">
                </div>

                <div class="mb-4">
                    <label for="payment-method" class="block text-sm font-medium text-gray-700 mb-2">Forma de Pagamento *</label>
                    <select id="payment-method" 
                            name="payment_method" 
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Selecione...</option>
                        <option value="dinheiro">Dinheiro</option>
                        <option value="pix">PIX</option>
                        <option value="cartao">Cartão</option>
                        <option value="transferencia">Transferência</option>
                        <option value="boleto">Boleto</option>
                    </select>
                </div>

                <div class="mb-6">
                    <label for="payment-date" class="block text-sm font-medium text-gray-700 mb-2">Data do Pagamento *</label>
                    <input type="date" 
                           id="payment-date" 
                           name="payment_date" 
                           required
                           value="{{ date('Y-m-d') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" 
                            onclick="closePaymentModal()"
                            class="px-4 py-2 text-gray-600 hover:text-gray-900">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Registrar Pagamento
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Solicitação de Antecipação -->
    <div id="delivery-request-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4 pb-4 border-b">
                <h3 class="text-xl font-bold text-gray-900">🚀 Solicitar Antecipação</h3>
                <button onclick="closeDeliveryRequestModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="delivery-request-form" onsubmit="submitDeliveryRequest(event)">
                <input type="hidden" id="delivery-order-id" name="order_id">
                <input type="hidden" id="current-delivery-date" name="current_delivery_date">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data de Entrega Atual</label>
                    <div class="text-lg font-bold text-orange-600" id="current-delivery-display">-</div>
                </div>

                <div class="mb-4">
                    <label for="requested-delivery-date" class="block text-sm font-medium text-gray-700 mb-2">Nova Data Solicitada *</label>
                    <input type="date" 
                           id="requested-delivery-date" 
                           name="requested_delivery_date" 
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">A data deve ser anterior à data de entrega atual</p>
                </div>

                <div class="mb-6">
                    <label for="delivery-reason" class="block text-sm font-medium text-gray-700 mb-2">Motivo da Solicitação *</label>
                    <textarea id="delivery-reason" 
                              name="reason" 
                              rows="3"
                              required
                              maxlength="500"
                              placeholder="Explique o motivo da antecipação..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" 
                            onclick="closeDeliveryRequestModal()"
                            class="px-4 py-2 text-gray-600 hover:text-gray-900">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Enviar Solicitação
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Drag and Drop functionality
        let draggedElement = null;
        let isDragging = false;

        document.querySelectorAll('.kanban-card').forEach(card => {
            card.addEventListener('dragstart', function(e) {
                isDragging = true;
                draggedElement = this;
                this.style.opacity = '0.5';
                this.classList.add('scale-95');
            });

            card.addEventListener('dragend', function(e) {
                setTimeout(() => { isDragging = false; }, 100);
                this.style.opacity = '1';
                this.classList.remove('scale-95');
            });

            // Prevenir click quando estiver arrastando
            card.addEventListener('click', function(e) {
                if (isDragging) {
                    e.stopPropagation();
                    e.preventDefault();
                }
            });
        });

        document.querySelectorAll('.kanban-column').forEach(column => {
            column.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('bg-blue-50', 'border-2', 'border-dashed', 'border-blue-400');
            });

            column.addEventListener('dragleave', function(e) {
                this.classList.remove('bg-blue-50', 'border-2', 'border-dashed', 'border-blue-400');
            });

            column.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('bg-blue-50', 'border-2', 'border-dashed', 'border-blue-400');
                
                if (draggedElement) {
                    this.appendChild(draggedElement);
                    
                    const orderId = draggedElement.dataset.orderId;
                    const newStatusId = this.dataset.statusId;
                    
                    // Atualizar status via AJAX
                    updateOrderStatus(orderId, newStatusId);
                }
            });
        });

        function updateOrderStatus(orderId, statusId) {
            fetch(`/kanban/update-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    order_id: orderId,
                    status_id: statusId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Status atualizado com sucesso!', 'success');
                } else {
                    showNotification('Erro ao atualizar status', 'error');
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showNotification('Erro ao atualizar status', 'error');
                location.reload();
            });
        }

        function openOrderModal(orderId) {
            if (isDragging) return;
            
            fetch(`/kanban/order/${orderId}`)
                .then(response => response.json())
                .then(data => {
                    displayOrderDetails(data);
                    document.getElementById('order-modal').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Erro:', error);
                    showNotification('Erro ao carregar detalhes do pedido', 'error');
                });
        }

        function closeOrderModal() {
            document.getElementById('order-modal').classList.add('hidden');
        }

        function displayOrderDetails(order) {
            const payment = order.payment;
            // Contar arquivos das personalizações
            const totalFiles = order.items.reduce((sum, item) => {
                let itemFilesCount = 0;
                if (item.sublimations) {
                    item.sublimations.forEach(sub => {
                        if (sub.files) {
                            itemFilesCount += sub.files.length;
                        }
                    });
                }
                return sum + itemFilesCount;
            }, 0);
            
            let html = `
                <!-- Botões de Download -->
                <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-200">
                    <h4 class="font-semibold mb-3 text-indigo-900">📥 Downloads</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <a href="/kanban/download-costura/${order.id}" target="_blank"
                           class="flex items-center justify-center px-4 py-3 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                            </svg>
                            Folha Costura (A4)
                        </a>
                        <a href="/kanban/download-personalizacao/${order.id}" target="_blank"
                           class="flex items-center justify-center px-4 py-3 bg-pink-600 text-white rounded-md hover:bg-pink-700 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                            </svg>
                            Folha Personalização (A4)
                        </a>
                        ${totalFiles > 0 ? `
                        <button onclick="downloadAllFiles(${order.id})"
                                class="flex items-center justify-center px-4 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                            </svg>
                            Arquivos da Arte (${totalFiles})
                        </button>
                        ` : ''}
                    </div>
                </div>

                <!-- Informações do Cliente -->
                <div class="bg-white rounded-lg border p-4">
                    <h4 class="font-semibold mb-3 text-gray-900">👤 Cliente</h4>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div><strong>Nome:</strong> ${order.client.name}</div>
                        <div><strong>Telefone:</strong> ${order.client.phone_primary || '-'}</div>
                        ${order.client.email ? `<div><strong>Email:</strong> ${order.client.email}</div>` : ''}
                        ${order.client.cpf_cnpj ? `<div><strong>CPF/CNPJ:</strong> ${order.client.cpf_cnpj}</div>` : ''}
                    </div>
                </div>

                <!-- Vendedor -->
                ${order.seller ? `
                <div class="bg-white rounded-lg border p-4">
                    <h4 class="font-semibold mb-3 text-gray-900">👨‍💼 Vendedor</h4>
                    <div class="text-sm">
                        <div><strong>Nome:</strong> ${order.seller}</div>
                    </div>
                </div>
                ` : ''}

                <!-- Itens do Pedido -->
                <div class="space-y-6">
                    <h4 class="font-bold text-lg text-gray-900">📦 Itens do Pedido (${order.items.length})</h4>
                    
                    ${order.items.map((item, index) => `
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border-2 border-indigo-200 p-6">
                        <div class="flex justify-between items-center mb-4 pb-3 border-b border-indigo-300">
                            <h5 class="text-xl font-bold text-indigo-900">Item ${item.item_number || index + 1}</h5>
                            <span class="text-sm bg-indigo-600 text-white px-3 py-1 rounded-full font-semibold">${item.quantity} peças</span>
                        </div>

                        <!-- Imagem de Capa -->
                        ${item.cover_image ? `
                        <div class="bg-white rounded-lg p-3 mb-4">
                            <h6 class="font-semibold mb-2 text-gray-900">🖼️ Imagem de Capa</h6>
                            <img src="/storage/${item.cover_image}" alt="Capa" class="max-w-sm mx-auto rounded-lg border">
                        </div>
                        ` : ''}

                        <!-- Detalhes da Costura -->
                        <div class="bg-white rounded-lg p-4 mb-4">
                            <h6 class="font-semibold mb-3 text-gray-900">✂️ Costura</h6>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">
                                <div><strong>Tecido:</strong> ${item.fabric}</div>
                                <div><strong>Cor:</strong> ${item.color}</div>
                                ${item.collar ? `<div><strong>Gola:</strong> ${item.collar}</div>` : ''}
                                ${item.detail ? `<div><strong>Detalhe:</strong> ${item.detail}</div>` : ''}
                                ${item.model ? `<div><strong>Tipo de Corte:</strong> ${item.model}</div>` : ''}
                                <div><strong>Personalização:</strong> ${item.print_type}</div>
                            </div>
                            
                            <div class="mt-4">
                                <strong class="block mb-2">Tamanhos:</strong>
                                <div class="grid grid-cols-5 md:grid-cols-10 gap-2">
                                    ${Object.entries(item.sizes).map(([size, qty]) => 
                                        qty > 0 ? `
                                        <div class="bg-gray-100 rounded px-2 py-1 text-center">
                                            <span class="text-xs text-gray-600">${size}</span>
                                            <p class="font-bold text-sm">${qty}</p>
                                        </div>
                                        ` : ''
                                    ).join('')}
                                </div>
                            </div>
                        </div>

                        <!-- Personalização -->
                        ${item.sublimations && item.sublimations.length > 0 ? `
                        <div class="bg-white rounded-lg p-4">
                            <h6 class="font-semibold mb-3 text-gray-900">🎨 Personalização</h6>
                            ${item.art_name ? `<p class="text-sm mb-2"><strong>Nome da Arte:</strong> ${item.art_name}</p>` : ''}
                            <div class="space-y-2">
                                ${item.sublimations.map(sub => {
                                    const sizeName = sub.size ? sub.size.name : sub.size_name;
                                    const sizeDimensions = sub.size ? sub.size.dimensions : '';
                                    const locationName = sub.location ? sub.location.name : sub.location_name;
                                    const appType = sub.application_type ? sub.application_type.toUpperCase() : 'APLICAÇÃO';
                                    
                                    return `
                                    <div class="flex justify-between items-center bg-gray-50 rounded p-3 text-sm">
                                        <div>
                                            <strong>
                                                ${sizeName ? sizeName : appType}${sizeDimensions ? ` (${sizeDimensions})` : ''}
                                            </strong>
                                            ${locationName ? ` - ${locationName}` : ''}
                                            <span class="text-gray-600">x${sub.quantity}</span>
                                            ${sub.color_count > 0 ? `<br><span class="text-xs text-gray-500">${sub.color_count} ${sub.color_count == 1 ? 'Cor' : 'Cores'}${sub.has_neon ? ' + Neon' : ''}</span>` : ''}
                                        </div>
                                        <div class="text-right">
                                            <div class="text-gray-600">R$ ${parseFloat(sub.unit_price).toFixed(2).replace('.', ',')} × ${sub.quantity}</div>
                                            ${sub.discount_percent > 0 ? `<div class="text-xs text-green-600">-${sub.discount_percent}%</div>` : ''}
                                            <div class="font-bold">R$ ${parseFloat(sub.final_price).toFixed(2).replace('.', ',')}</div>
                                        </div>
                                    </div>
                                `}).join('')}
                            </div>
                        </div>
                        ` : ''}
                    </div>
                    `).join('')}
                </div>

                <!-- Data de Entrega e Solicitação de Antecipação -->
                <div class="bg-white rounded-lg border p-4">
                    <h4 class="font-semibold mb-3 text-gray-900">📅 Entrega</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm"><strong>Data de Pedido:</strong></span>
                            <span class="text-sm">${new Date(order.created_at).toLocaleDateString('pt-BR')}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm"><strong>Data de Entrega:</strong></span>
                            <span class="text-sm font-bold text-orange-600">${order.delivery_date ? new Date(order.delivery_date).toLocaleDateString('pt-BR') : 'Não definida'}</span>
                        </div>
                        ${order.pending_delivery_request ? `
                        <div class="bg-yellow-50 border border-yellow-300 rounded-lg p-3">
                            <p class="text-sm font-semibold text-yellow-800 mb-1">⏳ Solicitação Pendente</p>
                            <p class="text-xs text-yellow-700">Nova data solicitada: ${new Date(order.pending_delivery_request.requested_delivery_date).toLocaleDateString('pt-BR')}</p>
                            <p class="text-xs text-yellow-700 mt-1">Motivo: ${order.pending_delivery_request.reason}</p>
                        </div>
                        ` : `
                        <button onclick="openDeliveryRequestModal(${order.id}, '${order.delivery_date}')" 
                                class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition text-sm">
                            🚀 Solicitar Antecipação de Entrega
                        </button>
                        `}
                    </div>
                </div>

                <!-- Pagamento -->
                ${payment ? `
                <div class="bg-white rounded-lg border p-4">
                    <h4 class="font-semibold mb-3 text-gray-900">💰 Pagamento</h4>
                    <div class="space-y-2 text-sm">
                        <div><strong>Data de Entrada:</strong> ${new Date(payment.entry_date).toLocaleDateString('pt-BR')}</div>
                        <div><strong>Formas de Pagamento:</strong></div>
                        ${payment.payment_methods.map(method => `
                            <div class="flex justify-between bg-gray-50 rounded p-2">
                                <span class="capitalize">${method.method}</span>
                                <span class="font-bold">R$ ${parseFloat(method.amount).toFixed(2).replace('.', ',')}</span>
                            </div>
                        `).join('')}
                        <div class="border-t pt-2 mt-2">
                            <div class="flex justify-between"><span>Total:</span><strong>R$ ${parseFloat(order.total).toFixed(2).replace('.', ',')}</strong></div>
                            <div class="flex justify-between"><span>Pago:</span><strong class="text-green-600">R$ ${parseFloat(payment.entry_amount).toFixed(2).replace('.', ',')}</strong></div>
                            <div class="flex justify-between"><span>Restante:</span><strong class="${payment.remaining_amount > 0 ? 'text-orange-600' : 'text-green-600'}">R$ ${parseFloat(payment.remaining_amount).toFixed(2).replace('.', ',')}</strong></div>
                        </div>
                        ${payment.remaining_amount > 0 ? `
                        <div class="border-t pt-3 mt-3">
                            <button onclick="openPaymentModal(${order.id}, ${payment.remaining_amount})" 
                                    class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition font-semibold">
                                💵 Registrar Pagamento Restante
                            </button>
                        </div>
                        ` : ''}
                    </div>
                </div>
                ` : ''}

                <!-- Comentários -->
                <div class="bg-white rounded-lg border p-4">
                    <h4 class="font-semibold mb-3 text-gray-900">💬 Comentários</h4>
                    
                    <!-- Formulário de Novo Comentário -->
                    <div class="mb-4 bg-gray-50 rounded-lg p-3">
                        <textarea id="comment-text-${order.id}" placeholder="Escreva seu comentário..." 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                                  rows="3"></textarea>
                        <button onclick="addComment(${order.id})" 
                                class="mt-2 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm">
                            Adicionar Comentário
                        </button>
                    </div>

                    <!-- Lista de Comentários -->
                    <div id="comments-list-${order.id}" class="space-y-3">
                        ${order.comments && order.comments.length > 0 ? order.comments.map(comment => `
                            <div class="bg-blue-50 rounded-lg p-3 border-l-4 border-blue-500">
                                <div class="flex justify-between items-start mb-1">
                                    <span class="font-semibold text-sm text-blue-900">${comment.user_name}</span>
                                    <span class="text-xs text-gray-500">${new Date(comment.created_at).toLocaleString('pt-BR')}</span>
                                </div>
                                <p class="text-sm text-gray-700">${comment.comment}</p>
                            </div>
                        `).join('') : '<p class="text-sm text-gray-500 text-center py-4">Nenhum comentário ainda.</p>'}
                    </div>
                </div>

                <!-- Log de Atendimento -->
                <div class="bg-white rounded-lg border p-4">
                    <h4 class="font-semibold mb-3 text-gray-900">📋 Log de Atendimento</h4>
                    <div class="space-y-2 max-h-96 overflow-y-auto">
                        ${order.logs && order.logs.length > 0 ? order.logs.map(log => `
                            <div class="flex items-start space-x-3 text-sm border-l-2 ${
                                log.action === 'status_changed' ? 'border-purple-500 bg-purple-50' :
                                log.action === 'comment_added' ? 'border-blue-500 bg-blue-50' :
                                'border-gray-500 bg-gray-50'
                            } rounded p-3">
                                <div class="flex-shrink-0 mt-0.5">
                                    ${log.action === 'status_changed' ? '🔄' :
                                      log.action === 'comment_added' ? '💬' :
                                      '📝'}
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start mb-1">
                                        <span class="font-semibold text-gray-900">${log.user_name}</span>
                                        <span class="text-xs text-gray-500">${new Date(log.created_at).toLocaleString('pt-BR')}</span>
                                    </div>
                                    <p class="text-gray-700">${log.description}</p>
                                </div>
                            </div>
                        `).join('') : '<p class="text-sm text-gray-500 text-center py-4">Nenhum log de atendimento ainda.</p>'}
                    </div>
                </div>
            `;
            
            document.getElementById('modal-title').textContent = `Pedido #${String(order.id).padStart(6, '0')}`;
            document.getElementById('modal-content').innerHTML = html;
        }

        function addComment(orderId) {
            const commentText = document.getElementById(`comment-text-${orderId}`).value;

            if (!commentText) {
                showNotification('Por favor, escreva um comentário', 'error');
                return;
            }

            fetch(`/kanban/order/${orderId}/comment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    comment: commentText
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Comentário adicionado com sucesso!', 'success');
                    // Recarregar detalhes do pedido
                    openOrderModal(orderId);
                } else {
                    showNotification('Erro ao adicionar comentário', 'error');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showNotification('Erro ao adicionar comentário', 'error');
            });
        }

        function downloadAllFiles(orderId) {
            window.open(`/kanban/download-files/${orderId}`, '_blank');
        }

        function openPaymentModal(orderId, remainingAmount) {
            document.getElementById('payment-order-id').value = orderId;
            document.getElementById('remaining-amount').textContent = `R$ ${parseFloat(remainingAmount).toFixed(2).replace('.', ',')}`;
            document.getElementById('payment-amount').value = parseFloat(remainingAmount).toFixed(2);
            document.getElementById('payment-amount').max = parseFloat(remainingAmount).toFixed(2);
            document.getElementById('payment-modal').classList.remove('hidden');
        }

        function closePaymentModal() {
            document.getElementById('payment-modal').classList.add('hidden');
            document.getElementById('payment-form').reset();
        }

        function submitPayment(event) {
            event.preventDefault();
            
            const orderId = document.getElementById('payment-order-id').value;
            const amount = parseFloat(document.getElementById('payment-amount').value);
            const paymentMethod = document.getElementById('payment-method').value;
            const paymentDate = document.getElementById('payment-date').value;

            fetch(`/kanban/order/${orderId}/add-payment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    amount: amount,
                    payment_method: paymentMethod,
                    payment_date: paymentDate
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Pagamento registrado com sucesso!', 'success');
                    closePaymentModal();
                    closeOrderModal();
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    showNotification(data.message || 'Erro ao registrar pagamento', 'error');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showNotification('Erro ao registrar pagamento', 'error');
            });
        }

        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} z-50`;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Fechar modal ao clicar fora
        document.getElementById('order-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeOrderModal();
            }
        });

        document.getElementById('payment-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePaymentModal();
            }
        });

        document.getElementById('delivery-request-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeliveryRequestModal();
            }
        });

        function openDeliveryRequestModal(orderId, currentDeliveryDate) {
            document.getElementById('delivery-order-id').value = orderId;
            document.getElementById('current-delivery-date').value = currentDeliveryDate;
            
            // Formatar data para exibição
            const date = new Date(currentDeliveryDate);
            document.getElementById('current-delivery-display').textContent = date.toLocaleDateString('pt-BR');
            
            // Definir data máxima para o input (um dia antes da data atual)
            const maxDate = new Date(currentDeliveryDate);
            maxDate.setDate(maxDate.getDate() - 1);
            document.getElementById('requested-delivery-date').max = maxDate.toISOString().split('T')[0];
            
            document.getElementById('delivery-request-modal').classList.remove('hidden');
        }

        function closeDeliveryRequestModal() {
            document.getElementById('delivery-request-modal').classList.add('hidden');
            document.getElementById('delivery-request-form').reset();
        }

        function submitDeliveryRequest(event) {
            event.preventDefault();
            
            const orderId = document.getElementById('delivery-order-id').value;
            const currentDeliveryDate = document.getElementById('current-delivery-date').value;
            const requestedDeliveryDate = document.getElementById('requested-delivery-date').value;
            const reason = document.getElementById('delivery-reason').value;

            // Validar se a data solicitada é anterior à atual
            if (new Date(requestedDeliveryDate) >= new Date(currentDeliveryDate)) {
                showNotification('A data solicitada deve ser anterior à data de entrega atual', 'error');
                return;
            }

            fetch(`/delivery-requests`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    order_id: orderId,
                    current_delivery_date: currentDeliveryDate,
                    requested_delivery_date: requestedDeliveryDate,
                    reason: reason
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Solicitação enviada com sucesso!', 'success');
                    closeDeliveryRequestModal();
                    closeOrderModal();
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    showNotification(data.message || 'Erro ao enviar solicitação', 'error');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showNotification('Erro ao enviar solicitação', 'error');
            });
        }


    </script>
</body>
</html>