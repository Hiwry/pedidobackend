<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edição de Pedido - Costura e Personalização</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <x-app-header />

    <div class="max-w-7xl mx-auto p-6">
        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-indigo-600">Etapa 2 de 5</span>
                <span class="text-sm text-gray-500">Costura e Personalização</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-indigo-600 h-2 rounded-full" style="width: 40%"></div>
            </div>
        </div>

        @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
        @endif

        @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="editWizard()">
            <!-- Lista de Itens - Atualização Dinâmica -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold">Itens do Pedido</h2>
                        <div class="text-sm text-gray-500">
                            <span x-text="items.length"></span> item(s)
                        </div>
                    </div>
                    
                    <!-- Resumo do Pedido -->
                    <div class="bg-indigo-50 rounded-lg p-4 mb-4">
                        <div class="text-sm text-indigo-600 font-medium mb-2">Resumo</div>
                        <div class="space-y-1 text-sm">
                            <div class="flex justify-between">
                                <span>Total de Itens:</span>
                                <span x-text="totalQuantity"></span>
                            </div>
                            <div class="flex justify-between">
                                <span>Subtotal:</span>
                                <span class="font-medium" x-text="'R$ ' + formatCurrency(totalPrice)"></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Lista de Itens -->
                    <div class="space-y-3" x-show="items.length > 0">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-indigo-400">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="font-semibold text-indigo-600" x-text="'Item ' + (index + 1)"></h3>
                                    <div class="flex gap-2">
                                        <button @click="editItem(index)" 
                                                class="text-blue-600 hover:text-blue-800 text-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <button @click="removeItem(index)" 
                                                class="text-red-600 hover:text-red-800 text-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <div class="text-sm text-gray-600 space-y-1">
                                    <p><strong>Tipo:</strong> <span x-text="item.print_type || 'Não definido'"></span></p>
                                    <p><strong>Arte:</strong> <span x-text="item.art_name || 'Não definido'"></span></p>
                                    <p><strong>Quantidade:</strong> <span x-text="item.quantity || 0"></span></p>
                                    <p><strong>Tecido:</strong> <span x-text="item.fabric || 'Não definido'"></span></p>
                                    <p><strong>Cor:</strong> <span x-text="item.color || 'Não definido'"></span></p>
                                    <p><strong>Preço Unit.:</strong> <span x-text="'R$ ' + formatCurrency(item.unit_price || 0)"></span></p>
                                    <p><strong>Total:</strong> <span class="font-medium text-indigo-600" x-text="'R$ ' + formatCurrency((item.quantity || 0) * (item.unit_price || 0))"></span></p>
                                </div>
                            </div>
                        </template>
                        </div>
                    
                    <!-- Estado Vazio -->
                    <div x-show="items.length === 0" class="text-center text-gray-500 py-8">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <p>Nenhum item adicionado ainda</p>
                        <p class="text-sm mt-2">Use o formulário ao lado para adicionar itens</p>
                        </div>
                </div>
            </div>

            <!-- Formulário de Edição -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold">
                            <span x-show="editingIndex === null">Adicionar Novo Item</span>
                            <span x-show="editingIndex !== null">Editar Item <span x-text="editingIndex + 1"></span></span>
                        </h2>
                        <button x-show="editingIndex !== null" 
                                @click="cancelEdit()"
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                            Cancelar Edição
                                        </button>
                                    </div>
                                    
                    <form @submit.prevent="saveItem()" class="space-y-6">
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                        <!-- Tipo de Personalização -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Personalização *</label>
                                <select x-model="currentItem.print_type" required
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                <option value="">Selecione...</option>
                                    <option value="BORDADO">Bordado</option>
                                    <option value="SUBLIMACAO">Sublimação</option>
                                    <option value="SERIGRAFIA">Serigrafia</option>
                                    <option value="VINIL">Vinil</option>
                                    <option value="DIGITAL">Digital</option>
                                    <option value="DTF">DTF</option>
                                            </select>
                                        </div>

                                        <!-- Nome da Arte -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Nome da Arte *</label>
                                <input type="text" x-model="currentItem.art_name" required
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                                   placeholder="Ex: Logo Empresa XYZ">
                                        </div>

                                        <!-- Quantidade -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Quantidade *</label>
                                <input type="number" x-model.number="currentItem.quantity" min="1" required
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        </div>

                                        <!-- Tecido -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Tecido *</label>
                                <select x-model="currentItem.fabric" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">Selecione...</option>
                                <option value="Algodão - PP">Algodão - PP</option>
                                <option value="Algodão - P">Algodão - P</option>
                                <option value="Algodão - M">Algodão - M</option>
                                <option value="Algodão - G">Algodão - G</option>
                                <option value="Algodão - GG">Algodão - GG</option>
                                <option value="Algodão - XGG">Algodão - XGG</option>
                                <option value="Malha - PP">Malha - PP</option>
                                <option value="Malha - P">Malha - P</option>
                                <option value="Malha - M">Malha - M</option>
                                <option value="Malha - G">Malha - G</option>
                                <option value="Malha - GG">Malha - GG</option>
                                <option value="Malha - XGG">Malha - XGG</option>
                                    <option value="Poliéster - DRY FIT">Poliéster - DRY FIT</option>
                            </select>
                        </div>

                            <!-- Cor -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cor *</label>
                                <select x-model="currentItem.color" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">Selecione...</option>
                                <option value="Branco">Branco</option>
                                <option value="Preto">Preto</option>
                                <option value="Azul">Azul</option>
                                <option value="Vermelho">Vermelho</option>
                                <option value="Verde">Verde</option>
                                <option value="Amarelo">Amarelo</option>
                                <option value="Rosa">Rosa</option>
                                <option value="Cinza">Cinza</option>
                                <option value="Marrom">Marrom</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>

                            <!-- Preço Unitário -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Preço Unitário (R$) *</label>
                                <input type="number" x-model.number="currentItem.unit_price" step="0.01" min="0" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                        </div>

                        <!-- Botões de Ação -->
                        <div class="flex justify-end gap-3 pt-4 border-t">
                            <button type="button" @click="resetForm()"
                                    class="px-6 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                                Limpar
                            </button>
                            <button type="submit" 
                                    class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                <span x-show="editingIndex === null">Adicionar Item</span>
                                <span x-show="editingIndex !== null">Salvar Alterações</span>
                            </button>
                        </div>
                    </form>
                    </div>

                <!-- Navegação -->
                <div class="flex justify-between mt-6">
                    <a href="{{ route('orders.edit-wizard.client') }}" 
                       class="px-6 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                        ← Voltar
                    </a>
                    
                    <button @click="proceedToNext()" 
                            :disabled="items.length === 0"
                            :class="items.length === 0 ? 'bg-gray-300 cursor-not-allowed' : 'bg-indigo-600 hover:bg-indigo-700'"
                            class="px-6 py-2 text-white rounded-md">
                        Próximo →
                    </button>
                </div>
            </div>
        </div>
    </div>

    @php
        $itemsData = $order->items->map(function($item) {
            return [
                'id' => $item->id,
                'print_type' => $item->print_type,
                'art_name' => $item->art_name,
                'quantity' => $item->quantity,
                'fabric' => $item->fabric,
                'color' => $item->color,
                'unit_price' => $item->unit_price,
                'total_price' => $item->total_price
            ];
        })->toArray();
    @endphp

    <script>
        function editWizard() {
            return {
                items: @json($itemsData),
                editingIndex: null,
                currentItem: {
                    print_type: '',
                    art_name: '',
                    quantity: 1,
                    fabric: '',
                    color: '',
                    unit_price: 0
                },

                get totalQuantity() {
                    return this.items.reduce((sum, item) => sum + (item.quantity || 0), 0);
                },

                get totalPrice() {
                    return this.items.reduce((sum, item) => sum + ((item.quantity || 0) * (item.unit_price || 0)), 0);
                },

                formatCurrency(value) {
                    return parseFloat(value).toFixed(2).replace('.', ',');
                },

                saveItem() {
                    console.log('=== SALVANDO ITEM ===');
                    console.log('Editing index:', this.editingIndex);
                    console.log('Current item:', this.currentItem);
                    console.log('Items before:', JSON.stringify(this.items, null, 2));
                    
                    if (this.editingIndex !== null) {
                        // Atualizar item existente
                        console.log('Atualizando item existente no índice:', this.editingIndex);
                        this.items[this.editingIndex] = { ...this.currentItem };
                        this.editingIndex = null;
                    } else {
                        // Adicionar novo item
                        console.log('Adicionando novo item');
                        this.items.push({ ...this.currentItem });
                    }
                    
                    console.log('Items after:', JSON.stringify(this.items, null, 2));
                    
                    this.resetForm();
                    this.updateServerData();
                },

                editItem(index) {
                    console.log('=== EDITANDO ITEM ===');
                    console.log('Index:', index);
                    console.log('Item to edit:', this.items[index]);
                    
                    this.currentItem = { ...this.items[index] };
                    this.editingIndex = index;
                    
                    console.log('Current item set to:', this.currentItem);
                    console.log('Editing index set to:', this.editingIndex);
                },

                removeItem(index) {
                    console.log('=== REMOVENDO ITEM ===');
                    console.log('Index to remove:', index);
                    console.log('Item to remove:', this.items[index]);
                    
                    if (confirm('Tem certeza que deseja remover este item?')) {
                        this.items.splice(index, 1);
                        console.log('Item removido. Items restantes:', this.items);
                        this.updateServerData();
                    }
                },

                cancelEdit() {
                    this.editingIndex = null;
                    this.resetForm();
                },

                resetForm() {
                    this.currentItem = {
                        print_type: '',
                        art_name: '',
                        quantity: 1,
                        fabric: '',
                        color: '',
                        unit_price: 0
                    };
                },

                updateServerData() {
                    console.log('=== ATUALIZANDO DADOS NO SERVIDOR ===');
                    console.log('Items to send:', JSON.stringify(this.items, null, 2));
                    
                    // Enviar dados atualizados para o servidor via AJAX
                    fetch('{{ route("orders.edit-wizard.sewing") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            action: 'update_items',
                            items: this.items
                        })
                    })
                    .then(response => {
                        console.log('Response status:', response.status);
                        console.log('Response headers:', response.headers);
                        return response.json();
                    })
                    .then(data => {
                        console.log('Response data:', data);
                        if (data.success) {
                            console.log('✅ Itens atualizados no servidor com sucesso');
                        } else {
                            console.error('❌ Erro na resposta do servidor:', data);
                        }
                    })
                    .catch(error => {
                        console.error('❌ Erro ao atualizar itens:', error);
                    });
                },

                proceedToNext() {
                    if (this.items.length === 0) {
                        alert('Adicione pelo menos um item antes de continuar.');
                        return;
                    }
                    
                    // Salvar dados na sessão e prosseguir
                    this.updateServerData();
                    
                    // Redirecionar para próxima etapa
                    window.location.href = '{{ route("orders.edit-wizard.customization") }}';
                }
            }
        }
    </script>
</body>
</html>