<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edição de Pedido - Costura e Personalização</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Lista de Itens Adicionados -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-6">
                    <h2 class="text-lg font-semibold mb-4">Itens do Pedido</h2>
                    
                    @if(isset($editData['items']) && count($editData['items']) > 0)
                        <div class="space-y-3">
                            @foreach($editData['items'] as $index => $item)
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="font-semibold text-indigo-600">Item {{ $index + 1 }}</h3>
                                </div>
                                <div class="text-sm text-gray-600">
                                    <p><strong>Tipo:</strong> {{ $item['print_type'] ?? 'Não definido' }}</p>
                                    <p><strong>Arte:</strong> {{ $item['art_name'] ?? 'Não definido' }}</p>
                                    <p><strong>Quantidade:</strong> {{ $item['quantity'] ?? 0 }}</p>
                                    <p><strong>Tecido:</strong> {{ $item['fabric'] ?? 'Não definido' }}</p>
                                    <p><strong>Cor:</strong> {{ $item['color'] ?? 'Não definido' }}</p>
                                    <p><strong>Preço:</strong> R$ {{ number_format($item['unit_price'] ?? 0, 2, ',', '.') }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-gray-500 py-8">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <p>Nenhum item adicionado ainda</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Formulário de Adição de Itens -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-6">Adicionar/Editar Itens</h2>
                    
                    <form method="POST" action="{{ route('orders.edit-wizard.sewing') }}" id="items-form">
                        @csrf
                        
                        <div id="items-container">
                            @if(isset($editData['items']) && count($editData['items']) > 0)
                                @foreach($editData['items'] as $index => $item)
                                <div class="item-form bg-gray-50 p-6 rounded-lg mb-6 border">
                                    <div class="flex justify-between items-center mb-4">
                                        <h3 class="text-lg font-semibold">Item {{ $index + 1 }}</h3>
                                        <button type="button" onclick="removeItem(this)" class="text-red-600 hover:text-red-800">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                        <!-- Tipo de Personalização -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Personalização *</label>
                                            <select name="items[{{ $index }}][print_type]" required
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                <option value="">Selecione...</option>
                                                <option value="BORDADO" {{ ($item['print_type'] ?? '') == 'BORDADO' ? 'selected' : '' }}>Bordado</option>
                                                <option value="SUBLIMACAO" {{ ($item['print_type'] ?? '') == 'SUBLIMACAO' ? 'selected' : '' }}>Sublimação</option>
                                                <option value="SERIGRAFIA" {{ ($item['print_type'] ?? '') == 'SERIGRAFIA' ? 'selected' : '' }}>Serigrafia</option>
                                                <option value="VINIL" {{ ($item['print_type'] ?? '') == 'VINIL' ? 'selected' : '' }}>Vinil</option>
                                                <option value="DIGITAL" {{ ($item['print_type'] ?? '') == 'DIGITAL' ? 'selected' : '' }}>Digital</option>
                                            </select>
                                        </div>

                                        <!-- Nome da Arte -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Nome da Arte *</label>
                                            <input type="text" name="items[{{ $index }}][art_name]" value="{{ $item['art_name'] ?? '' }}" required
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                                   placeholder="Ex: Logo Empresa XYZ">
                                        </div>

                                        <!-- Quantidade -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Quantidade *</label>
                                            <input type="number" name="items[{{ $index }}][quantity]" value="{{ $item['quantity'] ?? 1 }}" min="1" required
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        </div>

                                        <!-- Tecido -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Tecido *</label>
                                            <select name="items[{{ $index }}][fabric]" required
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                <option value="">Selecione...</option>
                                                <option value="Algodão - PP" {{ ($item['fabric'] ?? '') == 'Algodão - PP' ? 'selected' : '' }}>Algodão - PP</option>
                                                <option value="Algodão - P" {{ ($item['fabric'] ?? '') == 'Algodão - P' ? 'selected' : '' }}>Algodão - P</option>
                                                <option value="Algodão - M" {{ ($item['fabric'] ?? '') == 'Algodão - M' ? 'selected' : '' }}>Algodão - M</option>
                                                <option value="Algodão - G" {{ ($item['fabric'] ?? '') == 'Algodão - G' ? 'selected' : '' }}>Algodão - G</option>
                                                <option value="Algodão - GG" {{ ($item['fabric'] ?? '') == 'Algodão - GG' ? 'selected' : '' }}>Algodão - GG</option>
                                                <option value="Algodão - XGG" {{ ($item['fabric'] ?? '') == 'Algodão - XGG' ? 'selected' : '' }}>Algodão - XGG</option>
                                                <option value="Malha - PP" {{ ($item['fabric'] ?? '') == 'Malha - PP' ? 'selected' : '' }}>Malha - PP</option>
                                                <option value="Malha - P" {{ ($item['fabric'] ?? '') == 'Malha - P' ? 'selected' : '' }}>Malha - P</option>
                                                <option value="Malha - M" {{ ($item['fabric'] ?? '') == 'Malha - M' ? 'selected' : '' }}>Malha - M</option>
                                                <option value="Malha - G" {{ ($item['fabric'] ?? '') == 'Malha - G' ? 'selected' : '' }}>Malha - G</option>
                                                <option value="Malha - GG" {{ ($item['fabric'] ?? '') == 'Malha - GG' ? 'selected' : '' }}>Malha - GG</option>
                                                <option value="Malha - XGG" {{ ($item['fabric'] ?? '') == 'Malha - XGG' ? 'selected' : '' }}>Malha - XGG</option>
                                            </select>
                                        </div>

                                        <!-- Cor -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Cor *</label>
                                            <select name="items[{{ $index }}][color]" required
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                <option value="">Selecione...</option>
                                                <option value="Branco" {{ ($item['color'] ?? '') == 'Branco' ? 'selected' : '' }}>Branco</option>
                                                <option value="Preto" {{ ($item['color'] ?? '') == 'Preto' ? 'selected' : '' }}>Preto</option>
                                                <option value="Azul" {{ ($item['color'] ?? '') == 'Azul' ? 'selected' : '' }}>Azul</option>
                                                <option value="Vermelho" {{ ($item['color'] ?? '') == 'Vermelho' ? 'selected' : '' }}>Vermelho</option>
                                                <option value="Verde" {{ ($item['color'] ?? '') == 'Verde' ? 'selected' : '' }}>Verde</option>
                                                <option value="Amarelo" {{ ($item['color'] ?? '') == 'Amarelo' ? 'selected' : '' }}>Amarelo</option>
                                                <option value="Rosa" {{ ($item['color'] ?? '') == 'Rosa' ? 'selected' : '' }}>Rosa</option>
                                                <option value="Cinza" {{ ($item['color'] ?? '') == 'Cinza' ? 'selected' : '' }}>Cinza</option>
                                                <option value="Marrom" {{ ($item['color'] ?? '') == 'Marrom' ? 'selected' : '' }}>Marrom</option>
                                                <option value="Outro" {{ ($item['color'] ?? '') == 'Outro' ? 'selected' : '' }}>Outro</option>
                                            </select>
                                        </div>

                                        <!-- Preço Unitário -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Preço Unitário (R$) *</label>
                                            <input type="number" name="items[{{ $index }}][unit_price]" value="{{ $item['unit_price'] ?? 0 }}" step="0.01" min="0" required
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <!-- Item padrão baseado no pedido original -->
                                @foreach($order->items as $index => $item)
                                <div class="item-form bg-gray-50 p-6 rounded-lg mb-6 border">
                                    <div class="flex justify-between items-center mb-4">
                                        <h3 class="text-lg font-semibold">Item {{ $index + 1 }}</h3>
                                        <button type="button" onclick="removeItem(this)" class="text-red-600 hover:text-red-800">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                        <!-- Tipo de Personalização -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Personalização *</label>
                                            <select name="items[{{ $index }}][print_type]" required
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
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
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
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
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @endif
                        </div>

                        <div class="flex justify-between mt-8">
                            <a href="{{ route('orders.edit-wizard.client') }}" 
                               class="px-6 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                                ← Voltar
                            </a>
                            
                            <div class="flex gap-3">
                                <button type="button" onclick="addItem()" 
                                        class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                    + Adicionar Item
                                </button>
                                <button type="submit" 
                                        class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                    Próximo →
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let itemIndex = {{ isset($editData['items']) ? count($editData['items']) : $order->items->count() }};

        function addItem() {
            const container = document.getElementById('items-container');
            const itemHtml = `
                <div class="item-form bg-gray-50 p-6 rounded-lg mb-6 border">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Item ${itemIndex + 1}</h3>
                        <button type="button" onclick="removeItem(this)" class="text-red-600 hover:text-red-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Personalização *</label>
                            <select name="items[${itemIndex}][print_type]" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">Selecione...</option>
                                <option value="BORDADO">Bordado</option>
                                <option value="SUBLIMACAO">Sublimação</option>
                                <option value="SERIGRAFIA">Serigrafia</option>
                                <option value="VINIL">Vinil</option>
                                <option value="DIGITAL">Digital</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nome da Arte *</label>
                            <input type="text" name="items[${itemIndex}][art_name]" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                   placeholder="Ex: Logo Empresa XYZ">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Quantidade *</label>
                            <input type="number" name="items[${itemIndex}][quantity]" value="1" min="1" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tecido *</label>
                            <select name="items[${itemIndex}][fabric]" required
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
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cor *</label>
                            <select name="items[${itemIndex}][color]" required
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
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Preço Unitário (R$) *</label>
                            <input type="number" name="items[${itemIndex}][unit_price]" value="0" step="0.01" min="0" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', itemHtml);
            itemIndex++;
        }

        function removeItem(button) {
            const itemForm = button.closest('.item-form');
            itemForm.remove();
            
            // Renumerar os itens restantes
            const remainingItems = document.querySelectorAll('.item-form');
            remainingItems.forEach((item, index) => {
                const title = item.querySelector('h3');
                title.textContent = `Item ${index + 1}`;
            });
        }
    </script>
</body>
</html>
