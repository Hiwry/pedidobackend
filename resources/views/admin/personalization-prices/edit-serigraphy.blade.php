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
            background: rgb(79 70 229);
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
            ring-color: rgb(79 70 229);
        }
        .price-row {
            transition: all 0.2s ease;
        }
        .price-row:hover {
            background-color: #f9fafb !important;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <x-app-header />

    <div class="max-w-7xl mx-auto p-6">
        <!-- Header -->
        <div class="mb-8">
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background-color: rgb(79 70 229);">
                            <i class="fas fa-paint-brush text-white text-sm"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold" style="color: rgb(79 70 229);">Configurar Preços - Serigrafia</h1>
                            <p class="text-sm text-gray-600">Tabela de preços por tamanho e quantidade</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.personalization-prices.index') }}" 
                       class="text-sm" style="color: rgb(79 70 229);" onmouseover="this.style.color='rgb(67 56 202)'" onmouseout="this.style.color='rgb(79 70 229)'">
                        ← Voltar
                    </a>
                </div>
            </div>
        </div>

        <!-- Alertas -->
        @if(session('success'))
        <div class="mb-6">
            <div class="bg-gray-50 border border-gray-200 p-4 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-gray-600 text-lg mr-3"></i>
                    <div>
                        <p class="font-medium" style="color: rgb(79 70 229);">Sucesso!</p>
                        <p class="text-gray-700 text-sm">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if($errors->any())
        <div class="mb-6">
            <div class="bg-gray-50 border border-gray-200 p-4 rounded-lg">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-gray-600 text-lg mr-3 mt-0.5"></i>
                    <div>
                        <p class="font-medium mb-2" style="color: rgb(79 70 229);">Erro na validação:</p>
                        <ul class="text-gray-700 text-sm space-y-1">
                            @foreach($errors->all() as $error)
                                <li class="flex items-center">
                                    <i class="fas fa-dot-circle text-gray-500 text-xs mr-2"></i>
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

            <!-- Gerenciamento de Tamanhos de Aplicação -->
            <div class="bg-white rounded-lg border border-gray-200 mb-8">
                <div class="px-6 py-4" style="background-color: rgb(79 70 229);">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-ruler text-white text-lg mr-3"></i>
                            <div>
                                <h2 class="text-lg font-semibold text-white">Tamanhos de Aplicação</h2>
                                <p class="text-gray-300 text-sm mt-1">Gerencie os tamanhos disponíveis para serigrafia</p>
                            </div>
                        </div>
                        <button type="button" onclick="addNewSize()" 
                                class="flex items-center px-4 py-2 text-white rounded-lg text-sm" style="background-color: rgb(67 56 202);" onmouseover="this.style.backgroundColor='rgb(55 48 163)'" onmouseout="this.style.backgroundColor='rgb(67 56 202)'">
                            <i class="fas fa-plus mr-2"></i>
                            Adicionar Tamanho
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="sizes-container">
                        <!-- Tamanhos existentes serão carregados aqui -->
                        <div class="size-item border border-gray-200 rounded-lg p-4" data-size="ESCUDO">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <input type="text" value="ESCUDO" class="size-name-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-medium" onchange="updateSizeName(this, 'ESCUDO')">
                                    <p class="text-xs text-gray-500 mt-1">Tamanho de aplicação</p>
                                </div>
                                <button type="button" onclick="removeSize('ESCUDO')" 
                                        class="ml-3 p-2 rounded-lg transition-colors" style="color: rgb(79 70 229);" onmouseover="this.style.color='rgb(67 56 202)'; this.style.backgroundColor='rgb(243 244 246)';" onmouseout="this.style.color='rgb(79 70 229)'; this.style.backgroundColor='transparent';" title="Remover tamanho">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="size-item border border-gray-200 rounded-lg p-4" data-size="A4">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <input type="text" value="A4" class="size-name-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-medium" onchange="updateSizeName(this, 'A4')">
                                    <p class="text-xs text-gray-500 mt-1">Tamanho de aplicação</p>
                                </div>
                                <button type="button" onclick="removeSize('A4')" 
                                        class="ml-3 p-2 rounded-lg transition-colors" style="color: rgb(79 70 229);" onmouseover="this.style.color='rgb(67 56 202)'; this.style.backgroundColor='rgb(243 244 246)';" onmouseout="this.style.color='rgb(79 70 229)'; this.style.backgroundColor='transparent';" title="Remover tamanho">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="size-item border border-gray-200 rounded-lg p-4" data-size="A3">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <input type="text" value="A3" class="size-name-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-medium" onchange="updateSizeName(this, 'A3')">
                                    <p class="text-xs text-gray-500 mt-1">Tamanho de aplicação</p>
                                </div>
                                <button type="button" onclick="removeSize('A3')" 
                                        class="ml-3 p-2 rounded-lg transition-colors" style="color: rgb(79 70 229);" onmouseover="this.style.color='rgb(67 56 202)'; this.style.backgroundColor='rgb(243 244 246)';" onmouseout="this.style.color='rgb(79 70 229)'; this.style.backgroundColor='transparent';" title="Remover tamanho">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-gray-600 text-lg mr-3 mt-0.5"></i>
                            <div class="text-sm text-gray-700">
                                <p class="font-medium mb-2" style="color: rgb(79 70 229);">Como gerenciar tamanhos:</p>
                                <div class="space-y-1 text-xs">
                                    <p><strong>• Editar nome:</strong> Clique no campo de texto e digite o novo nome</p>
                                    <p><strong>• Adicionar:</strong> Use o botão "Adicionar Tamanho" para criar novos tamanhos</p>
                                    <p><strong>• Remover:</strong> Clique no ícone de lixeira para excluir um tamanho</p>
                                    <p><strong>• Importante:</strong> Remover um tamanho excluirá todos os preços associados</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabela de Preços Base (Tamanhos x Quantidades) -->
            <div class="bg-white rounded-lg border border-gray-200 mb-8">
                <div class="style="background-color: rgb(79 70 229);" px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-table text-white text-lg mr-3"></i>
                            <div>
                                <h2 class="text-lg font-semibold text-white">Preços Base por Tamanho e Quantidade</h2>
                                <p class="text-gray-300 text-sm mt-1">Preços incluem 1 cor - cores adicionais são cobradas separadamente</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6 overflow-x-auto">
                    <!-- Botão para adicionar nova linha de quantidade -->
                    <div class="mb-4 flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            <span id="row-count">0 faixas de quantidade</span>
                        </div>
                        <button type="button" onclick="addQuantityRow()" 
                                class="flex items-center px-4 py-2 text-white rounded-lg text-sm" style="background-color: rgb(79 70 229);" onmouseover="this.style.backgroundColor='rgb(67 56 202)'" onmouseout="this.style.backgroundColor='rgb(79 70 229)'">
                            <i class="fas fa-plus mr-2"></i>
                            Adicionar Faixa de Quantidade
                        </button>
                    </div>

                    <table class="price-table w-full border-collapse" id="price-table">
                        <thead>
                            <tr id="table-header">
                                <th class="rounded-tl-lg w-32">DE</th>
                                <th class="w-32">ATÉ</th>
                                <!-- Tamanhos dinâmicos serão inseridos aqui -->
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
                            <tr class="price-row hover:bg-gray-50 transition-colors" data-index="{{ $loop->index }}">
                                <!-- De -->
                                <td class="bg-gray-50">
                                    <input type="number" 
                                           name="prices[{{ $loop->index }}][from]"
                                           value="{{ $qtyGroup['from'] }}"
                                           min="1"
                                           required
                                           onchange="updateRowCount()"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-bold text-center"
                                           placeholder="10">
                                </td>
                                
                                <!-- Até -->
                                <td class="bg-gray-50">
                                    <input type="number" 
                                           name="prices[{{ $loop->index }}][to]"
                                           value="{{ $qtyGroup['to'] }}"
                                           min="1"
                                           onchange="updateRowCount()"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-bold text-center"
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
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                               placeholder="0,00">
                                    </td>
                                @endforeach
                                
                                <!-- Coluna de Cor Adicional - Removida da tabela principal -->
                                <td class="bg-gray-50">
                                    <div class="text-center text-gray-500 text-sm">
                                        <i class="fas fa-info-circle"></i>
                                        <br>Ver seção<br>de cores abaixo
                                    </div>
                                </td>
                                
                                <!-- Botão Remover -->
                                <td class="text-center">
                                    <button type="button" 
                                            onclick="removeRow(this)"
                                            class="p-2 rounded-lg transition-colors" style="color: rgb(79 70 229);" onmouseover="this.style.color='rgb(67 56 202)'; this.style.backgroundColor='rgb(243 244 246)';" onmouseout="this.style.color='rgb(79 70 229)'; this.style.backgroundColor='transparent';"
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

                    <!-- Seção Separada para Cores -->
                    <div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">
                                <i class="fas fa-palette text-purple-600 mr-2"></i>
                                Preços das Cores Adicionais
                            </h3>
                            <div class="text-sm text-gray-500">
                                Configure os preços para cores extras por faixa de quantidade
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($colors as $color)
                                @if($color->order > 1) {{-- Pular "1 Cor" que tem ordem 1 --}}
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    <div class="flex items-center justify-between mb-2">
                                        <label class="text-sm font-medium text-gray-700">
                                            {{ $color->name }}
                                        </label>
                                        <div class="text-xs text-gray-500">
                                            Ordem: {{ $color->order }}
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm text-gray-600">R$</span>
                                        <input type="number" 
                                               name="color_prices[{{ $color->id }}]"
                                               value="{{ $color->price }}"
                                               step="0.01"
                                               min="0"
                                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                               placeholder="0,00">
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                        
                        <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                            <div class="flex items-start">
                                <i class="fas fa-info-circle text-blue-600 mt-1 mr-2"></i>
                                <div class="text-sm text-blue-800">
                                    <strong>Como funciona:</strong> Estes preços são aplicados para cada cor adicional além da primeira cor (que já está incluída no preço base dos tamanhos).
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Legenda -->
                    <div class="mt-6 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-gray-600 text-lg mr-3 mt-0.5"></i>
                            <div class="text-sm text-gray-700">
                                <p class="font-medium mb-2" style="color: rgb(79 70 229);">Como preencher a tabela:</p>
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
            <div class="bg-white rounded-lg border border-gray-200 mb-8">
                <div class="style="background-color: rgb(79 70 229);" px-6 py-4">
                    <div class="flex items-center">
                        <i class="fas fa-calculator text-white text-lg mr-3"></i>
                        <div>
                            <h2 class="text-lg font-semibold text-white">Calculadora de Exemplo</h2>
                            <p class="text-gray-300 text-sm mt-1">Veja como o preço é calculado</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tamanho</label>
                            <select id="calc-size" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500">
                                <option value="ESCUDO">ESCUDO</option>
                                <option value="A4" selected>A4</option>
                                <option value="A3">A3</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Quantidade</label>
                            <input type="number" id="calc-qty" value="10" min="10" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cores</label>
                            <input type="number" id="calc-colors" value="1" min="1" max="10" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500">
                        </div>
                    </div>

                    <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                        <div class="text-sm space-y-2" id="calc-result">
                            <div class="flex justify-between">
                                <span>Preço base:</span>
                                <span class="font-bold" id="calc-base">R$ 0,00</span>
                            </div>
                            <div class="flex justify-between text-gray-700">
                                <span>Cores adicionais (<span id="calc-extra-colors">0</span> × <span id="calc-color-price">R$ 0,00</span>):</span>
                                <span class="font-bold" id="calc-color-total">R$ 0,00</span>
                            </div>
                            <div class="flex justify-between pt-2 border-t border-gray-300 text-lg">
                                <span class="font-bold">Total por peça:</span>
                                <span class="font-bold" id="calc-total" style="color: rgb(79 70 229);">R$ 0,00</span>
                            </div>
                            <div class="flex justify-between" style="color: rgb(79 70 229);">
                                <span class="font-bold">Total do pedido (<span id="calc-qty-display">0</span> peças):</span>
                                <span class="font-bold text-xl" id="calc-order-total">R$ 0,00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botões de Ação -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0">
                    <a href="{{ route('admin.personalization-prices.index') }}" 
                       class="text-sm" style="color: rgb(79 70 229);" onmouseover="this.style.color='rgb(67 56 202)'" onmouseout="this.style.color='rgb(79 70 229)'">
                        ← Cancelar
                    </a>
                    
                    <div class="flex items-center space-x-4">
                        <button type="button" onclick="previewPrices()" 
                                class="px-6 py-2 text-white rounded-lg text-sm" style="background-color: rgb(79 70 229);" onmouseover="this.style.backgroundColor='rgb(67 56 202)'" onmouseout="this.style.backgroundColor='rgb(79 70 229)'">
                            Pré-visualizar
                        </button>
                        <button type="submit" 
                                class="px-8 py-2 text-white rounded-lg text-sm font-medium" style="background-color: rgb(79 70 229);" onmouseover="this.style.backgroundColor='rgb(67 56 202)'" onmouseout="this.style.backgroundColor='rgb(79 70 229)'">
                            Salvar Tabela de Preços
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Dicas Importantes -->
        <div class="mt-8">
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="p-2 bg-gray-200 rounded-lg">
                            <i class="fas fa-lightbulb text-gray-600 text-lg"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold mb-3" style="color: rgb(79 70 229);">Dicas Importantes</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
                            <div class="space-y-2">
                                <div class="flex items-start">
                                    <i class="fas fa-check-circle text-gray-500 text-xs mt-1 mr-2"></i>
                                    <span>Preencha os valores em Reais (R$) com centavos (ex: 5.46)</span>
                                </div>
                                <div class="flex items-start">
                                    <i class="fas fa-check-circle text-gray-500 text-xs mt-1 mr-2"></i>
                                    <span>Os preços base já incluem 1 cor - cores extras são adicionadas</span>
                                </div>
                                <div class="flex items-start">
                                    <i class="fas fa-check-circle text-gray-500 text-xs mt-1 mr-2"></i>
                                    <span>Use a calculadora para testar seus preços antes de salvar</span>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex items-start">
                                    <i class="fas fa-check-circle text-gray-500 text-xs mt-1 mr-2"></i>
                                    <span>Quanto maior a quantidade, menor deve ser o preço unitário</span>
                                </div>
                                <div class="flex items-start">
                                    <i class="fas fa-check-circle text-gray-500 text-xs mt-1 mr-2"></i>
                                    <span>As alterações se aplicam imediatamente a novos pedidos</span>
                                </div>
                                <div class="flex items-start">
                                    <i class="fas fa-check-circle text-gray-500 text-xs mt-1 mr-2"></i>
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
        let availableSizes = ['ESCUDO', 'A4', 'A3']; // Tamanhos disponíveis
        let sizeCounter = 1; // Contador para novos tamanhos

        // Inicializar tabela com tamanhos padrão
        document.addEventListener('DOMContentLoaded', function() {
            // Sempre atualizar cabeçalhos para garantir que os tamanhos apareçam
            updateTableHeaders();
            updateCalculator();
            updateRowCount();
        });

        // Atualizar cabeçalhos da tabela baseado nos tamanhos disponíveis
        function updateTableHeaders() {
            const headerRow = document.getElementById('table-header');
            const existingHeaders = headerRow.querySelectorAll('th');
            
            // Verificar se já temos os cabeçalhos dos tamanhos
            const hasSizeHeaders = existingHeaders.length > 4; // DE, ATÉ, tamanhos, COR+, AÇÕES
            
            if (!hasSizeHeaders) {
                // Manter apenas DE, ATÉ, COR + e AÇÕES
                const fixedHeaders = [existingHeaders[0], existingHeaders[1], existingHeaders[existingHeaders.length - 2], existingHeaders[existingHeaders.length - 1]];
                
                // Limpar header
                headerRow.innerHTML = '';
                
                // Adicionar DE e ATÉ
                headerRow.appendChild(fixedHeaders[0]);
                headerRow.appendChild(fixedHeaders[1]);
                
                // Adicionar tamanhos dinâmicos
                availableSizes.forEach(size => {
                    const th = document.createElement('th');
                    th.textContent = size;
                    th.className = 'text-center';
                    headerRow.appendChild(th);
                });
                
                // Adicionar COR + e AÇÕES
                headerRow.appendChild(fixedHeaders[2]);
                headerRow.appendChild(fixedHeaders[3]);
                
                // Atualizar todas as linhas existentes
                updateAllTableRows();
            }
        }

        // Atualizar todas as linhas da tabela
        function updateAllTableRows() {
            const rows = document.querySelectorAll('.price-row');
            rows.forEach(row => {
                updateTableRow(row);
            });
        }

        // Atualizar uma linha específica da tabela
        function updateTableRow(row) {
            const cells = row.querySelectorAll('td');
            const fromCell = cells[0];
            const toCell = cells[1];
            const actionsCell = cells[cells.length - 1];
            
            // Coletar valores existentes ANTES de limpar a linha
            const existingValues = {};
            availableSizes.forEach(size => {
                const existingInput = row.querySelector(`input[name*="[${size}]"]`);
                if (existingInput) {
                    existingValues[size] = existingInput.value;
                }
            });
            
            // Limpar linha
            row.innerHTML = '';
            
            // Adicionar DE e ATÉ
            row.appendChild(fromCell);
            row.appendChild(toCell);
            
            // Adicionar células para cada tamanho
            availableSizes.forEach(size => {
                const td = document.createElement('td');
                const input = document.createElement('input');
                input.type = 'number';
                input.name = `prices[${row.dataset.index}][${size}]`;
                input.step = '0.01';
                input.min = '0';
                input.setAttribute('data-size', size);
                input.className = 'w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500';
                input.placeholder = '0,00';
                
                // Restaurar valor existente se houver
                if (existingValues[size]) {
                    input.value = existingValues[size];
                }
                
                td.appendChild(input);
                row.appendChild(td);
            });
            
            // Adicionar célula COR +
            const colorCell = document.createElement('td');
            colorCell.className = 'bg-gray-50';
            const colorInput = document.createElement('input');
            colorInput.type = 'number';
            colorInput.name = `colors[${row.dataset.index}][price]`;
            colorInput.step = '0.01';
            colorInput.min = '0';
            colorInput.className = 'w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white';
            colorInput.placeholder = '0,00';
            
            // Tentar manter valor existente se houver
            const existingColorInput = row.querySelector('input[name*="colors"][name*="[price]"]');
            if (existingColorInput) {
                colorInput.value = existingColorInput.value;
            }
            
            colorCell.appendChild(colorInput);
            row.appendChild(colorCell);
            
            // Adicionar AÇÕES
            row.appendChild(actionsCell);
        }

        // Adicionar novo tamanho
        function addNewSize() {
            const newSizeName = prompt('Digite o nome do novo tamanho:');
            if (!newSizeName || newSizeName.trim() === '') {
                return;
            }
            
            const sizeName = newSizeName.trim().toUpperCase();
            
            // Verificar se já existe
            if (availableSizes.includes(sizeName)) {
                alert('Este tamanho já existe!');
                return;
            }
            
            // Adicionar à lista
            availableSizes.push(sizeName);
            
            // Adicionar card do tamanho
            addSizeCard(sizeName);
            
            // Atualizar tabela
            updateTableHeaders();
            updateCalculatorOptions();
            
            showNotification(`Tamanho "${sizeName}" adicionado com sucesso!`, 'success');
        }

        // Adicionar card de tamanho
        function addSizeCard(sizeName) {
            const container = document.getElementById('sizes-container');
            const sizeCard = document.createElement('div');
            sizeCard.className = 'size-item border border-gray-200 rounded-lg p-4';
            sizeCard.setAttribute('data-size', sizeName);
            
            sizeCard.innerHTML = `
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <input type="text" value="${sizeName}" class="size-name-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-medium" onchange="updateSizeName(this, '${sizeName}')">
                        <p class="text-xs text-gray-500 mt-1">Tamanho de aplicação</p>
                    </div>
                    <button type="button" onclick="removeSize('${sizeName}')" 
                            class="ml-3 p-2 rounded-lg transition-colors" style="color: rgb(79 70 229);" onmouseover="this.style.color='rgb(67 56 202)'; this.style.backgroundColor='rgb(243 244 246)';" onmouseout="this.style.color='rgb(79 70 229)'; this.style.backgroundColor='transparent';" title="Remover tamanho">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            
            container.appendChild(sizeCard);
        }

        // Atualizar nome do tamanho
        function updateSizeName(input, oldName) {
            const newName = input.value.trim().toUpperCase();
            
            if (!newName) {
                alert('O nome do tamanho não pode estar vazio!');
                input.value = oldName;
                return;
            }
            
            if (newName !== oldName && availableSizes.includes(newName)) {
                alert('Este nome já existe!');
                input.value = oldName;
                return;
            }
            
            // Atualizar na lista
            const index = availableSizes.indexOf(oldName);
            if (index !== -1) {
                availableSizes[index] = newName;
            }
            
            // Atualizar data-size do card
            const card = input.closest('.size-item');
            card.setAttribute('data-size', newName);
            
            // Atualizar tabela
            updateTableHeaders();
            updateCalculatorOptions();
            
            showNotification(`Tamanho renomeado de "${oldName}" para "${newName}"`, 'success');
        }

        // Remover tamanho
        function removeSize(sizeName) {
            if (availableSizes.length <= 1) {
                alert('Deve haver pelo menos um tamanho!');
                return;
            }
            
            if (!confirm(`Tem certeza que deseja remover o tamanho "${sizeName}"?\n\nIsso excluirá todos os preços associados a este tamanho.`)) {
                return;
            }
            
            // Remover da lista
            const index = availableSizes.indexOf(sizeName);
            if (index !== -1) {
                availableSizes.splice(index, 1);
            }
            
            // Remover card
            const card = document.querySelector(`[data-size="${sizeName}"]`);
            if (card) {
                card.remove();
            }
            
            // Atualizar tabela
            updateTableHeaders();
            updateCalculatorOptions();
            
            showNotification(`Tamanho "${sizeName}" removido com sucesso!`, 'success');
        }

        // Adicionar nova linha de quantidade
        function addQuantityRow() {
            const tbody = document.getElementById('price-tbody');
            const emptyMessage = document.getElementById('empty-message');
            
            if (emptyMessage) {
                emptyMessage.remove();
            }
            
            const newRow = document.createElement('tr');
            newRow.className = 'price-row hover:bg-gray-50 transition-colors fade-in';
            newRow.setAttribute('data-index', rowIndex);
            
            // Criar HTML dinâmico baseado nos tamanhos disponíveis
            let html = `
                <!-- De -->
                <td class="bg-gray-50">
                    <input type="number" 
                           name="prices[${rowIndex}][from]"
                           value=""
                           min="1"
                           required
                           onchange="updateRowCount()"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-bold text-center"
                           placeholder="10">
                </td>
                
                <!-- Até -->
                <td class="bg-gray-50">
                    <input type="number" 
                           name="prices[${rowIndex}][to]"
                           value=""
                           min="1"
                           onchange="updateRowCount()"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-bold text-center"
                           placeholder="29">
                </td>
            `;
            
            // Adicionar células para cada tamanho disponível
            availableSizes.forEach(size => {
                html += `
                    <td>
                        <input type="number" 
                               name="prices[${rowIndex}][${size}]"
                               value=""
                               step="0.01"
                               min="0"
                               data-size="${size}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="0,00">
                    </td>
                `;
            });
            
            html += `
                <!-- Cor Adicional -->
                <td class="bg-gray-50">
                    <input type="number" 
                           name="colors[${rowIndex}][price]"
                           value=""
                           step="0.01"
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white"
                           placeholder="0,00">
                </td>
                
                <!-- Botão Remover -->
                <td class="text-center">
                    <button type="button" 
                            onclick="removeRow(this)"
                            class="p-2 rounded-lg transition-colors" style="color: rgb(79 70 229);" onmouseover="this.style.color='rgb(67 56 202)'; this.style.backgroundColor='rgb(243 244 246)';" onmouseout="this.style.color='rgb(79 70 229)'; this.style.backgroundColor='transparent';"
                            title="Remover linha">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            
            newRow.innerHTML = html;
            
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

        // Atualizar opções da calculadora quando tamanhos mudarem
        function updateCalculatorOptions() {
            const calcSizeSelect = document.getElementById('calc-size');
            const currentValue = calcSizeSelect.value;
            
            // Limpar opções existentes
            calcSizeSelect.innerHTML = '';
            
            // Adicionar opções baseadas nos tamanhos disponíveis
            availableSizes.forEach(size => {
                const option = document.createElement('option');
                option.value = size;
                option.textContent = size;
                if (size === currentValue) {
                    option.selected = true;
                }
                calcSizeSelect.appendChild(option);
            });
            
            // Se o tamanho atual não existe mais, selecionar o primeiro disponível
            if (!availableSizes.includes(currentValue) && availableSizes.length > 0) {
                calcSizeSelect.value = availableSizes[0];
            }
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
        
        // Inicializar calculadora e contador (já inicializado acima)
        
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
