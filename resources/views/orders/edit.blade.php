<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Editar Pedido #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-100">
    <x-app-header />

    <div class="max-w-7xl mx-auto p-6" x-data="orderEditForm()">
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

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-semibold">Editar Pedido #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</h1>
                    <p class="text-sm text-gray-600 mt-1">Cliente: {{ $order->client->name }}</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('orders.show', $order->id) }}" 
                       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                        ← Voltar
                    </a>
                </div>
            </div>

            <!-- Seleção de Etapas para Edição -->
            <div class="mb-8 p-4 bg-blue-50 rounded-lg">
                <h3 class="text-lg font-medium mb-4">Selecione as etapas que deseja editar:</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" x-model="selectedSteps.client" class="rounded">
                        <span class="text-sm font-medium">Dados do Cliente</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" x-model="selectedSteps.items" class="rounded">
                        <span class="text-sm font-medium">Itens do Pedido</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" x-model="selectedSteps.personalization" class="rounded">
                        <span class="text-sm font-medium">Personalização</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" x-model="selectedSteps.payment" class="rounded">
                        <span class="text-sm font-medium">Pagamento</span>
                    </label>
                </div>
            </div>

            <form method="POST" action="{{ route('orders.update', $order->id) }}" @submit="submitForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <!-- Dados do Cliente -->
                <div x-show="selectedSteps.client" class="mb-8 p-6 border rounded-lg">
                    <h3 class="text-lg font-medium mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Dados do Cliente
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nome Completo *</label>
                            <input type="text" name="client_name" value="{{ $order->client->name }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Telefone Principal *</label>
                            <input type="text" name="client_phone_primary" value="{{ $order->client->phone_primary }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" name="client_email" value="{{ $order->client->email }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">CPF/CNPJ</label>
                            <input type="text" name="client_cpf_cnpj" value="{{ $order->client->cpf_cnpj }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Endereço</label>
                            <input type="text" name="client_address" value="{{ $order->client->address }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                <!-- Itens do Pedido -->
                <div x-show="selectedSteps.items" class="mb-8 p-6 border rounded-lg">
                    <h3 class="text-lg font-medium mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Itens do Pedido
                    </h3>
                    
                    <div class="space-y-6">
                        @foreach($order->items as $index => $item)
                        <div class="bg-gray-50 p-6 rounded-lg border">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="font-medium text-lg">Item {{ $index + 1 }}</h4>
                                <span class="text-sm text-gray-500">ID: {{ $item->id }}</span>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <!-- Tipo de Personalização -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Personalização *</label>
                                    <select name="items[{{ $index }}][print_type]" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                            onchange="updatePersonalizationType({{ $index }}, this.value)">
                                        <option value="">Selecione...</option>
                                        <option value="BORDADO" {{ $item->print_type == 'BORDADO' ? 'selected' : '' }}>Bordado</option>
                                        <option value="SUBLIMACAO" {{ $item->print_type == 'SUBLIMACAO' ? 'selected' : '' }}>Sublimação</option>
                                        <option value="SERIGRAFIA" {{ $item->print_type == 'SERIGRAFIA' ? 'selected' : '' }}>Serigrafia</option>
                                        <option value="VINIL" {{ $item->print_type == 'VINIL' ? 'selected' : '' }}>Vinil</option>
                                        <option value="DIGITAL" {{ $item->print_type == 'DIGITAL' ? 'selected' : '' }}>Digital</option>
                                    </select>
                                </div>

                                <!-- Nome da Arte -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nome da Arte *</label>
                                    <input type="text" name="items[{{ $index }}][art_name]" value="{{ $item->art_name }}" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                           placeholder="Ex: Logo Empresa XYZ">
                                </div>

                                <!-- Quantidade -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantidade *</label>
                                    <input type="number" name="items[{{ $index }}][quantity]" value="{{ $item->quantity }}" min="1" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                           onchange="calculateItemTotal({{ $index }})">
                                </div>

                                <!-- Tecido -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tecido *</label>
                                    <select name="items[{{ $index }}][fabric]" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        <option value="">Selecione...</option>
                                        <option value="Algodão - PP" {{ $item->fabric == 'Algodão - PP' ? 'selected' : '' }}>Algodão - PP</option>
                                        <option value="Algodão - P" {{ $item->fabric == 'Algodão - P' ? 'selected' : '' }}>Algodão - P</option>
                                        <option value="Algodão - M" {{ $item->fabric == 'Algodão - M' ? 'selected' : '' }}>Algodão - M</option>
                                        <option value="Algodão - G" {{ $item->fabric == 'Algodão - G' ? 'selected' : '' }}>Algodão - G</option>
                                        <option value="Algodão - GG" {{ $item->fabric == 'Algodão - GG' ? 'selected' : '' }}>Algodão - GG</option>
                                        <option value="Algodão - XGG" {{ $item->fabric == 'Algodão - XGG' ? 'selected' : '' }}>Algodão - XGG</option>
                                        <option value="Malha - PP" {{ $item->fabric == 'Malha - PP' ? 'selected' : '' }}>Malha - PP</option>
                                        <option value="Malha - P" {{ $item->fabric == 'Malha - P' ? 'selected' : '' }}>Malha - P</option>
                                        <option value="Malha - M" {{ $item->fabric == 'Malha - M' ? 'selected' : '' }}>Malha - M</option>
                                        <option value="Malha - G" {{ $item->fabric == 'Malha - G' ? 'selected' : '' }}>Malha - G</option>
                                        <option value="Malha - GG" {{ $item->fabric == 'Malha - GG' ? 'selected' : '' }}>Malha - GG</option>
                                        <option value="Malha - XGG" {{ $item->fabric == 'Malha - XGG' ? 'selected' : '' }}>Malha - XGG</option>
                                    </select>
                                </div>

                                <!-- Cor -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Cor *</label>
                                    <select name="items[{{ $index }}][color]" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        <option value="">Selecione...</option>
                                        <option value="Branco" {{ $item->color == 'Branco' ? 'selected' : '' }}>Branco</option>
                                        <option value="Preto" {{ $item->color == 'Preto' ? 'selected' : '' }}>Preto</option>
                                        <option value="Azul" {{ $item->color == 'Azul' ? 'selected' : '' }}>Azul</option>
                                        <option value="Vermelho" {{ $item->color == 'Vermelho' ? 'selected' : '' }}>Vermelho</option>
                                        <option value="Verde" {{ $item->color == 'Verde' ? 'selected' : '' }}>Verde</option>
                                        <option value="Amarelo" {{ $item->color == 'Amarelo' ? 'selected' : '' }}>Amarelo</option>
                                        <option value="Rosa" {{ $item->color == 'Rosa' ? 'selected' : '' }}>Rosa</option>
                                        <option value="Cinza" {{ $item->color == 'Cinza' ? 'selected' : '' }}>Cinza</option>
                                        <option value="Marrom" {{ $item->color == 'Marrom' ? 'selected' : '' }}>Marrom</option>
                                        <option value="Outro" {{ $item->color == 'Outro' ? 'selected' : '' }}>Outro</option>
                                    </select>
                                </div>

                                <!-- Preço Unitário -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Preço Unitário (R$) *</label>
                                    <input type="number" name="items[{{ $index }}][unit_price]" value="{{ $item->unit_price }}" step="0.01" min="0" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                           onchange="calculateItemTotal({{ $index }})">
                                </div>
                            </div>

                            <!-- Campos específicos por tipo de personalização -->
                            <div id="personalization-fields-{{ $index }}" class="mt-4">
                                <!-- Será preenchido via JavaScript baseado no tipo selecionado -->
                            </div>

                            <!-- Total do Item -->
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-700">Total do Item:</span>
                                    <span id="item-total-{{ $index }}" class="text-lg font-bold text-green-600">
                                        R$ {{ number_format($item->unit_price * $item->quantity, 2, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Personalização -->
                <div x-show="selectedSteps.personalization" class="mb-8 p-6 border rounded-lg">
                    <h3 class="text-lg font-medium mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                        </svg>
                        Personalização
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Contrato *</label>
                            <select name="contract_type" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">Selecione...</option>
                                <option value="costura" {{ $order->contract_type == 'costura' ? 'selected' : '' }}>Costura</option>
                                <option value="personalizacao" {{ $order->contract_type == 'personalizacao' ? 'selected' : '' }}>Personalização</option>
                                <option value="ambos" {{ $order->contract_type == 'ambos' ? 'selected' : '' }}>Ambos</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Vendedor</label>
                            <input type="text" name="seller" value="{{ $order->seller }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                   placeholder="Nome do vendedor">
                        </div>
                    </div>
                </div>

                <!-- Pagamento e Valores -->
                <div x-show="selectedSteps.payment" class="mb-8 p-6 border rounded-lg">
                    <h3 class="text-lg font-medium mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        Pagamento e Valores
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Data de Entrega *</label>
                            <input type="date" name="delivery_date" required
                                   value="{{ $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('Y-m-d') : '' }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Subtotal (R$) *</label>
                            <input type="number" name="subtotal" value="{{ $order->subtotal }}" step="0.01" min="0" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                   onchange="calculateTotal()">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Desconto (R$)</label>
                            <input type="number" name="discount" value="{{ $order->discount }}" step="0.01" min="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                   onchange="calculateTotal()">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Taxa de Entrega (R$)</label>
                            <input type="number" name="delivery_fee" value="{{ $order->delivery_fee }}" step="0.01" min="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                   onchange="calculateTotal()">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Total (R$) *</label>
                            <input type="number" name="total" value="{{ $order->total }}" step="0.01" min="0" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-gray-100"
                                   readonly>
                        </div>
                    </div>
                </div>

                <!-- Observações -->
                <div class="mb-8 p-6 border rounded-lg">
                    <h3 class="text-lg font-medium mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Observações
                    </h3>
                    <textarea name="notes" rows="4" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                              placeholder="Observações sobre o pedido...">{{ $order->notes }}</textarea>
                </div>

                <!-- Motivo da Edição -->
                <div class="mb-8 p-6 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <h3 class="text-lg font-medium mb-4 flex items-center text-yellow-800">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        Motivo da Edição *
                    </h3>
                    <textarea name="edit_reason" rows="3" required
                              class="w-full px-3 py-2 border border-yellow-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500"
                              placeholder="Explique o motivo das alterações..."></textarea>
                </div>

                <!-- Botões de Ação -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('orders.show', $order->id) }}" 
                       class="px-6 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Solicitar Edição
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function orderEditForm() {
            return {
                selectedSteps: {
                    client: false,
                    items: false,
                    personalization: false,
                    payment: false
                },
                
                submitForm(event) {
                    // Verificar se pelo menos uma etapa foi selecionada
                    const hasSelectedSteps = Object.values(this.selectedSteps).some(selected => selected);
                    
                    if (!hasSelectedSteps) {
                        event.preventDefault();
                        alert('Por favor, selecione pelo menos uma etapa para editar.');
                        return false;
                    }
                    
                    // Adicionar as etapas selecionadas ao formulário
                    Object.keys(this.selectedSteps).forEach(step => {
                        if (this.selectedSteps[step]) {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'selected_steps[]';
                            input.value = step;
                            event.target.appendChild(input);
                        }
                    });
                }
            }
        }

        // Funções para cálculos e interações
        function calculateItemTotal(index) {
            const quantity = parseFloat(document.querySelector(`input[name="items[${index}][quantity]"]`).value) || 0;
            const unitPrice = parseFloat(document.querySelector(`input[name="items[${index}][unit_price]"]`).value) || 0;
            const total = quantity * unitPrice;
            
            document.getElementById(`item-total-${index}`).textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
            calculateTotal();
        }

        function calculateTotal() {
            const subtotal = parseFloat(document.querySelector('input[name="subtotal"]').value) || 0;
            const discount = parseFloat(document.querySelector('input[name="discount"]').value) || 0;
            const deliveryFee = parseFloat(document.querySelector('input[name="delivery_fee"]').value) || 0;
            
            const total = subtotal - discount + deliveryFee;
            document.querySelector('input[name="total"]').value = total.toFixed(2);
        }

        function updatePersonalizationType(index, type) {
            const container = document.getElementById(`personalization-fields-${index}`);
            
            // Limpar campos existentes
            container.innerHTML = '';
            
            if (type === 'SUBLIMACAO') {
                container.innerHTML = `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tamanho da Sublimação</label>
                            <select name="items[${index}][sublimation_size]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">Selecione...</option>
                                <option value="10x10">10x10 cm</option>
                                <option value="15x15">15x15 cm</option>
                                <option value="20x20">20x20 cm</option>
                                <option value="25x25">25x25 cm</option>
                                <option value="30x30">30x30 cm</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Local da Sublimação</label>
                            <select name="items[${index}][sublimation_location]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">Selecione...</option>
                                <option value="frente">Frente</option>
                                <option value="costas">Costas</option>
                                <option value="manga">Manga</option>
                                <option value="ombro">Ombro</option>
                            </select>
                        </div>
                    </div>
                `;
            } else if (type === 'SERIGRAFIA') {
                container.innerHTML = `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Número de Cores</label>
                            <select name="items[${index}][serigraphy_colors]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">Selecione...</option>
                                <option value="1">1 Cor</option>
                                <option value="2">2 Cores</option>
                                <option value="3">3 Cores</option>
                                <option value="4">4 Cores</option>
                                <option value="5">5+ Cores</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tamanho da Arte</label>
                            <select name="items[${index}][serigraphy_size]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">Selecione...</option>
                                <option value="pequeno">Pequeno (até 15cm)</option>
                                <option value="medio">Médio (15-25cm)</option>
                                <option value="grande">Grande (25cm+)</option>
                            </select>
                        </div>
                    </div>
                `;
            } else if (type === 'BORDADO') {
                container.innerHTML = `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Número de Pontos</label>
                            <select name="items[${index}][embroidery_points]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">Selecione...</option>
                                <option value="1000">Até 1.000 pontos</option>
                                <option value="5000">1.001 - 5.000 pontos</option>
                                <option value="10000">5.001 - 10.000 pontos</option>
                                <option value="15000">10.001 - 15.000 pontos</option>
                                <option value="20000">15.001+ pontos</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tamanho do Bordado</label>
                            <select name="items[${index}][embroidery_size]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">Selecione...</option>
                                <option value="pequeno">Pequeno (até 8cm)</option>
                                <option value="medio">Médio (8-15cm)</option>
                                <option value="grande">Grande (15cm+)</option>
                            </select>
                        </div>
                    </div>
                `;
            }
        }

        // Inicializar campos específicos para cada item existente
        document.addEventListener('DOMContentLoaded', function() {
            @foreach($order->items as $index => $item)
                updatePersonalizationType({{ $index }}, '{{ $item->print_type }}');
            @endforeach
        });
    </script>
</body>
</html>
