<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Preços - {{ $types[$type] }} - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .fade-in { animation: fadeIn 0.3s ease-in; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .slide-in { animation: slideIn 0.2s ease-out; }
        @keyframes slideIn { from { opacity: 0; transform: translateX(-20px); } to { opacity: 1; transform: translateX(0); } }
        .pulse-success { animation: pulseSuccess 0.6s ease-in-out; }
        @keyframes pulseSuccess { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.05); } }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <x-app-header />

    <div class="max-w-7xl mx-auto p-6">
        <!-- Header melhorado -->
        <div class="mb-8 fade-in">
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-indigo-500">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-indigo-100 rounded-lg">
                            <i class="fas fa-tags text-indigo-600 text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Configurar Preços</h1>
                            <p class="text-gray-600 mt-1 flex items-center">
                                <i class="fas fa-palette mr-2 text-indigo-500"></i>
                                {{ $types[$type] }} - Personalização
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('admin.personalization-prices.index') }}" 
                       class="group flex items-center px-4 py-2 text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 border border-gray-300 hover:border-indigo-300 rounded-lg transition-all duration-200">
                        <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i>
                        Voltar
                    </a>
                </div>
            </div>
        </div>

        <!-- Alertas melhorados -->
        @if(session('success'))
        <div class="mb-6 fade-in">
            <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-400 text-xl mr-3"></i>
                    <div>
                        <p class="text-green-800 font-medium">Sucesso!</p>
                        <p class="text-green-700 text-sm">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if($errors->any())
        <div class="mb-6 fade-in">
            <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg shadow-sm">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-red-400 text-xl mr-3 mt-0.5"></i>
                    <div>
                        <p class="text-red-800 font-medium mb-2">Erro na validação:</p>
                        <ul class="text-red-700 text-sm space-y-1">
                            @foreach($errors->all() as $error)
                                <li class="flex items-center">
                                    <i class="fas fa-dot-circle text-red-400 text-xs mr-2"></i>
                                    {{ $error }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.personalization-prices.update', $type) }}" id="prices-form">
            @csrf
            @method('PUT')

            <!-- Card principal melhorado -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                    <div class="flex items-center">
                        <i class="fas fa-cogs text-white text-xl mr-3"></i>
                        <div>
                            <h2 class="text-xl font-semibold text-white">Preços por Tamanho e Quantidade</h2>
                            <p class="text-indigo-100 text-sm mt-1">Configure as faixas de quantidade e seus respectivos preços para cada tamanho</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Seção de Controles -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                        <!-- Seleção de Tamanho -->
                        <div class="space-y-3">
                            <label class="block text-sm font-medium text-gray-700 flex items-center">
                                <i class="fas fa-search mr-2 text-indigo-500"></i>
                                Filtrar por Tamanho
                            </label>
                            <select id="size-selector" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                <option value="">🔍 Todos os tamanhos</option>
                                @foreach($sizes as $size)
                                <option value="{{ $size->size_name }}" 
                                        data-dimensions="{{ $size->size_dimensions }}"
                                        {{ old('selected_size') == $size->size_name ? 'selected' : '' }}>
                                    {{ $size->size_name }}
                                    @if($size->size_dimensions)
                                        ({{ $size->size_dimensions }})
                                    @endif
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Estatísticas rápidas -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-sm font-medium text-gray-700 mb-3 flex items-center">
                                <i class="fas fa-chart-bar mr-2 text-indigo-500"></i>
                                Resumo
                            </h3>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-500">Tamanhos:</p>
                                    <p class="font-semibold text-gray-900" id="total-sizes">{{ $sizes->count() }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Faixas de Preço:</p>
                                    <p class="font-semibold text-gray-900" id="total-ranges">{{ $prices->flatten()->count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Adicionar Novo Tamanho -->
                    <div class="mb-8 p-6 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-200">
                        <div class="flex items-center mb-4">
                            <i class="fas fa-plus-circle text-green-600 text-lg mr-3"></i>
                            <h3 class="text-lg font-semibold text-gray-800">Adicionar Novo Tamanho</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nome do Tamanho *</label>
                                <input type="text" id="new-size-name" placeholder="Ex: A4, A3, 10x15cm" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Dimensões</label>
                                <input type="text" id="new-size-dimensions" placeholder="Ex: 21x29.7cm" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Preço Inicial</label>
                                <input type="number" id="new-size-price" placeholder="0.00" step="0.01" min="0"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                            </div>
                            <div class="flex items-end">
                                <button type="button" onclick="addNewSize()" 
                                        class="w-full bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 hover:shadow-lg transform hover:-translate-y-0.5">
                                    <i class="fas fa-plus mr-2"></i>
                                    Adicionar
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Preços por Tamanho -->
                    <div id="prices-container" class="space-y-6">
                        @if($prices->count() > 0)
                            @foreach($prices as $sizeName => $sizePrices)
                                <div class="size-section fade-in" data-size="{{ $sizeName }}">
                                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200">
                                        <!-- Header do tamanho -->
                                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200 rounded-t-xl">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-3">
                                                    <div class="p-2 bg-blue-100 rounded-lg">
                                                        <i class="fas fa-ruler text-blue-600"></i>
                                                    </div>
                                                    <div>
                                                        <h3 class="text-lg font-semibold text-gray-900">
                                                            {{ $sizeName }}
                                                            @if($sizePrices->first()->size_dimensions)
                                                                <span class="text-sm text-gray-500 font-normal">({{ $sizePrices->first()->size_dimensions }})</span>
                                                            @endif
                                                        </h3>
                                                        <p class="text-sm text-gray-600">
                                                            {{ $sizePrices->count() }} faixa{{ $sizePrices->count() > 1 ? 's' : '' }} de preço configurada{{ $sizePrices->count() > 1 ? 's' : '' }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                                                        {{ $sizePrices->count() }} faixas
                                                    </span>
                                                    <button type="button" onclick="removeSize('{{ $sizeName }}')" 
                                                            class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                                            title="Remover tamanho">
                                                        <i class="fas fa-trash text-sm"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Corpo com faixas de preço -->
                                        <div class="p-6">
                                            <div class="space-y-4" id="price-rows-{{ $sizeName }}">
                                                @foreach($sizePrices as $index => $price)
                                                    @include('admin.personalization-prices.partials.price-row', [
                                                        'type' => $type,
                                                        'sizeName' => $sizeName,
                                                        'index' => $index,
                                                        'price' => $price
                                                    ])
                                                @endforeach
                                            </div>
                                            
                                            <!-- Botão para adicionar faixa -->
                                            <div class="mt-6 pt-4 border-t border-gray-100">
                                                <button type="button" onclick="addPriceRow('{{ $sizeName }}')" 
                                                        class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 hover:shadow-lg transform hover:-translate-y-0.5">
                                                    <i class="fas fa-plus mr-2"></i>
                                                    Adicionar Faixa de Preço
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-12">
                                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-tags text-gray-400 text-3xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum tamanho configurado</h3>
                                <p class="text-gray-500 mb-6">Adicione um novo tamanho para começar a configurar preços</p>
                                <button type="button" onclick="document.getElementById('new-size-name').focus()" 
                                        class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                    <i class="fas fa-plus mr-2"></i>
                                    Adicionar Primeiro Tamanho
                                </button>
                            </div>
                        @endif
                    </div>

                    <!-- Botões de ação melhorados -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0">
                            <a href="{{ route('admin.personalization-prices.index') }}" 
                               class="group flex items-center px-6 py-3 text-gray-600 hover:text-gray-900 border border-gray-300 hover:border-gray-400 rounded-lg transition-all duration-200 hover:bg-gray-50">
                                <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i>
                                Cancelar
                            </a>
                            
                            <div class="flex items-center space-x-4">
                                <div class="text-sm text-gray-500 hidden sm:block">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    <span id="validation-status">Pronto para salvar</span>
                                </div>
                                <button type="submit" 
                                        class="group flex items-center px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200 hover:shadow-lg transform hover:-translate-y-0.5">
                                    <i class="fas fa-save mr-2 group-hover:rotate-12 transition-transform"></i>
                                    Salvar Preços
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Informações importantes melhoradas -->
        <div class="mt-8 fade-in">
            <div class="bg-gradient-to-r from-amber-50 to-yellow-50 border-l-4 border-amber-400 rounded-xl p-6 shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="p-2 bg-amber-100 rounded-lg">
                            <i class="fas fa-lightbulb text-amber-600 text-lg"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-amber-800 mb-3">Dicas Importantes</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-amber-700">
                            <div class="space-y-2">
                                <div class="flex items-start">
                                    <i class="fas fa-check-circle text-amber-500 text-xs mt-1 mr-2"></i>
                                    <span>As faixas devem ser sequenciais e não se sobrepor</span>
                                </div>
                                <div class="flex items-start">
                                    <i class="fas fa-check-circle text-amber-500 text-xs mt-1 mr-2"></i>
                                    <span>Deixe "Até" vazio para indicar "infinito" (última faixa)</span>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex items-start">
                                    <i class="fas fa-check-circle text-amber-500 text-xs mt-1 mr-2"></i>
                                    <span>As alterações se aplicam imediatamente a novos pedidos</span>
                                </div>
                                <div class="flex items-start">
                                    <i class="fas fa-check-circle text-amber-500 text-xs mt-1 mr-2"></i>
                                    <span>Use nomes de tamanho consistentes (ex: A4, A3, 10x15cm)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let priceRowIndex = {{ $prices->flatten()->count() }};
        const personalizationType = '{{ $type }}';

        function addNewSize() {
            const sizeName = document.getElementById('new-size-name').value.trim();
            const dimensions = document.getElementById('new-size-dimensions').value.trim();
            const initialPrice = document.getElementById('new-size-price').value || '0';
            
            if (!sizeName) {
                showNotification('Por favor, digite o nome do tamanho', 'error');
                document.getElementById('new-size-name').focus();
                return;
            }
            
            // Verificar se já existe
            if (document.querySelector(`[data-size="${sizeName}"]`)) {
                showNotification('Este tamanho já existe', 'warning');
                return;
            }
            
            // Adicionar seção do tamanho
            const container = document.getElementById('prices-container');
            const sizeSection = document.createElement('div');
            sizeSection.className = 'size-section fade-in';
            sizeSection.setAttribute('data-size', sizeName);
            
            sizeSection.innerHTML = `
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200 rounded-t-xl">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-blue-100 rounded-lg">
                                    <i class="fas fa-ruler text-blue-600"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        ${sizeName}
                                        ${dimensions ? `<span class="text-sm text-gray-500 font-normal">(${dimensions})</span>` : ''}
                                    </h3>
                                    <p class="text-sm text-gray-600">0 faixas de preço configuradas</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">0 faixas</span>
                                <button type="button" onclick="removeSize('${sizeName}')" 
                                        class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                        title="Remover tamanho">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div class="space-y-4" id="price-rows-${sizeName}">
                            <button type="button" onclick="addPriceRow('${sizeName}')" 
                                    class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 hover:shadow-lg transform hover:-translate-y-0.5">
                                <i class="fas fa-plus mr-2"></i>
                                Adicionar Faixa de Preço
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            container.appendChild(sizeSection);
            
            // Adicionar primeira faixa se preço inicial foi fornecido
            if (parseFloat(initialPrice) > 0) {
                setTimeout(() => {
                    addPriceRow(sizeName, initialPrice);
                }, 300);
            }
            
            // Limpar campos
            document.getElementById('new-size-name').value = '';
            document.getElementById('new-size-dimensions').value = '';
            document.getElementById('new-size-price').value = '';
            
            // Atualizar estatísticas
            updateStatistics();
            
            showNotification(`Tamanho "${sizeName}" adicionado com sucesso!`, 'success');
        }

        function addPriceRow(sizeName, initialPrice = '') {
            fetch(`{{ route('admin.personalization-prices.add-row') }}?type=${personalizationType}&size_name=${sizeName}&index=${priceRowIndex}`)
                .then(response => response.text())
                .then(html => {
                    const sizeSection = document.querySelector(`[data-size="${sizeName}"]`);
                    const container = sizeSection.querySelector(`#price-rows-${sizeName}`);
                    
                    // Se há apenas o botão, substituir por uma div vazia
                    if (container.children.length === 1 && container.querySelector('button[onclick*="addPriceRow"]')) {
                        container.innerHTML = '';
                    }
                    
                    container.insertAdjacentHTML('beforeend', html);
                    
                    // Se preço inicial foi fornecido, preencher
                    if (initialPrice) {
                        const newRow = container.lastElementChild;
                        const priceInput = newRow.querySelector('input[name*="[price]"]');
                        const quantityFromInput = newRow.querySelector('input[name*="[quantity_from]"]');
                        if (priceInput) priceInput.value = initialPrice;
                        if (quantityFromInput) quantityFromInput.value = '1';
                        validatePriceRow(priceInput);
                    }
                    
                    priceRowIndex++;
                    updateStatistics();
                    showNotification('Faixa de preço adicionada!', 'success');
                })
                .catch(error => {
                    console.error('Erro ao adicionar linha:', error);
                    showNotification('Erro ao adicionar linha. Tente novamente.', 'error');
                });
        }

        function removePriceRow(button) {
            const row = button.closest('.price-row');
            row.remove();
        }

        function removeSize(sizeName) {
            if (confirm(`Tem certeza que deseja remover o tamanho "${sizeName}" e todos os seus preços?`)) {
                const sizeSection = document.querySelector(`[data-size="${sizeName}"]`);
                sizeSection.remove();
            }
        }

        function validatePrices() {
            const rows = document.querySelectorAll('.price-row');
            const quantities = [];
            
            for (let row of rows) {
                const sizeName = row.closest('.size-section').getAttribute('data-size');
                const from = parseInt(row.querySelector('input[name*="[quantity_from]"]').value);
                const to = row.querySelector('input[name*="[quantity_to]"]').value;
                const toValue = to ? parseInt(to) : null;
                
                if (from && from > 0) {
                    quantities.push({ sizeName, from, to: toValue, row });
                }
            }
            
            // Agrupar por tamanho
            const bySize = {};
            quantities.forEach(q => {
                if (!bySize[q.sizeName]) bySize[q.sizeName] = [];
                bySize[q.sizeName].push(q);
            });
            
            // Verificar sobreposições por tamanho
            for (let sizeName in bySize) {
                const sizeQuantities = bySize[sizeName].sort((a, b) => a.from - b.from);
                
                for (let i = 0; i < sizeQuantities.length - 1; i++) {
                    const current = sizeQuantities[i];
                    const next = sizeQuantities[i + 1];
                    
                    if (current.to && current.to >= next.from) {
                        alert(`Sobreposição detectada no tamanho "${sizeName}": Faixa ${current.from}-${current.to} se sobrepõe com ${next.from}-${next.to || '∞'}`);
                        return false;
                    }
                }
            }
            
            return true;
        }

        document.getElementById('prices-form').addEventListener('submit', function(e) {
            if (!validatePrices()) {
                e.preventDefault();
            }
        });

        // Mostrar/ocultar seções baseado na seleção
        document.getElementById('size-selector').addEventListener('change', function() {
            const selectedSize = this.value;
            const sections = document.querySelectorAll('.size-section');
            
            sections.forEach(section => {
                if (selectedSize === '' || section.getAttribute('data-size') === selectedSize) {
                    section.style.display = 'block';
                } else {
                    section.style.display = 'none';
                }
            });
        });

        // Funções auxiliares
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500';
            const icon = type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle';
            
            notification.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 fade-in`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas ${icon} mr-2"></i>
                    <span>${message}</span>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        function validatePriceRow(input) {
            const row = input.closest('.price-row');
            const quantityFrom = parseInt(row.querySelector('input[name*="[quantity_from]"]').value) || 0;
            const quantityTo = row.querySelector('input[name*="[quantity_to]"]').value;
            const price = parseFloat(row.querySelector('input[name*="[price]"]').value) || 0;
            
            // Atualizar resumo
            const rangeSummary = row.querySelector('[id^="range-summary-"]');
            const priceSummary = row.querySelector('[id^="price-summary-"]');
            
            if (rangeSummary && priceSummary) {
                if (quantityFrom > 0) {
                    if (quantityTo) {
                        rangeSummary.textContent = `${quantityFrom} a ${quantityTo} peças`;
                    } else {
                        rangeSummary.textContent = `${quantityFrom}+ peças`;
                    }
                    priceSummary.textContent = `R$ ${price.toFixed(2).replace('.', ',')}`;
                } else {
                    rangeSummary.textContent = 'Configure os valores';
                    priceSummary.textContent = 'R$ 0,00';
                }
            }
            
            // Validação básica
            if (quantityFrom > 0 && price > 0) {
                row.classList.remove('border-red-300', 'bg-red-50');
                row.classList.add('border-gray-200', 'bg-gradient-to-r', 'from-gray-50', 'to-blue-50');
            } else {
                row.classList.add('border-red-300', 'bg-red-50');
                row.classList.remove('border-gray-200', 'bg-gradient-to-r', 'from-gray-50', 'to-blue-50');
            }
        }

        function updateStatistics() {
            const sizes = document.querySelectorAll('.size-section').length;
            const ranges = document.querySelectorAll('.price-row').length;
            
            const totalSizes = document.getElementById('total-sizes');
            const totalRanges = document.getElementById('total-ranges');
            
            if (totalSizes) totalSizes.textContent = sizes;
            if (totalRanges) totalRanges.textContent = ranges;
        }

        function removeSize(sizeName) {
            if (confirm(`Tem certeza que deseja remover o tamanho "${sizeName}" e todos os seus preços?`)) {
                const sizeSection = document.querySelector(`[data-size="${sizeName}"]`);
                sizeSection.style.animation = 'fadeOut 0.3s ease-out';
                setTimeout(() => {
                    sizeSection.remove();
                    updateStatistics();
                    showNotification(`Tamanho "${sizeName}" removido!`, 'success');
                }, 300);
            }
        }

        // Adicionar animação de fadeOut
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeOut {
                from { opacity: 1; transform: translateY(0); }
                to { opacity: 0; transform: translateY(-20px); }
            }
        `;
        document.head.appendChild(style);

        // Validação em tempo real
        document.addEventListener('input', function(e) {
            if (e.target.matches('input[name*="[quantity_from]"], input[name*="[quantity_to]"], input[name*="[price]"]')) {
                validatePriceRow(e.target);
            }
        });

        // Atualizar estatísticas na inicialização
        document.addEventListener('DOMContentLoaded', function() {
            updateStatistics();
        });
    </script>
</body>
</html>
