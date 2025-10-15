<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($editData) ? 'Edição de Pedido' : 'Novo Pedido' }} - Costura e Personalização</title>
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Formulário de Adicionar Item -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <!-- Header -->
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-indigo-100 rounded-md flex items-center justify-center">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-lg font-semibold text-gray-900" id="form-title">Adicionar Novo Item</h1>
                                <p class="text-sm text-gray-600">Configure os detalhes do item de costura</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <form method="POST" action="{{ isset($editData) ? route('orders.edit-wizard.sewing') : route('orders.wizard.sewing') }}" id="sewing-form" class="space-y-6" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="action" value="add_item" id="form-action">
                            <input type="hidden" name="editing_item_id" value="" id="editing-item-id">

                            <!-- Seção: Personalização -->
                            <div class="space-y-3">
                                <div class="flex items-center space-x-2 mb-3">
                                    <div class="w-5 h-5 bg-indigo-100 rounded-md flex items-center justify-center">
                                        <svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                        </svg>
                                    </div>
                                    <h2 class="text-sm font-medium text-gray-900">Personalização *</h2>
                                </div>

                                <div class="bg-gray-50 rounded-md p-4">
                                    <p class="text-xs text-gray-600 mb-3">Selecione uma ou mais opções de personalização</p>
                                    <div class="grid grid-cols-2 gap-3" id="personalizacao-options">
                                        <!-- Será preenchido via JavaScript -->
                                    </div>
                                </div>
                            </div>

                            <!-- Seção: Tecido -->
                            <div class="space-y-3">
                                <div class="flex items-center space-x-2 mb-3">
                                    <div class="w-5 h-5 bg-indigo-100 rounded-md flex items-center justify-center">
                                        <svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                        </svg>
                                    </div>
                                    <h2 class="text-sm font-medium text-gray-900">Tecido</h2>
                                </div>

                                <div class="bg-gray-50 rounded-md p-4 space-y-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Tecido *</label>
                                            <select name="tecido" id="tecido" onchange="loadTiposTecido()" class="w-full px-3 py-2 rounded-md border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
                                                <option value="">Selecione o tecido</option>
                                            </select>
                                        </div>
                                        <div id="tipo-tecido-container" style="display:none">
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Tipo de Tecido</label>
                                            <select name="tipo_tecido" id="tipo_tecido" class="w-full px-3 py-2 rounded-md border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
                                                <option value="">Selecione o tipo</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Seção: Cor -->
                            <div class="space-y-3">
                                <div class="flex items-center space-x-2 mb-3">
                                    <div class="w-5 h-5 bg-indigo-100 rounded-md flex items-center justify-center">
                                        <svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"></path>
                                        </svg>
                                    </div>
                                    <h2 class="text-sm font-medium text-gray-900">Cor do Tecido</h2>
                                </div>

                                <div class="bg-gray-50 rounded-md p-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Cor *</label>
                                        <select name="cor" id="cor" class="w-full px-3 py-2 rounded-md border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
                                            <option value="">Selecione a cor</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Seção: Modelo e Detalhes -->
                            <div class="space-y-3">
                                <div class="flex items-center space-x-2 mb-3">
                                    <div class="w-5 h-5 bg-indigo-100 rounded-md flex items-center justify-center">
                                        <svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <h2 class="text-sm font-medium text-gray-900">Modelo e Detalhes</h2>
                                </div>

                                <div class="bg-gray-50 rounded-md p-4 space-y-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Tipo de Corte *</label>
                                            <select name="tipo_corte" id="tipo_corte" onchange="updatePrice()" class="w-full px-3 py-2 rounded-md border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
                                                <option value="">Selecione o corte</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Detalhe</label>
                                            <select name="detalhe" id="detalhe" onchange="updatePrice()" class="w-full px-3 py-2 rounded-md border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
                                                <option value="">Selecione o detalhe</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Gola *</label>
                                        <select name="gola" id="gola" onchange="updatePrice()" class="w-full px-3 py-2 rounded-md border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
                                            <option value="">Selecione a gola</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Seção: Tamanhos -->
                            <div class="space-y-3">
                                <div class="flex items-center space-x-2 mb-3">
                                    <div class="w-5 h-5 bg-indigo-100 rounded-md flex items-center justify-center">
                                        <svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m-9 0h10m-10 0a2 2 0 00-2 2v14a2 2 0 002 2h10a2 2 0 002-2V6a2 2 0 00-2-2M9 4h6"></path>
                                        </svg>
                                    </div>
                                    <h2 class="text-sm font-medium text-gray-900">Tamanhos e Quantidades</h2>
                                </div>

                                <div class="bg-gray-50 rounded-md p-4">
                                    <p class="text-xs text-gray-600 mb-4">Informe a quantidade para cada tamanho</p>
                                    
                                    <!-- Primeira linha: PP ao GG -->
                                    <div class="grid grid-cols-5 gap-3 mb-3">
                                        <div>
                                            <label class="block text-xs text-gray-600 mb-1">PP</label>
                                            <input type="number" name="tamanhos[PP]" min="0" value="0" onchange="calculateTotal()" class="w-full px-2 py-1 border border-gray-300 rounded text-center text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-600 mb-1">P</label>
                                            <input type="number" name="tamanhos[P]" min="0" value="0" onchange="calculateTotal()" class="w-full px-2 py-1 border border-gray-300 rounded text-center text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-600 mb-1">M</label>
                                            <input type="number" name="tamanhos[M]" min="0" value="0" onchange="calculateTotal()" class="w-full px-2 py-1 border border-gray-300 rounded text-center text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-600 mb-1">G</label>
                                            <input type="number" name="tamanhos[G]" min="0" value="0" onchange="calculateTotal()" class="w-full px-2 py-1 border border-gray-300 rounded text-center text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-600 mb-1">GG</label>
                                            <input type="number" name="tamanhos[GG]" min="0" value="0" onchange="calculateTotal()" class="w-full px-2 py-1 border border-gray-300 rounded text-center text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                                        </div>
                                    </div>

                                    <!-- Segunda linha: EXG ao Especial -->
                                    <div class="grid grid-cols-5 gap-3">
                                        <div>
                                            <label class="block text-xs text-gray-600 mb-1">EXG</label>
                                            <input type="number" name="tamanhos[EXG]" min="0" value="0" onchange="calculateTotal()" class="w-full px-2 py-1 border border-gray-300 rounded text-center text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-600 mb-1">G1</label>
                                            <input type="number" name="tamanhos[G1]" min="0" value="0" onchange="calculateTotal()" class="w-full px-2 py-1 border border-gray-300 rounded text-center text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-600 mb-1">G2</label>
                                            <input type="number" name="tamanhos[G2]" min="0" value="0" onchange="calculateTotal()" class="w-full px-2 py-1 border border-gray-300 rounded text-center text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-600 mb-1">G3</label>
                                            <input type="number" name="tamanhos[G3]" min="0" value="0" onchange="calculateTotal()" class="w-full px-2 py-1 border border-gray-300 rounded text-center text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-600 mb-1">Especial</label>
                                            <input type="number" name="tamanhos[Especial]" min="0" value="0" onchange="calculateTotal()" class="w-full px-2 py-1 border border-gray-300 rounded text-center text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                                        </div>
                                    </div>
                                    <div class="mt-4 text-right">
                                        <span class="text-sm font-medium text-gray-700">Total de peças: <span class="text-indigo-600 font-bold text-lg" id="total-pecas">0</span></span>
                                    </div>
                                    <input type="hidden" name="quantity" id="quantity" value="0">
                                </div>
                            </div>

                            <!-- Seção: Imagem de Capa do Item -->
                            <div class="space-y-3">
                                <div class="flex items-center space-x-2 mb-3">
                                    <div class="w-5 h-5 bg-indigo-100 rounded-md flex items-center justify-center">
                                        <svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <h2 class="text-sm font-medium text-gray-900">📸 Imagem de Capa do Item</h2>
                                </div>

                                <div class="bg-gray-50 rounded-md p-4">
                                    <p class="text-xs text-gray-600 mb-3">Imagem que será usada em impressões e PDFs (opcional)</p>
                                    <input type="file" name="item_cover_image" id="item_cover_image" accept="image/*"
                                           onchange="previewItemCoverImage(this)"
                                           class="w-full px-3 py-2 rounded-md border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm">
                                    <div id="item-cover-preview" class="mt-3 hidden">
                                        <img id="item-cover-preview-img" src="" alt="Preview" class="max-w-xs rounded-lg border border-gray-300">
                                    </div>
                                </div>
                            </div>

                            <!-- Seção: Preços -->
                            <div class="space-y-3">
                                <div class="flex items-center space-x-2 mb-3">
                                    <div class="w-5 h-5 bg-indigo-100 rounded-md flex items-center justify-center">
                                        <svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                    </div>
                                    <h2 class="text-sm font-medium text-gray-900">Preços</h2>
                                </div>

                                <div class="bg-gray-50 rounded-md p-4">
                                    <div class="space-y-3 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Tipo de Corte:</span>
                                            <span class="font-medium text-gray-900" id="price-corte">R$ 0,00</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Detalhe:</span>
                                            <span class="font-medium text-gray-900" id="price-detalhe">R$ 0,00</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Gola:</span>
                                            <span class="font-medium text-gray-900" id="price-gola">R$ 0,00</span>
                                        </div>
                                        <div class="flex justify-between pt-3 border-t border-gray-200">
                                            <span class="text-gray-900 font-semibold">Valor Unitário:</span>
                                            <span class="font-bold text-indigo-600 text-lg" id="price-total">R$ 0,00</span>
                                        </div>
                                    </div>
                                    <input type="hidden" name="unit_price" id="unit_price" value="0">
                                </div>
                            </div>

                            <!-- Botões de Ação -->
                            <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                                <a href="{{ isset($editData) ? route('orders.edit-wizard.client') : route('orders.wizard.client') }}" 
                                   class="flex items-center px-4 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-md transition-all text-sm font-medium">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                    Voltar
                                </a>
                                <button type="submit" id="submit-button" 
                                        class="flex items-center px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition-all text-sm font-medium">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Adicionar Item
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Lista de Itens Adicionados -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 sticky top-6">
                    <!-- Header -->
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-indigo-100 rounded-md flex items-center justify-center">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Itens do Pedido</h2>
                                <p class="text-sm text-gray-600">{{ $order->items->count() }} item(s) adicionado(s)</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        @if($order->items->count() > 0)
                            <div class="space-y-3">
                                @foreach($order->items as $item)
                                <div class="bg-gray-50 rounded-md p-4 border border-gray-200 hover:border-indigo-300 transition-colors {{ isset($editData) ? 'cursor-pointer' : '' }}" 
                                     @if(isset($editData)) onclick="editItem({{ $item->id }})" @endif>
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-6 h-6 bg-indigo-100 rounded-md flex items-center justify-center">
                                                <span class="text-xs font-medium text-indigo-600">{{ $item->item_number }}</span>
                                            </div>
                                            <h3 class="text-sm font-medium text-gray-900">Item {{ $item->item_number }}</h3>
                                        </div>
                                        <div class="flex space-x-1">
                                            @if(isset($editData))
                                            <button type="button" onclick="event.stopPropagation(); editItem({{ $item->id }})" 
                                                    class="p-1 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded" title="Editar item">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                            @endif
                                            <form method="POST" action="{{ isset($editData) ? route('orders.edit-wizard.sewing') : route('orders.wizard.sewing') }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="action" value="delete_item">
                                                <input type="hidden" name="item_id" value="{{ $item->id }}">
                                                <button type="submit" onclick="event.stopPropagation(); return confirm('Deseja remover este item?')" 
                                                        class="p-1 text-red-600 hover:text-red-800 hover:bg-red-50 rounded" title="Remover item">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="space-y-2 text-xs">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Personalização:</span>
                                            <span class="font-medium text-gray-900">{{ $item->print_type }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Tecido:</span>
                                            <span class="font-medium text-gray-900">{{ $item->fabric }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Cor:</span>
                                            <span class="font-medium text-gray-900">{{ $item->color }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Gola:</span>
                                            <span class="font-medium text-gray-900">{{ $item->collar }}</span>
                                        </div>
                                        @if($item->model)
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Modelo:</span>
                                            <span class="font-medium text-gray-900">{{ $item->model }}</span>
                                        </div>
                                        @endif
                                        @if($item->detail)
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Detalhe:</span>
                                            <span class="font-medium text-gray-900">{{ $item->detail }}</span>
                                        </div>
                                        @endif
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Quantidade:</span>
                                            <span class="font-medium text-gray-900">{{ $item->quantity }} peças</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Valor Unit.:</span>
                                            <span class="font-medium text-gray-900">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</span>
                                        </div>
                                        <div class="flex justify-between pt-2 border-t border-gray-200">
                                            <span class="text-gray-600 font-medium">Total:</span>
                                            <span class="font-bold text-indigo-600">R$ {{ number_format($item->total_price, 2, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            
                            <div class="mt-6 pt-4 border-t border-gray-200">
                                <div class="space-y-2 text-sm">
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

                                <form method="POST" action="{{ route('orders.wizard.sewing') }}" class="mt-6">
                                    @csrf
                                    <input type="hidden" name="action" value="finish">
                                    <button type="submit" class="w-full flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-1 focus:ring-green-500 transition-all text-sm font-medium">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                        </svg>
                                        Finalizar e Prosseguir
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-500 mb-2">Nenhum item adicionado ainda</p>
                                <p class="text-xs text-gray-400">Preencha o formulário ao lado para adicionar o primeiro item</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let options = {};
        let optionsWithParents = {};
        let selectedPersonalizacoes = [];

        document.addEventListener('DOMContentLoaded', function() {
            loadOptions();
        });

        function loadOptions() {
            fetch('/api/product-options')
                .then(response => response.json())
                .then(data => {
                    options = data;
                    return fetch('/api/product-options-with-parents');
                })
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
            
            if (select.value && !items.find(item => item.id == select.value)) {
                select.value = '';
                loadTiposTecido();
            }
        }

        function loadTiposTecido() {
            const tecidoId = document.getElementById('tecido').value;
            const container = document.getElementById('tipo-tecido-container');
            const select = document.getElementById('tipo_tecido');
            
            if (!tecidoId) {
                container.style.display = 'none';
                return;
            }

            const items = (options.tipo_tecido || []).filter(t => t.parent_id == tecidoId);
            
            if (items.length > 0) {
                container.style.display = 'block';
                select.innerHTML = '<option value="">Selecione o tipo</option>' + 
                    items.map(item => `<option value="${item.id}">${item.name}</option>`).join('');
                select.required = true;
            } else {
                container.style.display = 'none';
                select.required = false;
            }
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
            
            select.innerHTML = '<option value="">Selecione o corte</option>' + 
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

        document.getElementById('sewing-form').addEventListener('submit', function(e) {
            const checkboxes = document.querySelectorAll('.personalizacao-checkbox');
            const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
            
            if (checkedCount === 0) {
                e.preventDefault();
                alert('Por favor, selecione pelo menos uma personalização.');
                return false;
            }

            const quantity = parseInt(document.getElementById('quantity').value);
            if (quantity === 0) {
                e.preventDefault();
                alert('Por favor, adicione pelo menos uma peça nos tamanhos.');
                return false;
            }
            
            return true;
        });

        // Dados dos itens para edição
        const itemsData = {!! json_encode($order->items->map(function($item) {
            return [
                'id' => $item->id,
                'item_number' => $item->item_number,
                'print_type' => $item->print_type,
                'art_name' => $item->art_name,
                'quantity' => $item->quantity,
                'fabric' => $item->fabric,
                'color' => $item->color,
                'unit_price' => $item->unit_price,
                'collar' => $item->collar,
                'model' => $item->model,
                'detail' => $item->detail,
                'tipo_tecido' => $item->tipo_tecido ?? '',
                'total_price' => $item->total_price
            ];
        })) !!};

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
            document.getElementById('form-title').textContent = 'Editar Item ' + itemData.item_number;
            document.getElementById('submit-button').innerHTML = '💾 Salvar Alterações';

            // Preencher personalização
            const personalizacoes = itemData.print_type.split(', ');
            document.querySelectorAll('.personalizacao-checkbox').forEach(checkbox => {
                checkbox.checked = personalizacoes.includes(checkbox.value);
            });

            // Preencher tecido
            document.getElementById('tecido').value = itemData.fabric;
            loadTiposTecido();

            // Aguardar carregamento do tipo de tecido
            setTimeout(() => {
                if (itemData.tipo_tecido) {
                    document.getElementById('tipo_tecido').value = itemData.tipo_tecido;
                }
            }, 500);

            // Preencher cor
            document.getElementById('cor').value = itemData.color;

            // Preencher tipo de corte
            if (itemData.model) {
                document.getElementById('tipo_corte').value = itemData.model;
            }

            // Preencher detalhe
            if (itemData.detail) {
                document.getElementById('detalhe').value = itemData.detail;
            }

            // Preencher gola
            document.getElementById('gola').value = itemData.collar;

            // Preencher quantidades por tamanho
            const totalQuantity = itemData.quantity;
            const sizeInputs = document.querySelectorAll('input[name^="tamanhos"]');
            const quantityPerSize = Math.floor(totalQuantity / sizeInputs.length);
            const remainder = totalQuantity % sizeInputs.length;

            sizeInputs.forEach((input, index) => {
                input.value = quantityPerSize + (index < remainder ? 1 : 0);
            });

            // Preencher preço unitário
            document.getElementById('unit_price').value = itemData.unit_price;

            // Atualizar cálculos
            updatePrice();
            calculateTotal();

            // Scroll para o formulário
            document.getElementById('sewing-form').scrollIntoView({ behavior: 'smooth' });
        }

        // Função para cancelar edição
        function cancelEdit() {
            document.getElementById('editing-item-id').value = '';
            document.getElementById('form-action').value = 'add_item';
            document.getElementById('form-title').textContent = 'Adicionar Novo Item';
            document.getElementById('submit-button').innerHTML = '➕ Adicionar Item';
            
            // Limpar formulário
            document.getElementById('sewing-form').reset();
            
            // Desmarcar checkboxes de personalização
            document.querySelectorAll('.personalizacao-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
        }

        // Função para preview da imagem de capa do item
        function previewItemCoverImage(input) {
            const preview = document.getElementById('item-cover-preview');
            const previewImg = document.getElementById('item-cover-preview-img');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.classList.add('hidden');
            }
        }

        // Adicionar botão de cancelar edição se estiver editando
        @if(isset($editData))
        document.addEventListener('DOMContentLoaded', function() {
            const submitButton = document.getElementById('submit-button');
            const cancelButton = document.createElement('button');
            cancelButton.type = 'button';
            cancelButton.className = 'px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg';
            cancelButton.innerHTML = '❌ Cancelar Edição';
            cancelButton.onclick = cancelEdit;
            
            submitButton.parentNode.insertBefore(cancelButton, submitButton.nextSibling);
        });
        @endif
    </script>
</body>
</html>