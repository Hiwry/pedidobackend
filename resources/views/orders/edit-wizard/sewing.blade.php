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

    <div class="max-w-4xl mx-auto px-4 py-6">
        <!-- Progress Bar -->
                <div class="mb-6">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center space-x-2">
                    <div class="w-6 h-6 bg-indigo-600 text-white rounded-full flex items-center justify-center text-xs font-medium">2</div>
                    <div>
                        <span class="text-base font-medium text-indigo-600">Costura e Personalização</span>
                        <p class="text-xs text-gray-500">Etapa 2 de 5</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-xs text-gray-500">Progresso</div>
                    <div class="text-sm font-medium text-indigo-600">40%</div>
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-1.5">
                <div class="bg-indigo-600 h-1.5 rounded-full transition-all duration-500 ease-out" style="width: 40%"></div>
            </div>
        </div>

        @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
        @endif

        <!-- Main Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-indigo-100 rounded-md flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg font-semibold text-gray-900">Editar Itens do Pedido</h1>
                        <p class="text-sm text-gray-600">Gerencie os itens de costura e personalização</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <!-- Lista de Itens Atuais -->
                <div class="mb-8">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Itens Atuais do Pedido</h2>
                    
                    @if($order->items->count() > 0)
                        <div class="space-y-4">
                            @foreach($order->items as $item)
                            <div class="bg-gray-50 rounded-md p-4 border border-gray-200">
                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 bg-indigo-100 rounded-md flex items-center justify-center">
                                            <span class="text-xs font-medium text-indigo-600">{{ $item->item_number }}</span>
                                        </div>
                                        <h3 class="text-sm font-medium text-gray-900">Item {{ $item->item_number }}</h3>
                                        </div>
                                    <div class="flex space-x-2">
                                        <button type="button" onclick="editItem({{ $item->id }})" 
                                                class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700">
                                            Editar
                                        </button>
                                        <form method="POST" action="{{ route('orders.edit-wizard.sewing') }}" class="inline">
                                            @csrf
                                            <input type="hidden" name="action" value="delete_item">
                                            <input type="hidden" name="item_id" value="{{ $item->id }}">
                                            <button type="submit" onclick="return confirm('Deseja remover este item?')" 
                                                    class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700">
                                                Remover
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                            <div>
                                        <span class="text-gray-600">Personalização:</span>
                                        <p class="font-medium">{{ $item->print_type }}</p>
                                    </div>
                                        <div>
                                        <span class="text-gray-600">Tecido:</span>
                                        <p class="font-medium">{{ $item->fabric }}</p>
                                        </div>
                                        <div>
                                        <span class="text-gray-600">Cor:</span>
                                        <p class="font-medium">{{ $item->color }}</p>
                                        </div>
                                        <div>
                                        <span class="text-gray-600">Quantidade:</span>
                                        <p class="font-medium">{{ $item->quantity }} peças</p>
                            </div>
                        </div>
                                <div class="mt-3 pt-3 border-t border-gray-200">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Valor Unitário:</span>
                                        <span class="text-sm font-medium">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</span>
                                        </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-900">Total:</span>
                                        <span class="text-lg font-bold text-indigo-600">R$ {{ number_format($item->total_price, 2, ',', '.') }}</span>
                                    </div>
                                </div>
                        </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-6 pt-4 border-t border-gray-200">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Total de Itens:</span>
                                <span class="text-sm font-medium">{{ $order->items->count() }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Total de Peças:</span>
                                <span class="text-sm font-medium">{{ $order->total_items }}</span>
                            </div>
                            <div class="flex justify-between items-center pt-2 border-t border-gray-200">
                                <span class="text-lg font-semibold text-gray-900">Subtotal:</span>
                                <span class="text-xl font-bold text-indigo-600">R$ {{ number_format($order->subtotal, 2, ',', '.') }}</span>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <p class="text-sm text-gray-500 mb-2">Nenhum item encontrado</p>
                            <p class="text-xs text-gray-400">Os itens do pedido serão exibidos aqui</p>
                        </div>
                    @endif
                            </div>

                <!-- Botões de Navegação -->
                <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                    <a href="{{ route('orders.edit-wizard.client') }}" 
                       class="flex items-center px-4 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-md transition-all text-sm font-medium">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Voltar
                    </a>
                    <a href="{{ route('orders.edit-wizard.customization') }}" 
                       class="flex items-center px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition-all text-sm font-medium">
                        Continuar
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Edição de Item -->
    <div id="editItemModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Editar Item</h3>
                    <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        </button>
                    </div>
                
                <form method="POST" action="{{ route('orders.edit-wizard.sewing') }}" id="editItemForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="action" value="update_item">
                    <input type="hidden" name="editing_item_id" id="editingItemId">
                    
                    <div class="space-y-6">
                        <!-- Personalização -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Personalização *</label>
                            <div class="grid grid-cols-2 gap-3" id="personalizacao-options">
                                <!-- Será preenchido via JavaScript -->
                            </div>
                        </div>

                        <!-- Tecido -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tecido *</label>
                            <select name="tecido" id="tecido" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-indigo-500" required>
                                <option value="">Selecione o tecido</option>
                            </select>
                        </div>

                        <!-- Tipo de Tecido -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Tecido</label>
                            <select name="tipo_tecido" id="tipo_tecido" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                <option value="">Selecione o tipo</option>
                            </select>
                        </div>

                        <!-- Cor -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cor *</label>
                            <select name="cor" id="cor" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-indigo-500" required>
                                <option value="">Selecione a cor</option>
                            </select>
                    </div>

                        <!-- Tipo de Corte -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Corte *</label>
                            <select name="tipo_corte" id="tipo_corte" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-indigo-500" required>
                                <option value="">Selecione o tipo de corte</option>
                            </select>
                        </div>

                        <!-- Detalhe -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Detalhe</label>
                            <select name="detalhe" id="detalhe" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                <option value="">Selecione o detalhe</option>
                            </select>
                    </div>

                        <!-- Gola -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Gola *</label>
                            <select name="gola" id="gola" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-indigo-500" required>
                                <option value="">Selecione a gola</option>
                            </select>
                        </div>

                        <!-- Tamanhos -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tamanhos *</label>
                            <div class="grid grid-cols-4 gap-3" id="tamanhos-container">
                                <!-- Será preenchido via JavaScript -->
                            </div>
                        </div>

                        <!-- Preço Unitário -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Preço Unitário *</label>
                            <input type="number" name="unit_price" id="unit_price" step="0.01" min="0" 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-indigo-500" required>
                        </div>

                        <!-- Imagem de Capa -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Imagem de Capa</label>
                            <input type="file" name="item_cover_image" id="item_cover_image" accept="image/*" 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    </div>
                </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="closeEditModal()" 
                                class="px-4 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-md transition-all">
                            Cancelar
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            Salvar Alterações
                    </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script>
        let currentItemData = null;
        let productOptions = {};

        // Carregar opções de produtos
        async function loadProductOptions() {
            try {
                const response = await fetch('/api/product-options', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                productOptions = await response.json();
                populateSelects();
            } catch (error) {
                console.error('Erro ao carregar opções:', error);
            }
        }

        function populateSelects() {
            // Personalização
            const personalizacaoContainer = document.getElementById('personalizacao-options');
            personalizacaoContainer.innerHTML = '';
            
            if (productOptions.personalizacao) {
                productOptions.personalizacao.forEach(option => {
                    const div = document.createElement('div');
                    div.className = 'flex items-center';
                    div.innerHTML = `
                        <input type="checkbox" name="personalizacao[]" value="${option.id}" id="personalizacao_${option.id}" 
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="personalizacao_${option.id}" class="ml-2 text-sm text-gray-700">${option.name}</label>
                    `;
                    personalizacaoContainer.appendChild(div);
                });
            }

            // Tecido
            populateSelect('tecido', productOptions.tecido);
            populateSelect('tipo_tecido', productOptions.tipo_tecido);
            populateSelect('cor', productOptions.cor);
            populateSelect('tipo_corte', productOptions.tipo_corte);
            populateSelect('detalhe', productOptions.detalhe);
            populateSelect('gola', productOptions.gola);

            // Tamanhos
            const tamanhosContainer = document.getElementById('tamanhos-container');
            tamanhosContainer.innerHTML = '';
            
            if (productOptions.tamanho && productOptions.tamanho.length > 0) {
                productOptions.tamanho.forEach(size => {
                    const div = document.createElement('div');
                    div.innerHTML = `
                        <label class="block text-xs text-gray-600 mb-1">${size.name}</label>
                        <input type="number" name="tamanhos[${size.name}]" min="0" value="0" 
                               class="w-full border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    `;
                    tamanhosContainer.appendChild(div);
                });
            } else {
                // Fallback: criar campos básicos se não houver dados da API
                const tamanhosBasicos = ['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3', 'Especial'];
                tamanhosBasicos.forEach(tamanho => {
                    const div = document.createElement('div');
                    div.innerHTML = `
                        <label class="block text-xs text-gray-600 mb-1">${tamanho}</label>
                        <input type="number" name="tamanhos[${tamanho}]" min="0" value="0" 
                               class="w-full border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    `;
                    tamanhosContainer.appendChild(div);
                });
            }
        }

        function populateSelect(selectId, options) {
            const select = document.getElementById(selectId);
            if (!select || !options) return;

            // Limpar opções existentes (exceto a primeira)
            while (select.children.length > 1) {
                select.removeChild(select.lastChild);
            }

            options.forEach(option => {
                const optionElement = document.createElement('option');
                optionElement.value = option.id;
                optionElement.textContent = option.name;
                select.appendChild(optionElement);
            });
        }

    function editItem(itemId) {
            // Buscar dados do item
            fetch(`/api/order-items/${itemId}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    currentItemData = data;
                    populateEditForm(data);
                    document.getElementById('editItemModal').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Erro ao carregar item:', error);
                    alert('Erro ao carregar dados do item: ' + error.message);
                });
        }

        function populateEditForm(item) {
            document.getElementById('editingItemId').value = item.id;
            document.getElementById('unit_price').value = item.unit_price;

            // Preencher personalização
            if (item.print_type) {
                const personalizacoes = item.print_type.split(', ');
                personalizacoes.forEach(p => {
                    const checkbox = document.querySelector(`input[name="personalizacao[]"][value="${p}"]`);
                    if (checkbox) checkbox.checked = true;
                });
        }

        // Preencher outros campos
            setSelectValue('tecido', item.fabric);
            setSelectValue('cor', item.color);
            setSelectValue('tipo_corte', item.model);
            setSelectValue('detalhe', item.detail);
            setSelectValue('gola', item.collar);

        // Preencher tamanhos
            if (item.sizes) {
                let sizes;
                if (typeof item.sizes === 'string') {
                    sizes = JSON.parse(item.sizes);
                } else {
                    sizes = item.sizes;
                }
                
                Object.entries(sizes).forEach(([size, quantity]) => {
                    const input = document.querySelector(`input[name="tamanhos[${size}]"]`);
                    if (input) input.value = quantity;
                });
            }
    }

    function setSelectValue(selectId, value) {
        const select = document.getElementById(selectId);
        if (!select) return;

        // Procurar por correspondência parcial
        for (let option of select.options) {
            if (option.textContent.includes(value) || value.includes(option.textContent)) {
                option.selected = true;
                break;
            }
        }
    }

        function closeEditModal() {
            document.getElementById('editItemModal').classList.add('hidden');
            document.getElementById('editItemForm').reset();
        }

        // Carregar opções quando a página carregar
        document.addEventListener('DOMContentLoaded', function() {
            loadProductOptions();
        });
    </script>
</body>
</html>