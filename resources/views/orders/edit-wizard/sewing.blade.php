@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Formulário de Edição -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Costura e Personalização</h2>
                    <p class="text-gray-600">Etapa 2 de 5 - Edição</p>
        </div>

                <form id="sewing-form" method="POST" action="{{ route('orders.edit-wizard.sewing', $order->id) }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="form-action" id="form-action" value="add_item">
                    <input type="hidden" name="editing-item-id" id="editing-item-id" value="">

                    <!-- Título do Formulário -->
                    <div class="mb-6">
                        <h3 id="form-title" class="text-lg font-semibold text-gray-900 flex items-center">
                            <span class="mr-2">➕</span>
                            Adicionar Item
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">Configure os detalhes do item de costura</p>
        </div>

                    <!-- Personalização -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Personalização *
                        </label>
                        <p class="text-sm text-gray-600 mb-3">Selecione uma ou mais opções de personalização</p>
                        <div id="personalizacao-options" class="space-y-2">
                            <!-- Opções serão carregadas via JavaScript -->
                </div>
            </div>

                    <!-- Tecido -->
                    <div class="mb-6">
                        <label for="tecido" class="block text-sm font-medium text-gray-700 mb-2">
                            Tecido *
                        </label>
                        <select id="tecido" name="tecido" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Selecione o tecido</option>
                        </select>
                                    </div>
                                    
                    <!-- Tipo de Tecido -->
                    <div class="mb-6">
                        <label for="tipo_tecido" class="block text-sm font-medium text-gray-700 mb-2">
                            Tipo de Tecido
                        </label>
                        <select id="tipo_tecido" name="tipo_tecido" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Selecione o tipo</option>
                                            </select>
                                        </div>

                    <!-- Cor do Tecido -->
                    <div class="mb-6">
                        <label for="cor" class="block text-sm font-medium text-gray-700 mb-2">
                            Cor *
                        </label>
                        <select id="cor" name="cor" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Selecione a cor</option>
                        </select>
                                        </div>

                    <!-- Modelo e Detalhes -->
                    <div class="mb-6">
                        <label for="tipo_corte" class="block text-sm font-medium text-gray-700 mb-2">
                            Tipo de Corte *
                        </label>
                        <select id="tipo_corte" name="tipo_corte" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Selecione o tipo de corte</option>
                        </select>
                                        </div>

                    <div class="mb-6">
                        <label for="detalhe" class="block text-sm font-medium text-gray-700 mb-2">
                            Detalhe
                        </label>
                        <select id="detalhe" name="detalhe" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Selecione o detalhe</option>
                                            </select>
                                        </div>

                    <div class="mb-6">
                        <label for="gola" class="block text-sm font-medium text-gray-700 mb-2">
                            Gola *
                        </label>
                        <select id="gola" name="gola" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Selecione a gola</option>
                                            </select>
                                        </div>

                    <!-- Tamanhos e Quantidades -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Tamanhos e Quantidades *
                        </label>
                        <div class="grid grid-cols-5 gap-3">
                                        <div>
                                <label class="block text-xs text-gray-600 mb-1">PP</label>
                                <input type="number" name="tamanhos[PP]" min="0" value="0" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                        </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">P</label>
                                <input type="number" name="tamanhos[P]" min="0" value="0" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">M</label>
                                <input type="number" name="tamanhos[M]" min="0" value="0" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">G</label>
                                <input type="number" name="tamanhos[G]" min="0" value="0" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    </div>
                                        <div>
                                <label class="block text-xs text-gray-600 mb-1">GG</label>
                                <input type="number" name="tamanhos[GG]" min="0" value="0" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                        </div>
                                        <div>
                                <label class="block text-xs text-gray-600 mb-1">EXG</label>
                                <input type="number" name="tamanhos[EXG]" min="0" value="0" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                        </div>
                                        <div>
                                <label class="block text-xs text-gray-600 mb-1">G1</label>
                                <input type="number" name="tamanhos[G1]" min="0" value="0" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                        </div>
                                        <div>
                                <label class="block text-xs text-gray-600 mb-1">G2</label>
                                <input type="number" name="tamanhos[G2]" min="0" value="0" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                        </div>
                                        <div>
                                <label class="block text-xs text-gray-600 mb-1">G3</label>
                                <input type="number" name="tamanhos[G3]" min="0" value="0" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                        </div>
                                        <div>
                                <label class="block text-xs text-gray-600 mb-1">Especial</label>
                                <input type="number" name="tamanhos[Especial]" min="0" value="0" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            </div>
                        </div>
                        <div class="mt-3 text-sm text-gray-600">
                            Total de peças: <span id="total-pecas" class="font-medium">0</span>
                                        </div>
                                    </div>

                    <!-- Imagem de Capa -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Imagem de Capa do Item
                        </label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
                            <div class="text-gray-400 mb-2">
                                <svg class="mx-auto h-12 w-12" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                </div>
                            <p class="text-sm text-gray-600">Clique para fazer upload ou arraste e solte</p>
                            <p class="text-xs text-gray-500 mt-1">PNG, JPG ou GIF (MAX. 10MB)</p>
                            <input type="file" name="cover_image" accept="image/*" class="hidden" id="cover-image-input">
                        </div>
                        </div>

                    <!-- Preços -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Preços</label>
                        
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="quantity" class="block text-sm text-gray-600 mb-1">Quantidade Total</label>
                                <input type="number" id="quantity" name="quantity" min="0" value="0" readonly class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50">
                            </div>
                            <div>
                                <label for="unit_price" class="block text-sm text-gray-600 mb-1">Valor Unitário (R$)</label>
                                <input type="number" id="unit_price" name="unit_price" step="0.01" min="0" value="0" readonly class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50">
                            </div>
                        </div>

                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tipo de Corte:</span>
                                <span id="price-corte" class="font-medium">R$ 0,00</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Detalhe:</span>
                                <span id="price-detalhe" class="font-medium">R$ 0,00</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Gola:</span>
                                <span id="price-gola" class="font-medium">R$ 0,00</span>
                </div>
                            <div class="flex justify-between pt-2 border-t border-gray-200">
                                <span class="text-gray-900 font-semibold">Valor Unitário:</span>
                                <span id="price-total" class="font-bold text-indigo-600">R$ 0,00</span>
            </div>
        </div>
    </div>

                    <!-- Botões -->
                    <div class="flex gap-4">
                        <button type="button" onclick="clearForm()" class="flex-1 px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            ✕ Cancelar Edição
                        </button>
                        <button type="submit" id="submit-button" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                            ➕ Adicionar Item
                        </button>
                    </div>
                </form>
            </div>

            <!-- Resumo do Pedido -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Itens do Pedido</h3>
                
                <div class="mb-4 p-3 bg-yellow-100 border border-yellow-300 rounded">
                    <p class="text-sm text-yellow-800">
                        <strong>Debug:</strong> 
                        Itens carregados: {{ count($order->items) }} | 
                        IDs: {{ $order->items->pluck('id')->join(', ') }}
                    </p>
                    </div>
                    
                <div id="items-list" class="space-y-4">
                    @foreach($order->items as $item)
                    <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-indigo-400">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-semibold text-indigo-600">Item {{ $item->id }}</h4>
                            <div class="flex gap-2">
                                <button type="button" onclick="editItem({{ $item->id }})" class="text-blue-600 hover:text-blue-800 text-sm">✏️</button>
                                <button type="button" onclick="removeItem({{ $item->id }})" class="text-red-600 hover:text-red-800 text-sm">🗑️</button>
                            </div>
                        </div>
                        <div class="text-sm text-gray-600 space-y-1">
                            <p><strong>Personalização:</strong> {{ $item->print_type }}</p>
                            <p><strong>Arte:</strong> {{ $item->art_name }}</p>
                            <p><strong>Tecido:</strong> {{ $item->fabric }}</p>
                            <p><strong>Cor:</strong> {{ $item->color }}</p>
                            <p><strong>Gola:</strong> {{ $item->collar }}</p>
                            <p><strong>Modelo:</strong> {{ $item->model }}</p>
                            <p><strong>Detalhe:</strong> {{ $item->detail }}</p>
                            <p><strong>Quantidade:</strong> {{ $item->quantity }} peças</p>
                            <p><strong>Valor Unit.:</strong> R$ {{ number_format($item->unit_price, 2, ',', '.') }}</p>
                            <p><strong>Total:</strong> R$ {{ number_format($item->total_price, 2, ',', '.') }}</p>
                        </div>
                    </div>
                    @endforeach
                        </div>

                <!-- Resumo -->
                <div class="mt-6 p-4 bg-gray-50 rounded-md">
                    <div class="text-sm text-gray-600 space-y-1">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total de Itens:</span>
                            <span class="font-medium text-gray-900">{{ $order->items->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total de Peças:</span>
                            <span class="font-medium text-gray-900">{{ $order->total_items }}</span>
                        </div>
                        <div class="flex justify-between pt-2 border-t border-gray-200">
                            <span class="text-gray-900 font-semibold">Subtotal:</span>
                            <span class="font-bold text-indigo-600 text-lg">R$ {{ number_format($order->subtotal, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Botão Finalizar -->
                <form method="POST" action="{{ route('orders.edit-wizard.finalize', $order->id) }}" class="mt-6">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 flex items-center justify-center">
                        <span class="mr-2">→</span>
                        Finalizar e Prosseguir
                    </button>
                </form>
            </div>
        </div>

        <!-- Progresso -->
        <div class="mt-8 text-center">
            <div class="text-sm text-gray-600">Progresso</div>
            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                <div class="bg-indigo-600 h-2 rounded-full" style="width: 40%"></div>
            </div>
            <div class="text-sm text-gray-600 mt-1">40%</div>
        </div>
    </div>
</div>

@php
    $currentItemsData = $order->items->map(function($item) {
        return [
            'id' => $item->id,
            'print_type' => $item->print_type,
            'art_name' => $item->art_name,
            'quantity' => $item->quantity,
            'fabric' => $item->fabric,
            'color' => $item->color,
            'collar' => $item->collar,
            'model' => $item->model,
            'detail' => $item->detail,
            'unit_price' => $item->unit_price,
            'total_price' => $item->total_price,
            'sizes' => is_string($item->sizes) ? json_decode($item->sizes, true) ?: [] : ($item->sizes ?: [])
        ];
    })->toArray();

    $productOptionsData = \App\Models\ProductOption::all()->groupBy('type')->toArray();
@endphp

<script>
    // Dados das opções de produto
    const options = @json($productOptionsData);
    let optionsWithParents = {};
    let selectedPersonalizacoes = [];

    // Dados dos itens atuais do pedido
    const itemsData = @json($currentItemsData);

    // Inicializar a página
    document.addEventListener('DOMContentLoaded', function() {
        console.log('=== INICIALIZANDO PÁGINA DE EDIÇÃO ===');
        console.log('Items data:', itemsData);
        console.log('Options:', options);
        
        loadOptions();
        setupEventListeners();
    });

    function loadOptions() {
        fetch('/api/product-options')
            .then(response => response.json())
            .then(data => {
                optionsWithParents = data;
                renderPersonalizacao();
                renderTecidos();
                renderCores();
                renderTiposCorte();
                renderDetalhes();
                renderGolas();
            })
            .catch(error => {
                console.error('Erro ao carregar opções:', error);
                renderPersonalizacao();
                renderTecidos();
                renderCores();
                renderTiposCorte();
                renderDetalhes();
                renderGolas();
            });
    }

    function renderPersonalizacao() {
        const container = document.getElementById('personalizacao-options');
        const items = options.personalizacao || [];
        
        container.innerHTML = items.map(item => `
            <label class="flex items-center p-3 border-2 rounded-md cursor-pointer hover:border-indigo-400 transition-all ${selectedPersonalizacoes.includes(item.id) ? 'border-indigo-600 bg-indigo-50' : 'border-gray-200'}">
                <input type="checkbox" name="personalizacao[]" value="${item.id}" 
                       onchange="togglePersonalizacao(${item.id})"
                       class="personalizacao-checkbox mr-3 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" ${selectedPersonalizacoes.includes(item.id) ? 'checked' : ''}>
                <span class="text-sm font-medium text-gray-900">${item.name}</span>
            </label>
        `).join('');
    }

    function togglePersonalizacao(id) {
        const index = selectedPersonalizacoes.indexOf(id);
        if (index > -1) {
            selectedPersonalizacoes.splice(index, 1);
        } else {
            selectedPersonalizacoes.push(id);
        }
        renderPersonalizacao();
        renderTecidos();
    }

    function renderTecidos() {
        const select = document.getElementById('tecido');
        let items = optionsWithParents.tecido || options.tecido || [];
        
        if (selectedPersonalizacoes.length > 0 && optionsWithParents.tecido) {
            items = items.filter(tecido => {
                if (!tecido.parent_ids || tecido.parent_ids.length === 0) {
                    return false;
                }
                return tecido.parent_ids.some(parentId => selectedPersonalizacoes.includes(parentId));
            });
        }
        
        select.innerHTML = '<option value="">Selecione o tecido</option>' + 
            items.map(item => `<option value="${item.id}">${item.name}</option>`).join('');
    }

    function renderCores() {
        const select = document.getElementById('cor');
        const items = options.cor || [];
        
        select.innerHTML = '<option value="">Selecione a cor</option>' + 
            items.map(item => `<option value="${item.id}">${item.name}</option>`).join('');
    }

    function renderTiposCorte() {
        const select = document.getElementById('tipo_corte');
        const items = options.tipo_corte || [];
        
        select.innerHTML = '<option value="">Selecione o tipo de corte</option>' + 
            items.map(item => `<option value="${item.id}" data-price="${item.price}">${item.name} ${item.price > 0 ? '(+R$ ' + parseFloat(item.price).toFixed(2).replace('.', ',') + ')' : ''}</option>`).join('');
    }

    function renderDetalhes() {
        const select = document.getElementById('detalhe');
        const items = options.detalhe || [];
        
        select.innerHTML = '<option value="">Selecione o detalhe</option>' + 
            items.map(item => `<option value="${item.id}" data-price="${item.price}">${item.name} ${item.price > 0 ? '(+R$ ' + parseFloat(item.price).toFixed(2).replace('.', ',') + ')' : ''}</option>`).join('');
    }

    function renderGolas() {
        const select = document.getElementById('gola');
        const items = options.gola || [];
        
        select.innerHTML = '<option value="">Selecione a gola</option>' + 
            items.map(item => `<option value="${item.id}" data-price="${item.price}">${item.name} ${item.price > 0 ? '(+R$ ' + parseFloat(item.price).toFixed(2).replace('.', ',') + ')' : ''}</option>`).join('');
    }

    function updatePrice() {
        const corteSelect = document.getElementById('tipo_corte');
        const detalheSelect = document.getElementById('detalhe');
        const golaSelect = document.getElementById('gola');

        const cortePrice = parseFloat(corteSelect.options[corteSelect.selectedIndex]?.dataset.price || 0);
        const detalhePrice = parseFloat(detalheSelect.options[detalheSelect.selectedIndex]?.dataset.price || 0);
        const golaPrice = parseFloat(golaSelect.options[golaSelect.selectedIndex]?.dataset.price || 0);

        const total = cortePrice + detalhePrice + golaPrice;

        document.getElementById('price-corte').textContent = 'R$ ' + cortePrice.toFixed(2).replace('.', ',');
        document.getElementById('price-detalhe').textContent = 'R$ ' + detalhePrice.toFixed(2).replace('.', ',');
        document.getElementById('price-gola').textContent = 'R$ ' + golaPrice.toFixed(2).replace('.', ',');
        document.getElementById('price-total').textContent = 'R$ ' + total.toFixed(2).replace('.', ',');
        
        document.getElementById('unit_price').value = total.toFixed(2);
    }

    function calculateTotal() {
        const inputs = document.querySelectorAll('input[name^="tamanhos"]');
        let total = 0;
        
        inputs.forEach(input => {
            total += parseInt(input.value) || 0;
        });
        
        document.getElementById('total-pecas').textContent = total;
        document.getElementById('quantity').value = total;
    }

    function setupEventListeners() {
        // Event listeners para os selects
        document.getElementById('tipo_corte')?.addEventListener('change', updatePrice);
        document.getElementById('detalhe')?.addEventListener('change', updatePrice);
        document.getElementById('gola')?.addEventListener('change', updatePrice);

        // Event listeners para os inputs de tamanho
        document.querySelectorAll('input[name^="tamanhos"]').forEach(input => {
            input.addEventListener('input', calculateTotal);
        });

        // Event listener para o formulário
        document.getElementById('sewing-form')?.addEventListener('submit', function(e) {
            e.preventDefault();
            handleFormSubmit();
        });
    }

    // Funcionalidade de edição de itens
    function editItem(itemId) {
        const itemData = itemsData.find(item => item.id == itemId);
        
        if (!itemData) {
            alert('Item não encontrado');
            return;
        }

        // Preencher formulário
        document.getElementById('editing-item-id').value = itemId;
        document.getElementById('form-action').value = 'update_item';
        document.getElementById('form-title').textContent = 'Editar Item ' + itemData.id;
        document.getElementById('submit-button').innerHTML = '💾 Salvar Alterações';

        // Preencher campos do formulário
        fillFormWithItemData(itemData);
        
        // Scroll para o formulário
        document.getElementById('sewing-form').scrollIntoView({ behavior: 'smooth' });
    }

    function fillFormWithItemData(itemData) {
        // Preencher personalizações
        if (itemData.print_type) {
            const personalizacaoTypes = itemData.print_type.split(', ');
            selectedPersonalizacoes = [];
            
            // Encontrar IDs das personalizações selecionadas
            const personalizacaoOptions = options.personalizacao || [];
            personalizacaoTypes.forEach(type => {
                const option = personalizacaoOptions.find(opt => opt.name === type.trim());
                if (option) {
                    selectedPersonalizacoes.push(option.id);
                }
            });
            
            renderPersonalizacao();
            renderTecidos();
        }

        // Preencher outros campos
        setSelectValue('tecido', itemData.fabric);
        setSelectValue('cor', itemData.color);
        setSelectValue('tipo_corte', itemData.model);
        setSelectValue('detalhe', itemData.detail);
        setSelectValue('gola', itemData.collar);

        // Preencher tamanhos
        if (itemData.sizes && Array.isArray(itemData.sizes)) {
            itemData.sizes.forEach(size => {
                const input = document.querySelector(`input[name="tamanhos[${size.size}]"]`);
                if (input) {
                    input.value = size.quantity || 0;
                }
            });
        }

        // Preencher preços
        document.getElementById('quantity').value = itemData.quantity || 0;
        document.getElementById('unit_price').value = itemData.unit_price || 0;

        // Atualizar cálculos
        calculateTotal();
        updatePrice();
    }

    function setSelectValue(selectId, value) {
        const select = document.getElementById(selectId);
        if (!select) return;

        // Procurar por texto
        for (let option of select.options) {
            if (option.textContent.trim() === value) {
                select.value = option.value;
                break;
            }
        }
    }

    function removeItem(itemId) {
        if (confirm('Tem certeza que deseja remover este item?')) {
            // Enviar requisição para remover item
            fetch(`/orders/edit-wizard/sewing`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    form_action: 'delete_item',
                    item_id: itemId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Erro ao remover item: ' + (data.message || 'Erro desconhecido'));
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao remover item');
            });
        }
    }

    function handleFormSubmit() {
        const formData = new FormData(document.getElementById('sewing-form'));
        const action = formData.get('form-action');
        
        if (action === 'update_item') {
            updateItem(formData);
        } else {
            addItem(formData);
        }
    }

    function updateItem(formData) {
        const itemId = formData.get('editing-item-id');
        
        // Enviar dados via AJAX
        fetch(`/orders/edit-wizard/sewing`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Recarregar a página para mostrar as alterações
                location.reload();
            } else {
                alert('Erro ao atualizar item: ' + (data.message || 'Erro desconhecido'));
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao atualizar item');
        });
    }

    function addItem(formData) {
        // Enviar dados via AJAX
        fetch('/orders/edit-wizard/sewing', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Recarregar a página para mostrar as alterações
                location.reload();
            } else {
                alert('Erro ao adicionar item: ' + (data.message || 'Erro desconhecido'));
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao adicionar item');
        });
    }

    function clearForm() {
        document.getElementById('sewing-form').reset();
        document.getElementById('editing-item-id').value = '';
        document.getElementById('form-action').value = 'add_item';
        document.getElementById('form-title').textContent = 'Adicionar Item';
        document.getElementById('submit-button').innerHTML = '➕ Adicionar Item';
        
        selectedPersonalizacoes = [];
        renderPersonalizacao();
        renderTecidos();
        
        document.querySelectorAll('input[name^="tamanhos"]').forEach(input => {
            input.value = 0;
        });
        document.getElementById('total-pecas').textContent = '0';
        document.getElementById('quantity').value = '0';
        document.getElementById('unit_price').value = '0';
        updatePrice();
        }
    </script>
@endsection