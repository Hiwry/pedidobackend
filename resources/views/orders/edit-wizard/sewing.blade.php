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
                        <p class="text-xs text-gray-500">Etapa 2 de 5 - Edição</p>
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
            <!-- Formulário de Adicionar/Editar Item -->
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
                        <form method="POST" action="{{ route('orders.edit-wizard.sewing') }}" id="sewing-form" class="space-y-6" enctype="multipart/form-data">
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
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"></path>
                                            </svg>
                                    </div>
                                    <h2 class="text-sm font-medium text-gray-900">Tecido *</h2>
                                    </div>
                                    
                                <div class="bg-gray-50 rounded-md p-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Tecido *</label>
                                            <select name="tecido" id="tecido" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                <option value="">Selecione o tecido</option>
                                                <!-- Será preenchido via JavaScript -->
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Tecido</label>
                                            <select name="tipo_tecido" id="tipo_tecido" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                <option value="">Selecione o tipo</option>
                                                <!-- Será preenchido via JavaScript -->
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Seção: Cor do Tecido -->
                            <div class="space-y-3">
                                <div class="flex items-center space-x-2 mb-3">
                                    <div class="w-5 h-5 bg-indigo-100 rounded-md flex items-center justify-center">
                                        <svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"></path>
                                        </svg>
                                    </div>
                                    <h2 class="text-sm font-medium text-gray-900">Cor do Tecido *</h2>
                                        </div>

                                <div class="bg-gray-50 rounded-md p-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Cor *</label>
                                            <select name="cor" id="cor" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                <option value="">Selecione a cor</option>
                                                <!-- Será preenchido via JavaScript -->
                                            </select>
                                        </div>
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

                                <div class="bg-gray-50 rounded-md p-4">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Corte *</label>
                                            <select name="tipo_corte" id="tipo_corte" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                <option value="">Selecione o corte</option>
                                                <!-- Será preenchido via JavaScript -->
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Detalhe</label>
                                            <select name="detalhe" id="detalhe" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                <option value="">Selecione o detalhe</option>
                                                <!-- Será preenchido via JavaScript -->
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Gola *</label>
                                            <select name="gola" id="gola" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                <option value="">Selecione a gola</option>
                                                <!-- Será preenchido via JavaScript -->
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Seção: Tamanhos e Quantidades -->
                            <div class="space-y-3">
                                <div class="flex items-center space-x-2 mb-3">
                                    <div class="w-5 h-5 bg-indigo-100 rounded-md flex items-center justify-center">
                                        <svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                    </div>
                                    <h2 class="text-sm font-medium text-gray-900">Tamanhos e Quantidades</h2>
                                    </div>
                                    
                                <div class="bg-gray-50 rounded-md p-4">
                                    <div class="grid grid-cols-5 gap-3 mb-4">
                                        <div class="text-center">
                                            <label class="block text-xs font-medium text-gray-700 mb-1">PP</label>
                                            <input type="number" name="tamanhos[PP]" value="0" min="0" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                        </div>
                                        <div class="text-center">
                                            <label class="block text-xs font-medium text-gray-700 mb-1">P</label>
                                            <input type="number" name="tamanhos[P]" value="0" min="0" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                        </div>
                                        <div class="text-center">
                                            <label class="block text-xs font-medium text-gray-700 mb-1">M</label>
                                            <input type="number" name="tamanhos[M]" value="0" min="0" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                        </div>
                                        <div class="text-center">
                                            <label class="block text-xs font-medium text-gray-700 mb-1">G</label>
                                            <input type="number" name="tamanhos[G]" value="0" min="0" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                        </div>
                                        <div class="text-center">
                                            <label class="block text-xs font-medium text-gray-700 mb-1">GG</label>
                                            <input type="number" name="tamanhos[GG]" value="0" min="0" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                        </div>
                                        <div class="text-center">
                                            <label class="block text-xs font-medium text-gray-700 mb-1">EXG</label>
                                            <input type="number" name="tamanhos[EXG]" value="0" min="0" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                        </div>
                                        <div class="text-center">
                                            <label class="block text-xs font-medium text-gray-700 mb-1">G1</label>
                                            <input type="number" name="tamanhos[G1]" value="0" min="0" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                        </div>
                                        <div class="text-center">
                                            <label class="block text-xs font-medium text-gray-700 mb-1">G2</label>
                                            <input type="number" name="tamanhos[G2]" value="0" min="0" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                        </div>
                                        <div class="text-center">
                                            <label class="block text-xs font-medium text-gray-700 mb-1">G3</label>
                                            <input type="number" name="tamanhos[G3]" value="0" min="0" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                        </div>
                                        <div class="text-center">
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Especial</label>
                                            <input type="number" name="tamanhos[Especial]" value="0" min="0" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-sm text-gray-600">Total de peças: <span id="total-quantity">0</span></p>
                                    </div>
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
                                    <h2 class="text-sm font-medium text-gray-900">Imagem de Capa do Item</h2>
                                        </div>

                                <div class="bg-gray-50 rounded-md p-4">
                                    <div class="flex items-center justify-center w-full">
                                        <label for="item_cover_image" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                <svg class="w-8 h-8 mb-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                                </svg>
                                                <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Clique para fazer upload</span> ou arraste e solte</p>
                                                <p class="text-xs text-gray-500">PNG, JPG ou GIF (MAX. 10MB)</p>
                                            </div>
                                            <input id="item_cover_image" name="item_cover_image" type="file" class="hidden" accept="image/*">
                                        </label>
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
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Quantidade Total</label>
                                            <input type="number" name="quantity" id="quantity" value="0" min="1" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" readonly>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Valor Unitário (R$)</label>
                                            <input type="number" name="unit_price" id="unit_price" value="0" step="0.01" min="0" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" readonly>
                                        </div>
                                    </div>
                                    <div class="mt-4 p-3 bg-indigo-50 rounded-md">
                                        <div class="text-sm text-indigo-600">
                                            <p>Tipo de Corte: R$ <span id="corte-price">0,00</span></p>
                                            <p>Detalhe: R$ <span id="detalhe-price">0,00</span></p>
                                            <p>Gola: R$ <span id="gola-price">0,00</span></p>
                                            <p class="font-medium">Valor Unitário: R$ <span id="total-unit-price">0,00</span></p>
                                        </div>
                                    </div>
                                </div>
                        </div>

                            <!-- Botões de Ação -->
                            <div class="flex justify-between pt-6 border-t border-gray-200">
                                <button type="button" id="cancel-edit-btn" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 hidden">
                                    ✕ Cancelar Edição
                                </button>
                                <div class="flex gap-3">
                                    <button type="button" id="add-item-btn" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                        + Adicionar Item
                                    </button>
                                    <button type="button" id="update-item-btn" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 hidden">
                                        ✓ Salvar Alterações
                                </button>
                            </div>
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
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-900">Itens do Pedido</h2>
                            <span class="text-sm text-gray-500" id="items-count">0 item(s) adicionado(s)</span>
                        </div>
                    </div>
                    
                    <!-- Lista de Itens -->
                    <div class="p-6">
                        <div id="items-list" class="space-y-4">
                            <!-- Itens serão adicionados aqui via JavaScript -->
                        </div>

                        <!-- Resumo -->
                        <div class="mt-6 p-4 bg-gray-50 rounded-md">
                            <div class="text-sm text-gray-600 space-y-1">
                                <div class="flex justify-between">
                                    <span>Total de Itens:</span>
                                    <span id="total-items">0</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Total de Peças:</span>
                                    <span id="total-pieces">0</span>
                        </div>
                                <div class="flex justify-between font-medium text-indigo-600">
                                    <span>Subtotal:</span>
                                    <span id="subtotal">R$ 0,00</span>
                        </div>
                        </div>
                        </div>

                        <!-- Botão Finalizar -->
                        <div class="mt-6">
                            <button type="button" id="finish-btn" class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:bg-gray-300 disabled:cursor-not-allowed" disabled>
                                → Finalizar e Prosseguir
                            </button>
                        </div>
                    </div>
                </div>
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
        // Dados dos itens atuais do pedido
        const currentItems = @json($currentItemsData);

        // Dados das opções de produto
        const productOptions = @json($productOptionsData);

        let items = [...currentItems];
        let editingIndex = null;

        // Inicializar a página
        document.addEventListener('DOMContentLoaded', function() {
            console.log('=== INICIALIZANDO PÁGINA ===');
            console.log('Current items:', currentItems);
            console.log('Items array:', items);
            console.log('Product options:', productOptions);
            
            loadProductOptions();
            updateItemsList();
            updateSummary();
            setupEventListeners();
        });

        function loadProductOptions() {
            console.log('=== CARREGANDO OPÇÕES DE PRODUTO ===');
            console.log('Product options:', productOptions);
            
            // Carregar opções de personalização
            const personalizacaoContainer = document.getElementById('personalizacao-options');
            if (productOptions.personalizacao) {
                console.log('Carregando personalizações:', productOptions.personalizacao);
                productOptions.personalizacao.forEach(option => {
                    const checkbox = document.createElement('div');
                    checkbox.className = 'flex items-center';
                    checkbox.innerHTML = `
                        <input type="checkbox" name="personalizacao[]" value="${option.id}" id="personalizacao_${option.id}" class="mr-2">
                        <label for="personalizacao_${option.id}" class="text-sm text-gray-700">${option.name}</label>
                    `;
                    personalizacaoContainer.appendChild(checkbox);
                });
            } else {
                console.log('Nenhuma opção de personalização encontrada');
            }

            // Carregar outras opções
            loadSelectOptions('tecido', productOptions.tecido);
            loadSelectOptions('tipo_tecido', productOptions.tipo_tecido);
            loadSelectOptions('cor', productOptions.cor);
            loadSelectOptions('tipo_corte', productOptions.tipo_corte);
            loadSelectOptions('detalhe', productOptions.detalhe);
            loadSelectOptions('gola', productOptions.gola);
        }

        function loadSelectOptions(selectId, options) {
            const select = document.getElementById(selectId);
            if (select && options) {
                options.forEach(option => {
                    const optionElement = document.createElement('option');
                    optionElement.value = option.id;
                    optionElement.textContent = option.name;
                    select.appendChild(optionElement);
                });
            }
        }

        function setupEventListeners() {
            // Event listeners para tamanhos
            document.querySelectorAll('input[name^="tamanhos["]').forEach(input => {
                input.addEventListener('input', updateQuantity);
            });

            // Event listeners para preços
            document.getElementById('tipo_corte').addEventListener('change', updatePrices);
            document.getElementById('detalhe').addEventListener('change', updatePrices);
            document.getElementById('gola').addEventListener('change', updatePrices);

            // Botões
            document.getElementById('add-item-btn').addEventListener('click', addItem);
            document.getElementById('update-item-btn').addEventListener('click', updateItem);
            document.getElementById('cancel-edit-btn').addEventListener('click', cancelEdit);
            document.getElementById('finish-btn').addEventListener('click', finishStep);
        }

        function updateQuantity() {
            const sizeInputs = document.querySelectorAll('input[name^="tamanhos["]');
            let total = 0;
            sizeInputs.forEach(input => {
                total += parseInt(input.value) || 0;
            });
            document.getElementById('total-quantity').textContent = total;
            document.getElementById('quantity').value = total;
        }

        function updatePrices() {
            const corteSelect = document.getElementById('tipo_corte');
            const detalheSelect = document.getElementById('detalhe');
            const golaSelect = document.getElementById('gola');

            const cortePrice = parseFloat(corteSelect.selectedOptions[0]?.dataset.price || 0);
            const detalhePrice = parseFloat(detalheSelect.selectedOptions[0]?.dataset.price || 0);
            const golaPrice = parseFloat(golaSelect.selectedOptions[0]?.dataset.price || 0);

            const totalPrice = cortePrice + detalhePrice + golaPrice;

            document.getElementById('corte-price').textContent = cortePrice.toFixed(2).replace('.', ',');
            document.getElementById('detalhe-price').textContent = detalhePrice.toFixed(2).replace('.', ',');
            document.getElementById('gola-price').textContent = golaPrice.toFixed(2).replace('.', ',');
            document.getElementById('total-unit-price').textContent = totalPrice.toFixed(2).replace('.', ',');
            document.getElementById('unit_price').value = totalPrice;
        }

        function addItem() {
            const formData = new FormData(document.getElementById('sewing-form'));
            
            // Validar campos obrigatórios
            if (!validateForm()) return;

            const item = {
                id: Date.now(), // ID temporário para novos itens
                print_type: getSelectedPersonalizations(),
                art_name: formData.get('art_name') || '',
                quantity: parseInt(formData.get('quantity')),
                fabric: getOptionName('tecido', formData.get('tecido')),
                color: getOptionName('cor', formData.get('cor')),
                collar: getOptionName('gola', formData.get('gola')),
                model: getOptionName('tipo_corte', formData.get('tipo_corte')),
                detail: getOptionName('detalhe', formData.get('detalhe')),
                unit_price: parseFloat(formData.get('unit_price')),
                total_price: parseInt(formData.get('quantity')) * parseFloat(formData.get('unit_price')),
                sizes: getSizesData()
            };

            items.push(item);
            updateItemsList();
            updateSummary();
            resetForm();
        }

        function updateItem() {
            if (editingIndex === null) return;

            const formData = new FormData(document.getElementById('sewing-form'));
            
            if (!validateForm()) return;

            const item = {
                ...items[editingIndex],
                print_type: getSelectedPersonalizations(),
                art_name: formData.get('art_name') || '',
                quantity: parseInt(formData.get('quantity')),
                fabric: getOptionName('tecido', formData.get('tecido')),
                color: getOptionName('cor', formData.get('cor')),
                collar: getOptionName('gola', formData.get('gola')),
                model: getOptionName('tipo_corte', formData.get('tipo_corte')),
                detail: getOptionName('detalhe', formData.get('detalhe')),
                unit_price: parseFloat(formData.get('unit_price')),
                total_price: parseInt(formData.get('quantity')) * parseFloat(formData.get('unit_price')),
                sizes: getSizesData()
            };

            items[editingIndex] = item;
            updateItemsList();
            updateSummary();
            resetForm();
            cancelEdit();
        }

        function editItem(index) {
            const item = items[index];
            editingIndex = index;

            // Preencher formulário
            fillFormWithItem(item);

            // Atualizar interface
            document.getElementById('form-title').textContent = `Editar Item ${index + 1}`;
            document.getElementById('add-item-btn').classList.add('hidden');
            document.getElementById('update-item-btn').classList.remove('hidden');
            document.getElementById('cancel-edit-btn').classList.remove('hidden');
        }

        function deleteItem(index) {
            if (confirm('Tem certeza que deseja remover este item?')) {
                items.splice(index, 1);
                updateItemsList();
                updateSummary();
            }
        }

        function cancelEdit() {
            editingIndex = null;
            document.getElementById('form-title').textContent = 'Adicionar Novo Item';
            document.getElementById('add-item-btn').classList.remove('hidden');
            document.getElementById('update-item-btn').classList.add('hidden');
            document.getElementById('cancel-edit-btn').classList.add('hidden');
            resetForm();
        }

        function fillFormWithItem(item) {
            // Preencher personalizações
            document.querySelectorAll('input[name="personalizacao[]"]').forEach(checkbox => {
                checkbox.checked = item.print_type.includes(checkbox.nextElementSibling.textContent);
            });

            // Preencher selects
            setSelectValue('tecido', item.fabric);
            setSelectValue('cor', item.color);
            setSelectValue('tipo_corte', item.model);
            setSelectValue('detalhe', item.detail);
            setSelectValue('gola', item.collar);

            // Preencher tamanhos
            if (item.sizes) {
                Object.keys(item.sizes).forEach(size => {
                    const input = document.querySelector(`input[name="tamanhos[${size}]"]`);
                    if (input) input.value = item.sizes[size];
                });
            }

            // Atualizar quantidades e preços
            updateQuantity();
            updatePrices();
        }

        function setSelectValue(selectId, value) {
            const select = document.getElementById(selectId);
            if (select) {
                for (let option of select.options) {
                    if (option.textContent === value) {
                        select.value = option.value;
                        break;
                    }
                }
            }
        }

        function getSelectedPersonalizations() {
            const selected = [];
            document.querySelectorAll('input[name="personalizacao[]"]:checked').forEach(checkbox => {
                selected.push(checkbox.nextElementSibling.textContent);
            });
            return selected.join(', ');
        }

        function getOptionName(selectId, value) {
            const select = document.getElementById(selectId);
            if (select && value) {
                const option = select.querySelector(`option[value="${value}"]`);
                return option ? option.textContent : '';
            }
            return '';
        }

        function getSizesData() {
            const sizes = {};
            document.querySelectorAll('input[name^="tamanhos["]').forEach(input => {
                const size = input.name.match(/\[(.*?)\]/)[1];
                sizes[size] = parseInt(input.value) || 0;
            });
            return sizes;
        }

        function validateForm() {
            const requiredFields = ['tecido', 'cor', 'tipo_corte', 'gola'];
            for (let field of requiredFields) {
                if (!document.getElementById(field).value) {
                    alert(`Por favor, selecione ${field.replace('_', ' ')}.`);
                    return false;
                }
            }

            const quantity = parseInt(document.getElementById('quantity').value);
            if (quantity <= 0) {
                alert('Por favor, adicione pelo menos uma peça.');
                return false;
            }

            return true;
        }

        function resetForm() {
            document.getElementById('sewing-form').reset();
            document.querySelectorAll('input[name^="tamanhos["]').forEach(input => {
                input.value = '0';
            });
            updateQuantity();
            updatePrices();
        }

        function updateItemsList() {
            console.log('=== ATUALIZANDO LISTA DE ITENS ===');
            console.log('Items to display:', items);
            console.log('Items count:', items.length);
            
            const container = document.getElementById('items-list');
            container.innerHTML = '';

            items.forEach((item, index) => {
                console.log(`Processando item ${index}:`, item);
                const itemElement = document.createElement('div');
                itemElement.className = 'bg-gray-50 rounded-lg p-4 border-l-4 border-indigo-400';
                itemElement.innerHTML = `
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-semibold text-indigo-600">Item ${index + 1}</h3>
                        <div class="flex gap-2">
                            <button onclick="editItem(${index})" class="text-blue-600 hover:text-blue-800 text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <button onclick="deleteItem(${index})" class="text-red-600 hover:text-red-800 text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="text-sm text-gray-600 space-y-1">
                        <p><strong>Personalização:</strong> ${item.print_type || 'Não definido'}</p>
                        <p><strong>Tecido:</strong> ${item.fabric || 'Não definido'}</p>
                        <p><strong>Cor:</strong> ${item.color || 'Não definido'}</p>
                        <p><strong>Gola:</strong> ${item.collar || 'Não definido'}</p>
                        <p><strong>Modelo:</strong> ${item.model || 'Não definido'}</p>
                        <p><strong>Detalhe:</strong> ${item.detail || 'Não definido'}</p>
                        <p><strong>Quantidade:</strong> ${item.quantity || 0} peças</p>
                        <p><strong>Valor Unit.:</strong> R$ ${(item.unit_price || 0).toFixed(2).replace('.', ',')}</p>
                        <p><strong>Total:</strong> R$ ${(item.total_price || 0).toFixed(2).replace('.', ',')}</p>
                    </div>
                `;
                container.appendChild(itemElement);
            });
        }

        function updateSummary() {
            const totalItems = items.length;
            const totalPieces = items.reduce((sum, item) => sum + (item.quantity || 0), 0);
            const subtotal = items.reduce((sum, item) => sum + (item.total_price || 0), 0);

            document.getElementById('items-count').textContent = `${totalItems} item(s) adicionado(s)`;
            document.getElementById('total-items').textContent = totalItems;
            document.getElementById('total-pieces').textContent = totalPieces;
            document.getElementById('subtotal').textContent = `R$ ${subtotal.toFixed(2).replace('.', ',')}`;

            // Habilitar/desabilitar botão finalizar
            document.getElementById('finish-btn').disabled = totalItems === 0;
        }

        function finishStep() {
            if (items.length === 0) {
                alert('Adicione pelo menos um item antes de continuar.');
                return;
            }

            // Salvar itens na sessão via AJAX
            fetch('{{ route("orders.edit-wizard.sewing") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    action: 'update_items',
                    items: items
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirecionar para próxima etapa
                    window.location.href = '{{ route("orders.edit-wizard.customization") }}';
                } else {
                    alert('Erro ao salvar itens: ' + (data.message || 'Erro desconhecido'));
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao salvar itens. Tente novamente.');
            });
        }
    </script>
</body>
</html>