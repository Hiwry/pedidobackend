<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Preços - Serigrafia - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .fade-in { animation: fadeIn 0.3s ease-in; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeOut { from { opacity: 1; transform: translateY(0); } to { opacity: 0; transform: translateY(-10px); } }
        .price-table input {
            text-align: center;
            font-weight: 600;
        }
        .price-table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: bold;
            padding: 12px;
            text-align: center;
        }
        .price-table td {
            padding: 8px;
            border: 1px solid #e5e7eb;
        }
        .price-table input:focus {
            outline: none;
            ring: 2px;
            ring-color: #667eea;
        }
        .price-row {
            transition: all 0.2s ease;
        }
        .price-row:hover {
            background-color: #eff6ff !important;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <x-app-header />

    <div class="max-w-7xl mx-auto p-6">
        <!-- Header -->
        <div class="mb-8 fade-in">
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <i class="fas fa-paint-brush text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Configurar Preços - Serigrafia</h1>
                            <p class="text-gray-600 mt-1 flex items-center">
                                <i class="fas fa-table mr-2 text-blue-500"></i>
                                Tabela de preços por tamanho e quantidade
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('admin.personalization-prices.index') }}" 
                       class="group flex items-center px-4 py-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 border border-gray-300 hover:border-blue-300 rounded-lg transition-all duration-200">
                        <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i>
                        Voltar
                    </a>
                </div>
            </div>
        </div>

        <!-- Alertas -->
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

            <!-- Tabela de Preços Base (Tamanhos x Quantidades) -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200 mb-8">
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-table text-white text-xl mr-3"></i>
                            <div>
                                <h2 class="text-xl font-semibold text-white">Preços Base por Tamanho e Quantidade</h2>
                                <p class="text-blue-100 text-sm mt-1">Preços incluem 1 cor - cores adicionais são cobradas separadamente</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6 overflow-x-auto">
                    <!-- Botão para adicionar nova linha de quantidade -->
                    <div class="mb-4 flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            <i class="fas fa-info-circle mr-1"></i>
                            <span id="row-count">0 faixas de quantidade</span>
                        </div>
                        <button type="button" onclick="addQuantityRow()" 
                                class="flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-all duration-200">
                            <i class="fas fa-plus mr-2"></i>
                            Adicionar Faixa de Quantidade
                        </button>
                    </div>

                    <table class="price-table w-full border-collapse" id="price-table">
                        <thead>
                            <tr>
                                <th class="rounded-tl-lg w-32">DE</th>
                                <th class="w-32">ATÉ</th>
                                <th>ESCUDO</th>
                                <th>A4</th>
                                <th>A3</th>
                                <th>COR +</th>
                                <th class="rounded-tr-lg w-20">AÇÕES</th>
                            </tr>
                        </thead>
                        <tbody id="price-tbody">
                            @php
                                $sizes = ['ESCUDO', 'A4', 'A3'];
                                
                                // Debug: Log dos dados recebidos
                                \Log::info('Template Debug - Prices: ' . json_encode($prices->toArray()));
                                \Log::info('Template Debug - Colors: ' . json_encode($colors->toArray()));
                                
                                // Buscar preços existentes e organizar por quantidade
                                $quantityGroups = [];
                                foreach ($prices as $sizeName => $priceList) {
                                    foreach ($priceList as $priceItem) {
                                        $qtyKey = $priceItem->quantity_from . '_' . $priceItem->quantity_to;
                                        if (!isset($quantityGroups[$qtyKey])) {
                                            $quantityGroups[$qtyKey] = [
                                                'from' => $priceItem->quantity_from,
                                                'to' => $priceItem->quantity_to,
                                                'prices' => []
                                            ];
                                        }
                                        $quantityGroups[$qtyKey]['prices'][$sizeName] = $priceItem->price;
                                    }
                                }
                                
                    // Ordenar por quantidade (from)
                    uasort($quantityGroups, function($a, $b) {
                        return $a['from'] <=> $b['from'];
                    });
                                
                                \Log::info('Template Debug - Quantity Groups: ' . json_encode($quantityGroups));
                                
                                // Buscar preços de cores existentes e organizar por ordem
                                $colorPricesByOrder = [];
                                foreach ($colors as $color) {
                                    if ($color->order > 1) { // Pular "1 Cor" que tem ordem 1
                                        // Extrair faixa de quantidade do nome da cor
                                        if (preg_match('/\((?<from>\d+)-?(?<to>\d+)?\)/', $color->name, $matches)) {
                                            $from = $matches['from'];
                                            $to = $matches['to'] ?? null;
                                            $qtyKey = $from . '_' . $to;
                                            $colorPricesByOrder[$qtyKey] = $color->price;
                                        }
                                    }
                                }
                            @endphp
                            
                            @foreach($quantityGroups as $qtyIndex => $qtyGroup)
                            <tr class="price-row hover:bg-blue-50 transition-colors" data-index="{{ $loop->index }}">
                                <!-- De -->
                                <td class="bg-gray-50">
                                    <input type="number" 
                                           name="prices[{{ $loop->index }}][from]"
                                           value="{{ $qtyGroup['from'] }}"
                                           min="1"
                                           required
                                           onchange="updateRowCount()"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-bold text-center"
                                           placeholder="10">
                                </td>
                                
                                <!-- Até -->
                                <td class="bg-gray-50">
                                    <input type="number" 
                                           name="prices[{{ $loop->index }}][to]"
                                           value="{{ $qtyGroup['to'] }}"
                                           min="1"
                                           onchange="updateRowCount()"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-bold text-center"
                                           placeholder="29">
                                </td>
                                
                                @foreach($sizes as $size)
                                    <td>
                                        <input type="number" 
                                               name="prices[{{ $loop->parent->index }}][{{ $size }}]"
                                               value="{{ $qtyGroup['prices'][$size] ?? '' }}"
                                               step="0.01"
                                               min="0"
                                               data-size="{{ $size }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="0,00">
                                    </td>
                                @endforeach
                                
                                <!-- Coluna de Cor Adicional -->
                                <td class="bg-yellow-50">
                                    <input type="number" 
                                           name="colors[{{ $loop->index }}][price]"
                                           value="{{ $colorPricesByOrder[$qtyIndex] ?? '' }}"
                                           step="0.01"
                                           min="0"
                                           class="w-full px-3 py-2 border border-yellow-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 bg-white"
                                           placeholder="0,00">
                                </td>
                                
                                <!-- Botão Remover -->
                                <td class="text-center">
                                    <button type="button" 
                                            onclick="removeRow(this)"
                                            class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors"
                                            title="Remover linha">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                            
                            @if(count($quantityGroups) === 0)
                            <tr id="empty-message">
                                <td colspan="7" class="text-center py-8 text-gray-500">
                                    <i class="fas fa-inbox text-3xl mb-2"></i>
                                    <p>Nenhuma faixa de quantidade configurada</p>
                                    <p class="text-sm">Clique no botão acima para adicionar</p>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>

                    <!-- Legenda -->
                    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-500 text-lg mr-3 mt-0.5"></i>
                            <div class="text-sm text-blue-700">
                                <p class="font-medium mb-2">Como preencher a tabela:</p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                                    <div class="space-y-1">
                                        <p><strong>• ESCUDO, A4, A3:</strong> Preço base para cada tamanho (já inclui 1 cor)</p>
                                        <p><strong>• COR +:</strong> Valor adicional por cor extra</p>
                                        <p><strong>• Exemplo:</strong> A4 (10 unid.) = R$ 5,46 + COR + (R$ 8,82) = R$ 14,28 total para 2 cores</p>
                                    </div>
                                    <div class="space-y-1">
                                        <p><strong>• Quantidade:</strong> Faixa mínima de peças</p>
                                        <p><strong>• 10 =</strong> de 10 a 29 peças</p>
                                        <p><strong>• 1000+ =</strong> 1000 ou mais peças</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Exemplo de Cálculo -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200 mb-8">
                <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4">
                    <div class="flex items-center">
                        <i class="fas fa-calculator text-white text-xl mr-3"></i>
                        <div>
                            <h2 class="text-xl font-semibold text-white">Calculadora de Exemplo</h2>
                            <p class="text-green-100 text-sm mt-1">Veja como o preço é calculado</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tamanho</label>
                            <select id="calc-size" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="ESCUDO">ESCUDO</option>
                                <option value="A4" selected>A4</option>
                                <option value="A3">A3</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Quantidade</label>
                            <input type="number" id="calc-qty" value="10" min="10" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cores</label>
                            <input type="number" id="calc-colors" value="1" min="1" max="10" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                        <div class="text-sm space-y-2" id="calc-result">
                            <div class="flex justify-between">
                                <span>Preço base:</span>
                                <span class="font-bold" id="calc-base">R$ 0,00</span>
                            </div>
                            <div class="flex justify-between text-yellow-700">
                                <span>Cores adicionais (<span id="calc-extra-colors">0</span> × <span id="calc-color-price">R$ 0,00</span>):</span>
                                <span class="font-bold" id="calc-color-total">R$ 0,00</span>
                            </div>
                            <div class="flex justify-between pt-2 border-t border-gray-300 text-lg">
                                <span class="font-bold">Total por peça:</span>
                                <span class="font-bold text-blue-600" id="calc-total">R$ 0,00</span>
                            </div>
                            <div class="flex justify-between text-green-600">
                                <span class="font-bold">Total do pedido (<span id="calc-qty-display">0</span> peças):</span>
                                <span class="font-bold text-xl" id="calc-order-total">R$ 0,00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botões de Ação -->
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                <div class="flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0">
                    <a href="{{ route('admin.personalization-prices.index') }}" 
                       class="group flex items-center px-6 py-3 text-gray-600 hover:text-gray-900 border border-gray-300 hover:border-gray-400 rounded-lg transition-all duration-200 hover:bg-gray-50">
                        <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i>
                        Cancelar
                    </a>
                    
                    <div class="flex items-center space-x-4">
                        <button type="button" onclick="previewPrices()" 
                                class="flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-all duration-200">
                            <i class="fas fa-eye mr-2"></i>
                            Pré-visualizar
                        </button>
                        <button type="submit" 
                                class="group flex items-center px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 hover:shadow-lg transform hover:-translate-y-0.5">
                            <i class="fas fa-save mr-2 group-hover:rotate-12 transition-transform"></i>
                            Salvar Tabela de Preços
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Dicas Importantes -->
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
                                    <span>Preencha os valores em Reais (R$) com centavos (ex: 5.46)</span>
                                </div>
                                <div class="flex items-start">
                                    <i class="fas fa-check-circle text-amber-500 text-xs mt-1 mr-2"></i>
                                    <span>Os preços base já incluem 1 cor - cores extras são adicionadas</span>
                                </div>
                                <div class="flex items-start">
                                    <i class="fas fa-check-circle text-amber-500 text-xs mt-1 mr-2"></i>
                                    <span>Use a calculadora para testar seus preços antes de salvar</span>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex items-start">
                                    <i class="fas fa-check-circle text-amber-500 text-xs mt-1 mr-2"></i>
                                    <span>Quanto maior a quantidade, menor deve ser o preço unitário</span>
                                </div>
                                <div class="flex items-start">
                                    <i class="fas fa-check-circle text-amber-500 text-xs mt-1 mr-2"></i>
                                    <span>As alterações se aplicam imediatamente a novos pedidos</span>
                                </div>
                                <div class="flex items-start">
                                    <i class="fas fa-check-circle text-amber-500 text-xs mt-1 mr-2"></i>
                                    <span>Pedidos existentes mantêm os preços originais</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let rowIndex = {{ is_array($quantityGroups) ? count($quantityGroups) : $quantityGroups->count() }};

        // Adicionar nova linha de quantidade
        function addQuantityRow() {
            const tbody = document.getElementById('price-tbody');
            const emptyMessage = document.getElementById('empty-message');
            
            if (emptyMessage) {
                emptyMessage.remove();
            }
            
            const newRow = document.createElement('tr');
            newRow.className = 'price-row hover:bg-blue-50 transition-colors fade-in';
            newRow.setAttribute('data-index', rowIndex);
            
            newRow.innerHTML = `
                <!-- De -->
                <td class="bg-gray-50">
                    <input type="number" 
                           name="prices[${rowIndex}][from]"
                           value=""
                           min="1"
                           required
                           onchange="updateRowCount()"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-bold text-center"
                           placeholder="10">
                </td>
                
                <!-- Até -->
                <td class="bg-gray-50">
                    <input type="number" 
                           name="prices[${rowIndex}][to]"
                           value=""
                           min="1"
                           onchange="updateRowCount()"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-bold text-center"
                           placeholder="29">
                </td>
                
                <!-- ESCUDO -->
                <td>
                    <input type="number" 
                           name="prices[${rowIndex}][ESCUDO]"
                           value=""
                           step="0.01"
                           min="0"
                           data-size="ESCUDO"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="0,00">
                </td>
                
                <!-- A4 -->
                <td>
                    <input type="number" 
                           name="prices[${rowIndex}][A4]"
                           value=""
                           step="0.01"
                           min="0"
                           data-size="A4"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="0,00">
                </td>
                
                <!-- A3 -->
                <td>
                    <input type="number" 
                           name="prices[${rowIndex}][A3]"
                           value=""
                           step="0.01"
                           min="0"
                           data-size="A3"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="0,00">
                </td>
                
                <!-- Cor Adicional -->
                <td class="bg-yellow-50">
                    <input type="number" 
                           name="colors[${rowIndex}][price]"
                           value=""
                           step="0.01"
                           min="0"
                           class="w-full px-3 py-2 border border-yellow-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 bg-white"
                           placeholder="0,00">
                </td>
                
                <!-- Botão Remover -->
                <td class="text-center">
                    <button type="button" 
                            onclick="removeRow(this)"
                            class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors"
                            title="Remover linha">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            
            tbody.appendChild(newRow);
            rowIndex++;
            updateRowCount();
            
            // Focar no primeiro input da nova linha
            newRow.querySelector('input').focus();
            
            showNotification('Nova linha adicionada! Preencha os valores.', 'success');
        }

        // Remover linha
        function removeRow(button) {
            const row = button.closest('tr');
            const from = row.querySelector('input[name*="[from]"]').value;
            const to = row.querySelector('input[name*="[to]"]').value;
            
            const message = from && to 
                ? `Tem certeza que deseja remover a faixa ${from}-${to}?`
                : 'Tem certeza que deseja remover esta linha?';
            
            if (confirm(message)) {
                row.style.animation = 'fadeOut 0.3s ease-out';
                setTimeout(() => {
                    row.remove();
                    updateRowCount();
                    
                    // Verificar se não há mais linhas
                    const tbody = document.getElementById('price-tbody');
                    if (tbody.children.length === 0) {
                        tbody.innerHTML = `
                            <tr id="empty-message">
                                <td colspan="7" class="text-center py-8 text-gray-500">
                                    <i class="fas fa-inbox text-3xl mb-2"></i>
                                    <p>Nenhuma faixa de quantidade configurada</p>
                                    <p class="text-sm">Clique no botão acima para adicionar</p>
                                </td>
                            </tr>
                        `;
                    }
                    
                    showNotification('Linha removida!', 'success');
                }, 300);
            }
        }

        // Atualizar contador de linhas
        function updateRowCount() {
            const rows = document.querySelectorAll('.price-row').length;
            const rowCountEl = document.getElementById('row-count');
            if (rowCountEl) {
                rowCountEl.textContent = `${rows} faixa${rows !== 1 ? 's' : ''} de quantidade`;
            }
        }

        // Mostrar notificação
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
                notification.style.animation = 'fadeOut 0.3s ease-out';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Calculadora em tempo real
        function updateCalculator() {
            const size = document.getElementById('calc-size').value;
            const qty = parseInt(document.getElementById('calc-qty').value) || 0;
            const colors = parseInt(document.getElementById('calc-colors').value) || 1;
            
            let basePrice = 0;
            let colorPrice = 0;
            
            // Buscar preço base na tabela
            const rows = document.querySelectorAll('.price-row');
            rows.forEach(row => {
                const fromInput = row.querySelector('input[name*="[from]"]');
                const toInput = row.querySelector('input[name*="[to]"]');
                const sizeInput = row.querySelector(`input[name*="[${size}]"]`);
                
                if (fromInput && toInput && sizeInput) {
                    const from = parseInt(fromInput.value) || 0;
                    const to = parseInt(toInput.value) || 999999;
                    
                    if (qty >= from && qty <= to) {
                        basePrice = parseFloat(sizeInput.value) || 0;
                    }
                }
            });
            
            // Buscar preço de cor na tabela
            rows.forEach(row => {
                const fromInput = row.querySelector('input[name*="[from]"]');
                const toInput = row.querySelector('input[name*="[to]"]');
                const colorInput = row.querySelector('input[name*="colors"][name*="[price]"]');
                
                if (fromInput && toInput && colorInput) {
                    const from = parseInt(fromInput.value) || 0;
                    const to = parseInt(toInput.value) || 999999;
                    
                    if (qty >= from && qty <= to) {
                        colorPrice = parseFloat(colorInput.value) || 0;
                    }
                }
            });
            
            const extraColors = Math.max(0, colors - 1);
            const colorTotal = extraColors * colorPrice;
            const totalPerPiece = basePrice + colorTotal;
            const orderTotal = totalPerPiece * qty;
            
            document.getElementById('calc-base').textContent = `R$ ${basePrice.toFixed(2).replace('.', ',')}`;
            document.getElementById('calc-extra-colors').textContent = extraColors;
            document.getElementById('calc-color-price').textContent = `R$ ${colorPrice.toFixed(2).replace('.', ',')}`;
            document.getElementById('calc-color-total').textContent = `R$ ${colorTotal.toFixed(2).replace('.', ',')}`;
            document.getElementById('calc-total').textContent = `R$ ${totalPerPiece.toFixed(2).replace('.', ',')}`;
            document.getElementById('calc-qty-display').textContent = qty;
            document.getElementById('calc-order-total').textContent = `R$ ${orderTotal.toFixed(2).replace('.', ',')}`;
        }
        
        document.getElementById('calc-size').addEventListener('change', updateCalculator);
        document.getElementById('calc-qty').addEventListener('input', updateCalculator);
        document.getElementById('calc-colors').addEventListener('input', updateCalculator);
        
        // Atualizar calculadora quando qualquer input da tabela mudar (usando event delegation)
        document.getElementById('price-tbody').addEventListener('input', function(e) {
            if (e.target.tagName === 'INPUT') {
                updateCalculator();
            }
        });
        
        // Pré-visualização
        function previewPrices() {
            const hasEmptyFields = Array.from(document.querySelectorAll('.price-table input[type="number"]'))
                .some(input => !input.value || parseFloat(input.value) === 0);
            
            if (hasEmptyFields) {
                alert('⚠️ Alguns campos estão vazios ou com valor zero. Preencha todos os campos antes de salvar.');
                return;
            }
            
            alert('✅ Todos os campos foram preenchidos! Você pode salvar os preços agora.');
        }
        
        // Validação do formulário
        document.getElementById('prices-form').addEventListener('submit', function(e) {
            const hasEmptyFields = Array.from(document.querySelectorAll('.price-table input[type="number"]'))
                .filter(input => input.name.includes('prices'))
                .some(input => !input.value || parseFloat(input.value) === 0);
            
            if (hasEmptyFields) {
                if (!confirm('⚠️ Alguns campos de preço estão vazios. Deseja salvar mesmo assim?')) {
                    e.preventDefault();
                }
            }
        });
        
        // Inicializar calculadora e contador
        document.addEventListener('DOMContentLoaded', function() {
            updateCalculator();
            updateRowCount();
        });
        
        // Formatação automática de valores
        document.querySelectorAll('.price-table input[type="number"]').forEach(input => {
            input.addEventListener('blur', function() {
                if (this.value) {
                    const value = parseFloat(this.value);
                    if (!isNaN(value)) {
                        this.value = value.toFixed(2);
                    }
                }
            });
        });
        
        // Debug: Log dos dados do formulário antes do envio
        document.querySelector('form').addEventListener('submit', function(e) {
            console.log('=== FORM SUBMIT DEBUG ===');
            const formData = new FormData(this);
            console.log('Form data:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }
            
            // Verificar se há valores na tabela
            const tableInputs = document.querySelectorAll('.price-table input[type="number"]');
            console.log('Table inputs count:', tableInputs.length);
            tableInputs.forEach((input, index) => {
                console.log(`Input ${index}: name="${input.name}", value="${input.value}"`);
            });
        });
    </script>
</body>
</html>
