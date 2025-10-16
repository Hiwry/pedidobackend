<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Configurar Preços - {{ $types[$type] }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <x-app-header />

    <div class="max-w-4xl mx-auto px-4 py-6">
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
                    <!-- Action Bar -->
                    <div class="flex items-center justify-between mb-6">
                        <span class="text-sm text-gray-500">{{ $prices->flatten()->count() }} faixas de quantidade</span>
                                <button type="button" onclick="addNewSize()" 
                                class="flex items-center px-4 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-800 focus:outline-none focus:ring-1 focus:ring-gray-500 transition-all text-sm font-medium">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Adicionar Faixa de Quantidade
                                </button>
                    </div>

                    <!-- Tabela de Preços -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DE</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ATÉ</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ESCUDO</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">A4</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">A3</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">COR +</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">AÇÕES</th>
                                </tr>
                            </thead>
                            <tbody id="prices-tbody">
                        @if($prices->count() > 0)
                                    @foreach($prices->flatten() as $index => $price)
                                        <tr class="price-row border-b border-gray-100">
                                            <td class="px-4 py-3">
                                                <input type="number" name="prices[{{ $index }}][quantity_from]" 
                                                       value="{{ $price->quantity_from }}" min="1"
                                                       class="w-full px-3 py-2 border border-gray-200 rounded text-sm focus:border-gray-400 focus:outline-none">
                                            </td>
                                            <td class="px-4 py-3">
                                                <input type="number" name="prices[{{ $index }}][quantity_to]" 
                                                       value="{{ $price->quantity_to }}" min="1"
                                                       class="w-full px-3 py-2 border border-gray-200 rounded text-sm focus:border-gray-400 focus:outline-none">
                                            </td>
                                            <td class="px-4 py-3">
                                                <input type="number" name="prices[{{ $index }}][price]" 
                                                       value="{{ $price->price }}" step="0.01" min="0"
                                                       class="w-full px-3 py-2 border border-gray-200 rounded text-sm focus:border-gray-400 focus:outline-none">
                                            </td>
                                            <td class="px-4 py-3">
                                                <input type="number" name="prices[{{ $index }}][price_a4]" 
                                                       value="{{ $price->price_a4 ?? '' }}" step="0.01" min="0"
                                                       class="w-full px-3 py-2 border border-gray-200 rounded text-sm focus:border-gray-400 focus:outline-none">
                                            </td>
                                            <td class="px-4 py-3">
                                                <input type="number" name="prices[{{ $index }}][price_a3]" 
                                                       value="{{ $price->price_a3 ?? '' }}" step="0.01" min="0"
                                                       class="w-full px-3 py-2 border border-gray-200 rounded text-sm focus:border-gray-400 focus:outline-none">
                                            </td>
                                            <td class="px-4 py-3">
                                                <input type="number" name="prices[{{ $index }}][color_price]" 
                                                       value="{{ $price->color_price ?? '' }}" step="0.01" min="0"
                                                       class="w-full px-3 py-2 border border-gray-200 rounded text-sm focus:border-gray-400 focus:outline-none">
                                            </td>
                                            <td class="px-4 py-3">
                                                <button type="button" onclick="removePriceRow(this)" 
                                                        class="text-gray-400 hover:text-gray-600 p-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                            @endforeach
                        @else
                                    <tr>
                                        <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                                            <p class="text-sm">Nenhuma faixa de preço configurada</p>
                                        </td>
                                    </tr>
                        @endif
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
            </div>
        </form>
        </div>
    </div>

    <script>
        let priceRowIndex = {{ $prices->flatten()->count() }};

        function addNewSize() {
            const tbody = document.getElementById('prices-tbody');
            const newRow = document.createElement('tr');
            newRow.className = 'price-row border-b border-gray-100';
            
            newRow.innerHTML = `
                <td class="px-4 py-3">
                    <input type="number" name="prices[${priceRowIndex}][quantity_from]" 
                           value="1" min="1"
                           class="w-full px-3 py-2 border border-gray-200 rounded text-sm focus:border-gray-400 focus:outline-none">
                </td>
                <td class="px-4 py-3">
                    <input type="number" name="prices[${priceRowIndex}][quantity_to]" 
                           value="" min="1"
                           class="w-full px-3 py-2 border border-gray-200 rounded text-sm focus:border-gray-400 focus:outline-none">
                </td>
                <td class="px-4 py-3">
                    <input type="number" name="prices[${priceRowIndex}][price]" 
                           value="0" step="0.01" min="0"
                           class="w-full px-3 py-2 border border-gray-200 rounded text-sm focus:border-gray-400 focus:outline-none">
                </td>
                <td class="px-4 py-3">
                    <input type="number" name="prices[${priceRowIndex}][price_a4]" 
                           value="" step="0.01" min="0"
                           class="w-full px-3 py-2 border border-gray-200 rounded text-sm focus:border-gray-400 focus:outline-none">
                </td>
                <td class="px-4 py-3">
                    <input type="number" name="prices[${priceRowIndex}][price_a3]" 
                           value="" step="0.01" min="0"
                           class="w-full px-3 py-2 border border-gray-200 rounded text-sm focus:border-gray-400 focus:outline-none">
                </td>
                <td class="px-4 py-3">
                    <input type="number" name="prices[${priceRowIndex}][color_price]" 
                           value="" step="0.01" min="0"
                           class="w-full px-3 py-2 border border-gray-200 rounded text-sm focus:border-gray-400 focus:outline-none">
                </td>
                <td class="px-4 py-3">
                    <button type="button" onclick="removePriceRow(this)" 
                            class="text-gray-400 hover:text-gray-600 p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                                </button>
                </td>
            `;
            
            tbody.appendChild(newRow);
            priceRowIndex++;
            
            // Remover mensagem de "nenhuma faixa" se existir
            const emptyRow = tbody.querySelector('tr td[colspan="7"]');
            if (emptyRow) {
                emptyRow.parentElement.remove();
            }
        }

        function removePriceRow(button) {
            const row = button.closest('tr');
            row.remove();
            
            // Se não há mais linhas, adicionar mensagem vazia
            const tbody = document.getElementById('prices-tbody');
            if (tbody.children.length === 0) {
                const emptyRow = document.createElement('tr');
                emptyRow.innerHTML = `
                    <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                        <p class="text-sm">Nenhuma faixa de preço configurada</p>
                    </td>
                `;
                tbody.appendChild(emptyRow);
            }
        }
    </script>
</body>
</html>
