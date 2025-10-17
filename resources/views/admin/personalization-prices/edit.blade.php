<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Configurar Preços - {{ $types[$type] }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        .bg-blue-25 { background-color: #eff6ff; }
        .bg-green-25 { background-color: #f0fdf4; }
        .bg-gray-25 { background-color: #f9fafb; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <x-app-header />

    <div class="max-w-6xl mx-auto px-4 py-6">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center space-x-2">
                    <div class="w-6 h-6 bg-indigo-600 text-white rounded-full flex items-center justify-center text-xs font-medium">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                    <div>
                        <span class="text-base font-medium text-indigo-600">Configurar Preços - {{ $types[$type] }}</span>
                        <p class="text-xs text-gray-500">Tabela de preços por tamanho e quantidade</p>
                    </div>
                </div>
                <a href="{{ route('admin.personalization-prices.index') }}" 
                   class="flex items-center px-4 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-md transition-all text-sm font-medium">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Voltar
                </a>
            </div>
        </div>

        <!-- Messages -->
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

        @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-red-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="text-sm font-medium text-red-800 mb-2">Erro na validação:</p>
                    <ul class="text-sm text-red-700 space-y-1">
                        @foreach($errors->all() as $error)
                        <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        <div class="bg-white rounded-lg border border-gray-200">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-lg font-medium text-gray-900">Preços Base por Tamanho e Quantidade</h1>
                        <p class="text-sm text-gray-500">Preços incluem 1 cor - cores adicionais são cobradas separadamente</p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.personalization-prices.update', $type) }}" id="prices-form">
                @csrf
                @method('PUT')

                <div class="p-6">
                    <!-- Gerenciamento de Tamanhos -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-medium text-gray-900">Gerenciar Tamanhos</h3>
                            <button type="button" onclick="addNewSize()" 
                                    class="flex items-center px-3 py-1 bg-green-600 text-white rounded text-sm hover:bg-green-700">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Adicionar Tamanho
                            </button>
                        </div>
                        
                        <div id="sizes-container" class="flex flex-wrap gap-2">
                            <!-- Tamanhos existentes serão carregados aqui -->
                        </div>
                    </div>

                    <!-- Action Bar -->
                    <div class="flex items-center justify-between mb-6">
                        <span class="text-sm text-gray-500" id="quantity-ranges-count">0 faixas de quantidade</span>
                        <button type="button" onclick="addNewQuantityRange()" 
                                class="flex items-center px-4 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-800 focus:outline-none focus:ring-1 focus:ring-gray-500 transition-all text-sm font-medium">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Adicionar Faixa de Quantidade
                        </button>
                    </div>

                    <!-- Legenda -->
                    <div class="mb-4 p-3 bg-gray-50 rounded-lg border">
                        <div class="flex items-center space-x-6 text-sm">
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-blue-50 border border-blue-200 rounded mr-2"></div>
                                <span class="text-blue-600 font-medium">Quantidades</span>
                                <span class="text-gray-500 ml-1">(DE/ATÉ)</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-green-50 border border-green-200 rounded mr-2"></div>
                                <span class="text-green-600 font-medium">Tamanhos</span>
                                <span class="text-gray-500 ml-1">(Preços por tamanho)</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tabela de Preços -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full" id="prices-table">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DE</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ATÉ</th>
                                    <!-- Colunas de tamanhos serão adicionadas dinamicamente -->
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">AÇÕES</th>
                                </tr>
                            </thead>
                            <tbody id="prices-tbody">
                                <!-- Linhas serão adicionadas dinamicamente -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Botões de Ação -->
                    <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.personalization-prices.index') }}" 
                           class="text-gray-500 hover:text-gray-700 text-sm">
                            Voltar
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-gray-900 text-white rounded text-sm font-medium hover:bg-gray-800 focus:outline-none">
                            Salvar Preços
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Dados iniciais
        let availableSizes = ['ESCUDO', 'A4', 'A3']; // Tamanhos padrão
        let quantityRanges = []; // Faixas de quantidade
        let priceRowIndex = 0;

        // Inicializar página
        document.addEventListener('DOMContentLoaded', function() {
            loadExistingData();
            renderSizes();
            renderTable();
        });

        // Funções globais que podem ser chamadas pelos botões
        window.addNewSize = function() {
            const sizeName = prompt('Digite o nome do novo tamanho (ex: A5, A1, 10x15cm):');
            if (sizeName && sizeName.trim() && !availableSizes.includes(sizeName.trim().toUpperCase())) {
                availableSizes.push(sizeName.trim().toUpperCase());
                renderSizes();
                renderTable();
            } else if (sizeName && availableSizes.includes(sizeName.trim().toUpperCase())) {
                alert('Este tamanho já existe!');
            }
        };

        window.removeSize = function(size) {
            if (confirm(`Tem certeza que deseja remover o tamanho "${size}"?`)) {
                availableSizes = availableSizes.filter(s => s !== size);
                renderSizes();
                renderTable();
            }
        };

        window.addNewQuantityRange = function() {
            const newRange = {
                id: priceRowIndex++,
                quantity_from: 1,
                quantity_to: null,
                prices: {}
            };
            
            quantityRanges.push(newRange);
            updateQuantityRangesCount();
            renderTable();
        };

        window.removePriceRow = function(index) {
            if (confirm('Tem certeza que deseja remover esta faixa de quantidade?')) {
                quantityRanges.splice(index, 1);
                updateQuantityRangesCount();
                renderTable();
            }
        };

        function loadExistingData() {
            // Debug: verificar se há dados
            console.log('=== CARREGANDO DADOS EXISTENTES ===');
            console.log('Prices count:', {{ $prices->count() }});
            
            // Carregar dados existentes do servidor
            @if($prices->count() > 0)
                console.log('Dados encontrados, processando...');
                
                // Usar dados JSON diretamente
                const pricesData = @json($prices);
                console.log('Raw prices data:', pricesData);
                
                // Agrupar preços por faixa de quantidade
                const priceGroups = {};
                
                pricesData.forEach(function(price) {
                    const key = price.quantity_from + '_' + (price.quantity_to || 'null');
                    console.log('Processing price:', price);
                    
                    if (!priceGroups[key]) {
                        priceGroups[key] = {
                            quantity_from: price.quantity_from,
                            quantity_to: price.quantity_to,
                            prices: {}
                        };
                    }
                    priceGroups[key].prices[price.size_name] = price.price;
                });
                
                console.log('Price groups after processing:', priceGroups);
                
                // Converter para array
                Object.values(priceGroups).forEach((group, index) => {
                    quantityRanges.push({
                        id: index,
                        quantity_from: group.quantity_from,
                        quantity_to: group.quantity_to,
                        prices: group.prices
                    });
                });
                
                // Extrair tamanhos únicos dos dados existentes
                const existingSizes = new Set();
                pricesData.forEach(function(price) {
                    existingSizes.add(price.size_name);
                });
                
                // Atualizar lista de tamanhos disponíveis
                availableSizes = Array.from(existingSizes);
                
                console.log('Final available sizes:', availableSizes);
                console.log('Final quantity ranges:', quantityRanges);
            @else
                console.log('Nenhum dado encontrado');
            @endif
            updateQuantityRangesCount();
        }

        function renderSizes() {
            const container = document.getElementById('sizes-container');
            container.innerHTML = '';
            
            availableSizes.forEach(size => {
                const sizeElement = document.createElement('div');
                sizeElement.className = 'flex items-center bg-white border border-gray-200 rounded px-3 py-2';
                sizeElement.innerHTML = `
                    <span class="text-sm font-medium text-gray-700 mr-2">${size}</span>
                    <button type="button" onclick="removeSize('${size}')" 
                            class="text-red-500 hover:text-red-700 ml-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                `;
                container.appendChild(sizeElement);
            });
        }

        function renderTable() {
            const table = document.getElementById('prices-table');
            const tbody = document.getElementById('prices-tbody');
            
            // Atualizar cabeçalho
            const headerRow = table.querySelector('thead tr');
            headerRow.innerHTML = `
                <th class="px-4 py-3 text-left text-xs font-medium text-blue-600 uppercase tracking-wider bg-blue-50 border-r-2 border-blue-200">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                        </svg>
                        DE
                    </div>
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium text-blue-600 uppercase tracking-wider bg-blue-50 border-r-4 border-gray-300">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                        </svg>
                        ATÉ
                    </div>
                </th>
            `;
            
            availableSizes.forEach(size => {
                const th = document.createElement('th');
                th.className = 'px-4 py-3 text-left text-xs font-medium text-green-600 uppercase tracking-wider bg-green-50';
                th.innerHTML = `
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                        </svg>
                        ${size}
                    </div>
                `;
                headerRow.appendChild(th);
            });
            
            headerRow.innerHTML += '<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">AÇÕES</th>';
            
            // Renderizar linhas
            tbody.innerHTML = '';
            quantityRanges.forEach((range, index) => {
                const row = createPriceRow(range, index);
                tbody.appendChild(row);
            });
        }

        function createPriceRow(range, index) {
            const row = document.createElement('tr');
            row.className = 'price-row border-b border-gray-100';
            
            let html = `
                <td class="px-4 py-3 bg-blue-25 border-r-2 border-blue-200">
                    <input type="number" name="prices[${index}][quantity_from]" 
                           value="${range.quantity_from}" min="1"
                           class="w-full px-3 py-2 border border-blue-200 rounded text-sm focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-200">
                </td>
                <td class="px-4 py-3 bg-blue-25 border-r-4 border-gray-300">
                    <input type="number" name="prices[${index}][quantity_to]" 
                           value="${range.quantity_to || ''}" min="1"
                           class="w-full px-3 py-2 border border-blue-200 rounded text-sm focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-200">
                </td>
            `;
            
            availableSizes.forEach(size => {
                const price = range.prices[size] || '';
                html += `
                    <td class="px-4 py-3 bg-green-25">
                        <input type="number" name="prices[${index}][${size.toLowerCase()}]" 
                               value="${price}" step="0.01" min="0"
                               class="w-full px-3 py-2 border border-green-200 rounded text-sm focus:border-green-400 focus:outline-none focus:ring-1 focus:ring-green-200">
                    </td>
                `;
            });
            
            html += `
                <td class="px-4 py-3 bg-gray-25">
                    <button type="button" onclick="removePriceRow(${index})" 
                            class="text-gray-400 hover:text-gray-600 p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </td>
            `;
            
            row.innerHTML = html;
            return row;
        }

        function updateQuantityRangesCount() {
            document.getElementById('quantity-ranges-count').textContent = 
                `${quantityRanges.length} faixa${quantityRanges.length !== 1 ? 's' : ''} de quantidade`;
        }
    </script>
</body>
</html>