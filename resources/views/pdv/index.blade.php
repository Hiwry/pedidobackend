{{-- 
    PDV - Ponto de Venda
    Esta view é APENAS para vendas (PDV), NÃO deve conter:
    - Lista de estoque agrupado (@forelse($groupedStocks...))
    - Formulários de filtro de estoque
    - Tabelas de estoque
    - Qualquer código relacionado a gerenciamento de estoque
    
    O PDV apenas verifica estoque via API ao adicionar produtos ao carrinho.
--}}
@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">PDV - Ponto de Venda</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Coluna Esquerda: Produtos -->
        <div class="lg:col-span-2">
            <!-- Busca de Produtos -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-4 mb-6">
                <input type="text" 
                       id="product-search" 
                       placeholder="Buscar produtos..." 
                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Lista de Produtos -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-4">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Produtos</h2>
                <div id="products-grid" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @forelse($products as $product)
                    <div class="product-card border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-lg transition-shadow" 
                         data-product-id="{{ $product->id }}"
                         data-product-title="{{ strtolower($product->title) }}"
                         data-product-category="{{ strtolower($product->category?->name ?? '') }}"
                         data-type="product">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ $product->title }}</h3>
                                @if($product->category)
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $product->category->name }}</p>
                                @endif
                                @if($product->price)
                                <p class="text-lg font-bold text-indigo-600 dark:text-indigo-400 mt-1">
                                    R$ {{ number_format($product->price, 2, ',', '.') }}
                                    @if($product->sale_type && $product->sale_type != 'unidade')
                                        / {{ $product->sale_type == 'kg' ? 'Kg' : 'Metro' }}
                                    @endif
                                </p>
                                @endif
                                @if($product->allow_application)
                                <p class="text-xs text-green-600 dark:text-green-400 mt-1">
                                    ✓ Permite aplicação
                                </p>
                                @endif
                            </div>
                        </div>
                        <button onclick="openAddProductModal({{ $product->id }}, 'product')" 
                                class="w-full mt-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            Adicionar
                        </button>
                    </div>
                    @empty
                    @endforelse
                    
                    @forelse($productOptions as $option)
                    <div class="product-card border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-lg transition-shadow" 
                         data-product-option-id="{{ $option->id }}"
                         data-product-title="{{ strtolower($option->name) }}"
                         data-product-category="tipo de corte"
                         data-type="product_option">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ $option->name }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Tipo de Corte</p>
                                <p class="text-lg font-bold text-purple-600 dark:text-purple-400 mt-1">
                                    R$ {{ number_format($option->price, 2, ',', '.') }}
                                </p>
                            </div>
                        </div>
                        <button onclick="openAddProductModal({{ $option->id }}, 'product_option')" 
                                class="w-full mt-2 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                            Adicionar
                        </button>
                    </div>
                    @empty
                    @endforelse
                    
                    @if($products->isEmpty() && $productOptions->isEmpty())
                    <div class="col-span-2 text-center py-8 text-gray-500 dark:text-gray-400">
                        Nenhum produto encontrado
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Coluna Direita: Carrinho e Cliente -->
        <div class="lg:col-span-1">
            <!-- Seleção de Cliente -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-4 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Cliente <span class="text-sm font-normal text-gray-500 dark:text-gray-400">(opcional)</span></h2>
                
                <!-- Cliente Selecionado -->
                <div id="selected-client-display" class="hidden mb-3 p-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg border border-indigo-200 dark:border-indigo-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-gray-100" id="selected-client-name"></p>
                            <p class="text-sm text-gray-600 dark:text-gray-400" id="selected-client-info"></p>
                        </div>
                        <button type="button" onclick="clearSelectedClient()" 
                                class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Busca de Cliente -->
                <div class="space-y-3">
                    <div class="flex gap-2">
                        <div class="flex-1 relative">
                            <input type="text" id="search-client" placeholder="Digite nome, telefone ou CPF..." 
                                   class="w-full pl-4 pr-10 py-2 rounded-lg border-2 border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500 focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 dark:focus:ring-indigo-900/20 transition-all text-sm">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <button type="button" onclick="searchClient()" 
                                class="px-4 py-2 bg-indigo-600 dark:bg-indigo-500 text-white rounded-lg hover:bg-indigo-700 dark:hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-medium">
                            Buscar
                        </button>
                    </div>
                    <div id="search-results" class="space-y-2 max-h-48 overflow-y-auto"></div>
                </div>

                <input type="hidden" id="client_id" name="client_id" value="">
                
                <a href="{{ route('clients.create') }}" 
                   target="_blank"
                   class="mt-3 block text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                    + Novo Cliente
                </a>
            </div>

            <!-- Carrinho -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-4">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Carrinho</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Total de itens: <span id="cart-total-items" class="font-semibold text-indigo-600 dark:text-indigo-400">
                                @php
                                    $totalItems = 0;
                                    if (!empty($cart) && is_array($cart)) {
                                        foreach ($cart as $item) {
                                            $totalItems += $item['quantity'] ?? 0;
                                        }
                                    }
                                @endphp
                                {{ $totalItems }}
                            </span>
                        </p>
                    </div>
                    <button onclick="clearCart()" 
                            class="text-sm text-red-600 dark:text-red-400 hover:underline">
                        Limpar
                    </button>
                </div>

                <div id="cart-items" class="space-y-2 mb-4 max-h-96 overflow-y-auto">
                    @if(empty($cart))
                    <p class="text-gray-500 dark:text-gray-400 text-center py-4">Carrinho vazio</p>
                    @else
                    @foreach($cart as $item)
                    <div class="cart-item border border-gray-200 dark:border-gray-700 rounded-lg p-3" data-item-id="{{ $item['id'] }}">
                        <div class="flex justify-between items-start mb-2">
                    <div class="flex-1">
                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ $item['product_title'] }}</p>
                        @if(isset($item['sale_type']) && $item['sale_type'] != 'unidade')
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Venda por {{ $item['sale_type'] == 'kg' ? 'Kg' : 'Metro' }}
                        </p>
                        @endif
                        @if(isset($item['application_type']))
                        <p class="text-xs text-green-600 dark:text-green-400">
                            Aplicação: {{ $item['application_type'] == 'sublimacao_local' ? 'Sublimação Local' : 'DTF' }}
                        </p>
                        @endif
                        <div class="flex items-center gap-2 mt-1">
                            <input type="number" 
                                   value="{{ $item['quantity'] }}" 
                                   step="{{ isset($item['sale_type']) && $item['sale_type'] != 'unidade' ? '0.01' : '1' }}"
                                   min="{{ isset($item['sale_type']) && $item['sale_type'] != 'unidade' ? '0.01' : '1' }}"
                                   onchange="updateCartItem('{{ $item['id'] }}', this.value, null)"
                                   class="w-16 px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                            <span class="text-sm text-gray-500 dark:text-gray-400">x</span>
                            <input type="number" 
                                   step="0.01"
                                   value="{{ number_format($item['unit_price'], 2, '.', '') }}" 
                                   min="0"
                                   onchange="updateCartItem('{{ $item['id'] }}', null, this.value)"
                                   class="w-20 px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        </div>
                    </div>
                            <button onclick="removeCartItem('{{ $item['id'] }}')" 
                                    class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                            Total: R$ {{ number_format($item['total_price'], 2, ',', '.') }}
                        </p>
                    </div>
                    @endforeach
                    @endif
                </div>

                <!-- Totais -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
                        <span class="font-semibold text-gray-900 dark:text-gray-100" id="cart-subtotal">
                            @php
                                $subtotal = 0;
                                if (!empty($cart) && is_array($cart)) {
                                    $subtotal = array_sum(array_column($cart, 'total_price'));
                                }
                            @endphp
                            R$ {{ number_format($subtotal, 2, ',', '.') }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <label class="text-sm text-gray-600 dark:text-gray-400">Desconto:</label>
                        <input type="number" 
                               id="discount-input" 
                               step="0.01"
                               min="0"
                               value="0"
                               class="w-24 px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    </div>
                    <div class="flex justify-between items-center">
                        <label class="text-sm text-gray-600 dark:text-gray-400">Taxa de Entrega:</label>
                        <input type="number" 
                               id="delivery-fee-input" 
                               step="0.01"
                               min="0"
                               value="0"
                               class="w-24 px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    </div>
                    <div class="flex justify-between text-lg font-bold border-t border-gray-200 dark:border-gray-700 pt-2">
                        <span class="text-gray-900 dark:text-gray-100">Total:</span>
                        <span class="text-indigo-600 dark:text-indigo-400" id="cart-total">
                            R$ {{ number_format($subtotal, 2, ',', '.') }}
                        </span>
                    </div>
                </div>

                <!-- Observações -->
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Observações:</label>
                    <textarea id="notes-input" 
                              rows="2"
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>

                <!-- Botões Finalizar -->
                <div class="mt-4 space-y-2">
                    <button onclick="checkout()" 
                            id="checkout-btn"
                            class="w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-semibold disabled:bg-gray-400 disabled:cursor-not-allowed"
                            disabled>
                        Finalizar Venda
                    </button>
                    <button onclick="checkoutWithoutClient()" 
                            id="checkout-without-client-btn"
                            class="w-full px-4 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors font-semibold">
                        Finalizar Sem Cliente
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Adicionar Produto -->
<div id="add-product-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 max-w-5xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Adicionar ao Carrinho</h3>
            <button onclick="closeAddProductModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div id="product-modal-content">
            <!-- Conteúdo será preenchido via JavaScript -->
        </div>
    </div>
</div>

<!-- Modal de Confirmação para Limpar Carrinho -->
<div id="clear-cart-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center" onclick="if(event.target === this) closeClearCartModal()">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 max-w-md w-full mx-4" onclick="event.stopPropagation()">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Limpar Carrinho</h3>
            <button onclick="closeClearCartModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div class="mb-6">
            <p class="text-gray-700 dark:text-gray-300">
                Deseja realmente limpar o carrinho? Esta ação não pode ser desfeita.
            </p>
        </div>
        
        <div class="flex gap-3 justify-end">
            <button onclick="closeClearCartModal()" 
                    class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                Cancelar
            </button>
            <button onclick="confirmClearCart()" 
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                Limpar Carrinho
            </button>
        </div>
    </div>
</div>

<!-- Modal de Personalização SUB.LOCAL -->
<div id="sublocal-modal" class="hidden fixed inset-0 bg-black/50 dark:bg-black/70 z-50 flex items-center justify-center p-4" onclick="if(event.target === this) closeSublocalModal()">
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto border border-gray-200 dark:border-slate-800" onclick="event.stopPropagation()">
        <!-- Modal Header -->
        <div class="px-6 py-5 border-b border-gray-200 dark:border-slate-800 flex items-center justify-between sticky top-0 bg-white dark:bg-slate-900 z-10">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Adicionar Personalização SUB.LOCAL</h3>
            <button type="button" onclick="closeSublocalModal()" class="text-gray-400 dark:text-slate-500 hover:text-gray-600 dark:hover:text-slate-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <!-- Modal Body -->
        <div class="p-6 space-y-5">
            <!-- Localização -->
            <div>
                <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Localização *</label>
                <select id="sublocal-modal-location" class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                    <option value="">Selecione...</option>
                    @foreach($locations as $location)
                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Tamanho -->
            <div>
                <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Tamanho *</label>
                <select id="sublocal-modal-size" class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                    <option value="">Selecione...</option>
                </select>
            </div>

            <!-- Quantidade -->
            <div>
                <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Quantidade *</label>
                <input type="number" id="sublocal-modal-quantity" min="1" value="1"
                       class="w-full px-4 py-2.5 border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-all">
                <p class="mt-1 text-xs text-gray-500 dark:text-slate-400">Quantidade de peças para esta aplicação</p>
            </div>

            <!-- Preço Calculado -->
            <div id="sublocal-modal-price-display" class="hidden">
                <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-semibold text-gray-700 dark:text-slate-300">Preço por Aplicação:</span>
                        <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400" id="sublocal-modal-unit-price">R$ 0,00</span>
                    </div>
                    <div class="flex items-center justify-between mt-1">
                        <span class="text-xs text-gray-600 dark:text-slate-400">Total desta Aplicação:</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white" id="sublocal-modal-total-price">R$ 0,00</span>
                    </div>
                </div>
            </div>
            <input type="hidden" id="sublocal-modal-unit-price-value" value="0">
            <input type="hidden" id="sublocal-modal-final-price-value" value="0">

            <!-- Botões -->
            <div class="flex gap-3 pt-4 border-t border-gray-200 dark:border-slate-800">
                <button type="button" onclick="closeSublocalModal()" 
                        class="flex-1 px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                    Cancelar
                </button>
                <button type="button" onclick="confirmSublocalPersonalization()" 
                        class="flex-1 px-4 py-2 bg-indigo-600 dark:bg-indigo-500 text-white rounded-lg hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-colors font-semibold">
                    Adicionar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Formas de Pagamento -->
<div id="payment-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Formas de Pagamento</h3>
            <button onclick="closePaymentModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div class="mb-4">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                Total da Venda: <span class="font-semibold text-lg text-gray-900 dark:text-gray-100" id="payment-total">R$ 0,00</span>
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400">
                Adicione uma ou mais formas de pagamento para finalizar a venda
            </p>
        </div>
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Adicionar Forma de Pagamento:</label>
            <div class="flex gap-2">
                <select id="new-payment-method" class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">Selecione...</option>
                    <option value="dinheiro">Dinheiro</option>
                    <option value="pix">PIX</option>
                    <option value="cartao">Cartão</option>
                    <option value="transferencia">Transferência</option>
                    <option value="boleto">Boleto</option>
                </select>
                <input type="number" 
                       id="new-payment-amount" 
                       step="0.01"
                       min="0.01"
                       placeholder="Valor"
                       class="w-32 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                <button type="button" onclick="addPaymentMethod()" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Adicionar
                </button>
            </div>
        </div>
        
        <div id="payment-methods-list" class="space-y-2 mb-4">
            <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Nenhuma forma de pagamento adicionada</p>
        </div>
        
        <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mb-4">
            <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Total Pago:</span>
                <span class="text-lg font-semibold text-green-600 dark:text-green-400" id="payment-total-paid">R$ 0,00</span>
            </div>
            <div class="flex justify-between items-center mt-2">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Restante:</span>
                <span class="text-lg font-semibold" id="payment-remaining">R$ 0,00</span>
            </div>
        </div>
        
        <div class="flex gap-3">
            <button onclick="closePaymentModal()" 
                    class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                Cancelar
            </button>
            <button onclick="confirmPayment()" 
                    id="confirm-payment-btn"
                    class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-semibold disabled:bg-gray-400 disabled:cursor-not-allowed"
                    disabled>
                Finalizar Venda
            </button>
        </div>
    </div>
</div>

@php
$productsData = $products->map(function($p) {
    return [
        'id' => $p->id,
        'type' => 'product',
        'title' => $p->title,
        'price' => $p->price ?? 0,
        'sale_type' => $p->sale_type ?? 'unidade',
        'allow_application' => (bool)($p->allow_application ?? false),
        'application_types' => $p->application_types ?? [],
        'category_id' => $p->category_id ?? null // Para identificar produtos do quick-products (sem categoria)
    ];
})->values();

$productOptionsData = $productOptions->map(function($o) use ($productOptionsWithSublocal) {
    $sublocalInfo = $productOptionsWithSublocal->get($o->id);
    
    return [
        'id' => $o->id,
        'type' => 'product_option',
        'title' => $o->name,
        'price' => $o->price ?? 0,
        'sale_type' => 'unidade',
        'allow_application' => false,
        'application_types' => [],
        'allows_sublocal' => $sublocalInfo ? ($sublocalInfo['allows_sublocal'] ?? false) : false,
        'fabric_id' => $sublocalInfo ? ($sublocalInfo['fabric_id'] ?? null) : null
    ];
})->values();
@endphp

<script>
// CRÍTICO: Definir funções no window IMEDIATAMENTE, antes de qualquer outro código
// Isso garante que as funções estejam disponíveis quando a página é carregada via AJAX
// e os elementos com onclick/onchange tentam chamá-las

// Função stub para openAddProductModal
window.openAddProductModal = window.openAddProductModal || function() {
    console.warn('openAddProductModal ainda não foi totalmente inicializada. Aguarde...');
    return false;
};

// Função stub para checkStockForSizes
window.checkStockForSizes = window.checkStockForSizes || async function() {
    // Função stub silenciosa - apenas retorna sem fazer nada
    return;
};

// Função stub para updateTotalQuantity
window.updateTotalQuantity = window.updateTotalQuantity || function() {
    // Função stub silenciosa
    return;
};

// Função stub para calculateSizeSurcharges
window.calculateSizeSurcharges = window.calculateSizeSurcharges || function() {
    // Função stub silenciosa
    return;
};

// Função stub para confirmAddProduct
window.confirmAddProduct = window.confirmAddProduct || async function() {
    console.warn('confirmAddProduct ainda não foi totalmente inicializada. Aguarde...');
    return false;
};

// Função stub para closeAddProductModal
window.closeAddProductModal = window.closeAddProductModal || function() {
    // Função stub silenciosa
    return;
};

// Função stub para clearSelectedClient
window.clearSelectedClient = window.clearSelectedClient || function() {
    // Função stub silenciosa
    return;
};

// Função stub para searchClient
window.searchClient = window.searchClient || function() {
    // Função stub silenciosa
    return;
};

// Função stub para clearCart
window.clearCart = window.clearCart || function() {
    // Função stub silenciosa
    return;
};

// Função stub para removeCartItem
window.removeCartItem = window.removeCartItem || async function() {
    // Função stub silenciosa
    return;
};

// Função stub para checkout
window.checkout = window.checkout || async function() {
    // Função stub silenciosa
    return;
};

// Função stub para checkoutWithoutClient
window.checkoutWithoutClient = window.checkoutWithoutClient || async function() {
    // Função stub silenciosa
    return;
};

// Função stub para closeClearCartModal
window.closeClearCartModal = window.closeClearCartModal || function() {
    // Função stub silenciosa
    return;
};

// Função stub para confirmClearCart
window.confirmClearCart = window.confirmClearCart || function() {
    // Função stub silenciosa
    return;
};

// Função stub para closeSublocalModal
window.closeSublocalModal = window.closeSublocalModal || function() {
    // Função stub silenciosa
    return;
};

// Função stub para confirmSublocalPersonalization
window.confirmSublocalPersonalization = window.confirmSublocalPersonalization || function() {
    // Função stub silenciosa
    return;
};

(function() {
    // Evitar redeclaração ao carregar via AJAX
    if (typeof window.productsData !== 'undefined') {
        return; // Já foi declarado, não redeclarar
    }
    
    // Dados dos produtos
    window.productsData = @json($productsData);
    window.productOptionsData = @json($productOptionsData);
    window.allItemsData = [...window.productsData, ...window.productOptionsData];
    window.locationsData = @json($locations);
    window.fabricsData = @json($fabrics);
    window.colorsData = @json($colors);
    window.currentStoreId = {{ $currentStoreId ?? 'null' }};
    window.sizesList = ['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3'];
})();

// Aliases para compatibilidade (usar window.* para evitar redeclaração)
const currentStoreId = window.currentStoreId;
const sizesList = window.sizesList;

let currentProductId = null;
let currentProductType = 'product';

// Abrir modal de adicionar produto
// IMPORTANTE: Definir no window imediatamente para estar disponível quando carregado via AJAX
window.openAddProductModal = function openAddProductModal(itemId, type = 'product') {
    currentProductId = itemId;
    currentProductType = type;
    
    const product = type === 'product' 
        ? window.productsData.find(p => p.id === itemId)
        : window.productOptionsData.find(p => p.id === itemId);
    
    if (!product) return;
    
    const modal = document.getElementById('add-product-modal');
    const content = document.getElementById('product-modal-content');
    
    // Determinar step e label baseado no tipo de venda
    let quantityLabel = 'Quantidade';
    let quantityStep = '1';
    let quantityMin = '1';
    
    if (product.sale_type === 'kg' || product.sale_type === 'metro') {
        quantityLabel = `Quantidade (${product.sale_type === 'kg' ? 'Kg' : 'Metros'})`;
        quantityStep = '0.01';
        quantityMin = '0.01';
    }
    
    // Verificar se é tecido (kg ou metro) ou produto do quick-products (sem categoria)
    const isFabric = product.sale_type === 'kg' || product.sale_type === 'metro';
    const isQuickProduct = type === 'product' && !product.category_id; // Produtos do quick-products não têm categoria
    const isProductOption = type === 'product_option'; // Product_options sempre mostram tamanhos
    
    // Mostrar tamanhos para: produtos normais (com categoria) que não são tecidos, e product_options
    // NÃO mostrar para: tecidos e produtos do quick-products
    const shouldShowSizes = !isFabric && !isQuickProduct;
    
    // Mostrar campos de estoque apenas para product_options (tipo de corte)
    const shouldShowStockFields = isProductOption;
    
    let applicationHtml = '';
    if (product.allow_application && product.application_types && product.application_types.length > 0) {
        applicationHtml = `
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tipo de Aplicação:</label>
                <select id="modal-application-type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">Sem aplicação</option>
                    ${product.application_types.includes('sublimacao_local') ? '<option value="sublimacao_local">Sublimação Local</option>' : ''}
                    ${product.application_types.includes('dtf') ? '<option value="dtf">DTF</option>' : ''}
                </select>
            </div>
        `;
    }
    
    // Verificar se permite sub.local (apenas para product_option)
    const allowsSublocal = isProductOption && product.allows_sublocal;
    
    // HTML para sub.local personalizations
    let sublocalHtml = '';
    if (allowsSublocal) {
        sublocalHtml = `
            <div class="mb-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                <div class="flex justify-between items-center mb-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Personalizações SUB.LOCAL:</label>
                    <button type="button" onclick="openSublocalModal()" class="px-3 py-1 text-xs bg-indigo-600 text-white rounded hover:bg-indigo-700">
                        + Adicionar
                    </button>
                </div>
                <div id="sublocal-personalizations-list" class="space-y-3">
                    <!-- Personalizações serão adicionadas aqui -->
                </div>
            </div>
        `;
    }
    
    // HTML para tamanhos
    // Removido: seção separada de "Adicionais de Tamanho"
    // Os acréscimos agora são calculados automaticamente quando GG, EXG, G1, G2, G3 são selecionados
    // na seção principal de tamanhos (para product_options)
    let sizesHtml = '';
    
    content.innerHTML = `
        <div>
            <!-- Header do Produto -->
            <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h4 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">${product.title}</h4>
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-xl font-bold text-indigo-600 dark:text-indigo-400">
                                    R$ ${parseFloat(product.price || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                                </span>
                                ${product.sale_type !== 'unidade' ? `<span class="text-sm text-gray-500 dark:text-gray-400">/ ${product.sale_type === 'kg' ? 'Kg' : 'Metro'}</span>` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            ${!shouldShowStockFields ? `
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">${quantityLabel}:</label>
                <input type="number" 
                       id="modal-quantity" 
                       step="${quantityStep}"
                       min="${quantityMin}"
                       value="${quantityMin}"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
            </div>
            ` : ''}
            
            ${!shouldShowStockFields ? `
            <!-- Card: Preço Unitário (apenas para produtos normais) -->
            <div class="mb-6 bg-gradient-to-r from-indigo-50 to-blue-50 dark:from-indigo-900/20 dark:to-blue-900/20 rounded-xl p-4 border border-indigo-200 dark:border-indigo-800">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Preço Unitário <span class="text-red-500">*</span>
                </label>
                <input type="number" 
                       id="modal-unit-price" 
                       required
                       step="0.01"
                       min="0.01"
                       value="${product.price || 0}"
                       class="w-full px-4 py-3 border-2 border-indigo-300 dark:border-indigo-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 font-semibold text-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            ` : `
            <!-- Preço fixo do tipo de corte (não editável) -->
            <input type="hidden" id="modal-unit-price" value="${product.price || 0}">
            `}
            
            ${applicationHtml}
            
            ${shouldShowStockFields ? `
            <!-- Card: Seleção de Cor -->
            <div class="mb-6 bg-gradient-to-r from-indigo-50 to-blue-50 dark:from-indigo-900/20 dark:to-blue-900/20 rounded-xl p-5 border border-indigo-200 dark:border-indigo-800 shadow-sm">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                    </svg>
                    <h5 class="text-base font-bold text-gray-900 dark:text-gray-100">Selecionar Cor</h5>
                    <span class="text-red-500 text-sm font-semibold">*</span>
                </div>
                
                <select id="modal-color-select" required
                        class="w-full px-4 py-3 border-2 border-indigo-300 dark:border-indigo-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-medium shadow-sm transition-all">
                    <option value="">Selecione a cor...</option>
                    ${window.colorsData.map(color => `
                        <option value="${color.id}">${color.name}</option>
                    `).join('')}
                </select>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Selecione a cor para visualizar o estoque disponível
                </p>
            </div>
            
            <!-- Card: Tamanhos e Quantidades -->
            <div class="mb-6 bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <h5 class="text-sm font-bold text-gray-900 dark:text-gray-100">Tamanhos e Quantidades</h5>
                    <div class="flex items-center gap-3">
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            Total: <span id="total-quantity-display" class="font-bold text-indigo-600 dark:text-indigo-400">0</span>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            Acréscimo: <span id="total-surcharges-modal" class="font-bold text-green-600 dark:text-green-400">R$ 0,00</span>
                        </div>
                    </div>
                </div>
                
                <!-- Tamanhos Principais (PP a GG) -->
                <div class="grid grid-cols-5 gap-2 mb-2">
                    <div class="relative">
                        <label class="block text-[10px] font-bold text-gray-600 dark:text-gray-400 mb-1 text-center">PP</label>
                        <div class="relative">
                            <input type="number" id="modal-size-pp" min="0" value="0" 
                                   onchange="checkStockForSizes(); updateTotalQuantity();" 
                                   class="w-full px-2 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-center text-sm font-semibold bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                            <span id="stock-badge-pp" class="absolute -top-1 -right-1 text-[9px] font-bold px-1.5 py-0.5 rounded-full"></span>
                        </div>
                        <div id="stock-pp" class="hidden"></div>
                    </div>
                    <div class="relative">
                        <label class="block text-[10px] font-bold text-gray-600 dark:text-gray-400 mb-1 text-center">P</label>
                        <div class="relative">
                            <input type="number" id="modal-size-p" min="0" value="0" 
                                   onchange="checkStockForSizes(); updateTotalQuantity();" 
                                   class="w-full px-2 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-center text-sm font-semibold bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                            <span id="stock-badge-p" class="absolute -top-1 -right-1 text-[9px] font-bold px-1.5 py-0.5 rounded-full"></span>
                        </div>
                        <div id="stock-p" class="hidden"></div>
                    </div>
                    <div class="relative">
                        <label class="block text-[10px] font-bold text-gray-600 dark:text-gray-400 mb-1 text-center">M</label>
                        <div class="relative">
                            <input type="number" id="modal-size-m" min="0" value="0" 
                                   onchange="checkStockForSizes(); updateTotalQuantity();" 
                                   class="w-full px-2 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-center text-sm font-semibold bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                            <span id="stock-badge-m" class="absolute -top-1 -right-1 text-[9px] font-bold px-1.5 py-0.5 rounded-full"></span>
                        </div>
                        <div id="stock-m" class="hidden"></div>
                    </div>
                    <div class="relative">
                        <label class="block text-[10px] font-bold text-gray-600 dark:text-gray-400 mb-1 text-center">G</label>
                        <div class="relative">
                            <input type="number" id="modal-size-g" min="0" value="0" 
                                   onchange="checkStockForSizes(); updateTotalQuantity();" 
                                   class="w-full px-2 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-center text-sm font-semibold bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                            <span id="stock-badge-g" class="absolute -top-1 -right-1 text-[9px] font-bold px-1.5 py-0.5 rounded-full"></span>
                        </div>
                        <div id="stock-g" class="hidden"></div>
                    </div>
                    <div class="relative">
                        <label class="block text-[10px] font-bold text-gray-600 dark:text-gray-400 mb-1 text-center">GG</label>
                        <div class="relative">
                            <input type="number" id="modal-size-gg" min="0" value="0" 
                                   onchange="checkStockForSizes(); calculateSizeSurcharges(); updateTotalQuantity();" 
                                   class="w-full px-2 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-center text-sm font-semibold bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                            <span id="stock-badge-gg" class="absolute -top-1 -right-1 text-[9px] font-bold px-1.5 py-0.5 rounded-full"></span>
                        </div>
                        <p class="text-[10px] font-semibold text-green-600 dark:text-green-400 mt-0.5 text-center" id="surcharge-gg">+ R$ 0,00</p>
                        <div id="stock-gg" class="hidden"></div>
                    </div>
                </div>
                
                <!-- Tamanhos Extras (EXG a G3) -->
                <div class="grid grid-cols-4 gap-2">
                    <div class="relative">
                        <label class="block text-[10px] font-bold text-gray-600 dark:text-gray-400 mb-1 text-center">EXG</label>
                        <div class="relative">
                            <input type="number" id="modal-size-exg" min="0" value="0" 
                                   onchange="checkStockForSizes(); calculateSizeSurcharges(); updateTotalQuantity();" 
                                   class="w-full px-2 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-center text-sm font-semibold bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                            <span id="stock-badge-exg" class="absolute -top-1 -right-1 text-[9px] font-bold px-1.5 py-0.5 rounded-full"></span>
                        </div>
                        <p class="text-[10px] font-semibold text-green-600 dark:text-green-400 mt-0.5 text-center" id="surcharge-exg">+ R$ 0,00</p>
                        <div id="stock-exg" class="hidden"></div>
                    </div>
                    <div class="relative">
                        <label class="block text-[10px] font-bold text-gray-600 dark:text-gray-400 mb-1 text-center">G1</label>
                        <div class="relative">
                            <input type="number" id="modal-size-g1" min="0" value="0" 
                                   onchange="checkStockForSizes(); calculateSizeSurcharges(); updateTotalQuantity();" 
                                   class="w-full px-2 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-center text-sm font-semibold bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                            <span id="stock-badge-g1" class="absolute -top-1 -right-1 text-[9px] font-bold px-1.5 py-0.5 rounded-full"></span>
                        </div>
                        <p class="text-[10px] font-semibold text-green-600 dark:text-green-400 mt-0.5 text-center" id="surcharge-g1">+ R$ 0,00</p>
                        <div id="stock-g1" class="hidden"></div>
                    </div>
                    <div class="relative">
                        <label class="block text-[10px] font-bold text-gray-600 dark:text-gray-400 mb-1 text-center">G2</label>
                        <div class="relative">
                            <input type="number" id="modal-size-g2" min="0" value="0" 
                                   onchange="checkStockForSizes(); calculateSizeSurcharges(); updateTotalQuantity();" 
                                   class="w-full px-2 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-center text-sm font-semibold bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                            <span id="stock-badge-g2" class="absolute -top-1 -right-1 text-[9px] font-bold px-1.5 py-0.5 rounded-full"></span>
                        </div>
                        <p class="text-[10px] font-semibold text-green-600 dark:text-green-400 mt-0.5 text-center" id="surcharge-g2">+ R$ 0,00</p>
                        <div id="stock-g2" class="hidden"></div>
                    </div>
                    <div class="relative">
                        <label class="block text-[10px] font-bold text-gray-600 dark:text-gray-400 mb-1 text-center">G3</label>
                        <div class="relative">
                            <input type="number" id="modal-size-g3" min="0" value="0" 
                                   onchange="checkStockForSizes(); calculateSizeSurcharges(); updateTotalQuantity();" 
                                   class="w-full px-2 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-center text-sm font-semibold bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                            <span id="stock-badge-g3" class="absolute -top-1 -right-1 text-[9px] font-bold px-1.5 py-0.5 rounded-full"></span>
                        </div>
                        <p class="text-[10px] font-semibold text-green-600 dark:text-green-400 mt-0.5 text-center" id="surcharge-g3">+ R$ 0,00</p>
                        <div id="stock-g3" class="hidden"></div>
                    </div>
                </div>
            </div>
            
            <!-- Card: Resumo de Estoque (Compacto) -->
            <div class="mb-4">
                <button type="button" onclick="toggleStockDetails()" 
                        class="w-full flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-all">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <span class="text-sm font-bold text-gray-900 dark:text-gray-100">Ver Estoque Disponível</span>
                    </div>
                    <svg id="stock-toggle-icon" class="w-4 h-4 text-gray-600 dark:text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                
                <div id="stock-details-panel" class="hidden mt-2 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                    <div id="stock-by-size-list" class="space-y-2">
                        <div class="text-xs text-gray-600 dark:text-gray-400 text-center py-3">
                            <p class="font-medium">Selecione a cor acima para ver o estoque</p>
                        </div>
                    </div>
                </div>
                
                <input type="hidden" id="modal-cut-type-id" value="${product.id}">
                <input type="hidden" id="modal-fabric-id" value="${product.fabric_id || ''}">
            </div>
            
            <script>
            function toggleStockDetails() {
                const panel = document.getElementById('stock-details-panel');
                const icon = document.getElementById('stock-toggle-icon');
                if (panel.classList.contains('hidden')) {
                    panel.classList.remove('hidden');
                    icon.style.transform = 'rotate(180deg)';
                } else {
                    panel.classList.add('hidden');
                    icon.style.transform = 'rotate(0deg)';
                }
            }
            </script>
            ` : ''}
            
            ${sizesHtml}
            
            ${sublocalHtml}
            
            <div class="flex gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <button onclick="closeAddProductModal()" 
                        class="flex-1 px-6 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-semibold transition-all hover:scale-[1.02]">
                    Cancelar
                </button>
                <button onclick="confirmAddProduct()" 
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-lg hover:from-indigo-700 hover:to-indigo-800 font-bold shadow-lg shadow-indigo-500/50 transition-all hover:scale-[1.02] flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Adicionar ao Carrinho
                </button>
            </div>
        </div>
    `;
    
    modal.classList.remove('hidden');
    
    // Calcular acréscimos quando o modal abrir
    setTimeout(() => {
        calculateSizeSurcharges();
    }, 100);
    
    // Recalcular quando preço ou quantidade mudar (apenas se o campo existir e não for product_option)
    const unitPriceInput = document.getElementById('modal-unit-price');
    if (unitPriceInput && !shouldShowStockFields) {
        // Apenas para produtos normais, não para product_options (que usam preço fixo)
        unitPriceInput.addEventListener('input', calculateSizeSurcharges);
    }
    document.getElementById('modal-quantity')?.addEventListener('input', function() {
        calculateSizeSurcharges();
        if (shouldShowStockFields) {
            checkStockAvailability();
        }
    });
    
    // Buscar estoque automaticamente quando for tipo de corte
    if (shouldShowStockFields) {
        // Adicionar listener para cor
        setTimeout(() => {
            const colorSelect = document.getElementById('modal-color-select');
            if (colorSelect) {
                colorSelect.addEventListener('change', function() {
                    loadStockByCutType(product.id);
                    checkStockForSizes();
                });
            }
        }, 300);
    }
}

// Atualizar total de quantidade
window.updateTotalQuantity = function updateTotalQuantity() {
    const sizes = ['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3'];
    let total = 0;
    
    sizes.forEach(size => {
        const input = document.getElementById(`modal-size-${size.toLowerCase()}`);
        if (input) {
            total += parseInt(input.value || 0);
        }
    });
    
    const totalQuantityDisplay = document.getElementById('total-quantity-display');
    if (totalQuantityDisplay) {
        totalQuantityDisplay.textContent = total;
        totalQuantityDisplay.className = total > 0 ? 'font-semibold text-indigo-600 dark:text-indigo-400' : 'font-semibold text-gray-400';
    }
    
    const totalItemsDisplay = document.getElementById('total-items-display');
    if (totalItemsDisplay) {
        totalItemsDisplay.textContent = total;
    }
}

// Encontrar loja prioritária que tem todos os tamanhos selecionados
function findPriorityStore(selectedSizes, stockBySizeData) {
    if (!stockBySizeData || !stockBySizeData.stock_by_size) {
        return null;
    }
    
    // Mapear lojas e verificar quais têm todos os tamanhos selecionados
    const storeScores = {};
    
    selectedSizes.forEach(size => {
        const sizeData = stockBySizeData.stock_by_size.find(s => s.size === size);
        if (sizeData && sizeData.stores) {
            sizeData.stores.forEach(store => {
                if (!storeScores[store.store_id]) {
                    storeScores[store.store_id] = {
                        store_id: store.store_id,
                        store_name: store.store_name,
                        hasAllSizes: true,
                        totalAvailable: 0,
                        sizesCount: 0
                    };
                }
                storeScores[store.store_id].totalAvailable += store.available || 0;
                storeScores[store.store_id].sizesCount++;
            });
        }
    });
    
    // Verificar quais lojas têm todos os tamanhos
    const selectedSizesCount = selectedSizes.length;
    let priorityStore = null;
    let maxTotalAvailable = 0;
    
    Object.values(storeScores).forEach(store => {
        if (store.sizesCount === selectedSizesCount && store.totalAvailable > maxTotalAvailable) {
            priorityStore = store;
            maxTotalAvailable = store.totalAvailable;
        }
    });
    
    return priorityStore;
}

// Verificar estoque para cada tamanho com informações por loja
window.checkStockForSizes = async function checkStockForSizes() {
    const colorSelect = document.getElementById('modal-color-select');
    const cutTypeId = document.getElementById('modal-cut-type-id')?.value;
    const fabricId = document.getElementById('modal-fabric-id')?.value;
    
    if (!colorSelect || !cutTypeId || !fabricId) {
        return;
    }
    
    const colorId = colorSelect.value;
    if (!colorId) {
        // Limpar informações de estoque se cor não estiver selecionada
        sizesList.forEach(size => {
            const stockDiv = document.getElementById(`stock-${size.toLowerCase()}`);
            const stockBadge = document.getElementById(`stock-badge-${size.toLowerCase()}`);
            if (stockDiv) {
                stockDiv.innerHTML = '';
                stockDiv.className = 'text-xs text-gray-500 dark:text-gray-400 mt-1 text-center';
            }
            if (stockBadge) {
                stockBadge.innerHTML = '';
                stockBadge.className = '';
            }
        });
        return;
    }
    
    const sizes = ['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3'];
    
    // Coletar tamanhos selecionados
    const selectedSizes = [];
    sizes.forEach(size => {
        const input = document.getElementById(`modal-size-${size.toLowerCase()}`);
        if (input && parseInt(input.value || 0) > 0) {
            selectedSizes.push(size);
        }
    });
    
    // Buscar estoque de todos os tamanhos de uma vez
    let stockBySizeData = null;
    try {
        const stockBySizeParams = new URLSearchParams({
            cut_type_id: cutTypeId,
            color_id: colorId
        });
        
        const stockBySizeResponse = await fetch(`/api/stocks/by-cut-type?${stockBySizeParams}`);
        stockBySizeData = await stockBySizeResponse.json();
    } catch (error) {
        console.error('Erro ao buscar estoque:', error);
    }
    
    // Encontrar loja prioritária
    const priorityStore = selectedSizes.length > 0 && stockBySizeData ? 
        findPriorityStore(selectedSizes, stockBySizeData) : null;
    
    // Processar cada tamanho
    for (const size of sizes) {
        const input = document.getElementById(`modal-size-${size.toLowerCase()}`);
        const stockDiv = document.getElementById(`stock-${size.toLowerCase()}`);
        const stockBadge = document.getElementById(`stock-badge-${size.toLowerCase()}`);
        
        if (!input || !stockDiv || !stockBadge) continue;
        
        const quantity = parseInt(input.value || 0);
        
        // Atualizar badge com estoque total
        if (stockBySizeData && stockBySizeData.success && stockBySizeData.stock_by_size) {
            const sizeData = stockBySizeData.stock_by_size.find(s => s.size === size);
            
            if (sizeData) {
                const totalAvailable = sizeData.available || 0;
                
                if (totalAvailable > 0) {
                    stockBadge.innerHTML = `<span class="bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-300 px-2 py-0.5 rounded text-xs font-bold">${totalAvailable}</span>`;
                    stockBadge.className = 'text-xs font-semibold px-2 py-0.5 rounded';
                } else {
                    stockBadge.innerHTML = `<span class="bg-red-100 dark:bg-red-800 text-red-700 dark:text-red-300 px-2 py-0.5 rounded text-xs font-bold">0</span>`;
                    stockBadge.className = 'text-xs font-semibold px-2 py-0.5 rounded';
                }
            } else {
                stockBadge.innerHTML = `<span class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 px-2 py-0.5 rounded text-xs font-bold">-</span>`;
                stockBadge.className = 'text-xs font-semibold px-2 py-0.5 rounded';
            }
        } else {
            stockBadge.innerHTML = '';
            stockBadge.className = '';
        }
        
        if (quantity > 0 && stockBySizeData && stockBySizeData.success && stockBySizeData.stock_by_size) {
            const sizeData = stockBySizeData.stock_by_size.find(s => s.size === size);
            
            if (sizeData) {
                const totalAvailable = sizeData.available || 0;
                const hasEnoughStock = totalAvailable >= quantity;
                const stores = sizeData.stores || [];
                
                // Encontrar loja prioritária para este tamanho (ou usar a loja que tem todos os tamanhos)
                let bestStore = null;
                if (priorityStore) {
                    bestStore = stores.find(s => s.store_id === priorityStore.store_id);
                }
                if (!bestStore && stores.length > 0) {
                    // Se não encontrou a loja prioritária, usar a loja com mais estoque
                    bestStore = stores.reduce((prev, current) => 
                        (current.available > (prev?.available || 0)) ? current : prev
                    );
                }
                
                // Construir HTML com informações por loja
                let stockHtml = '';
                
                if (hasEnoughStock) {
                    stockHtml = `<div class="space-y-2">
                        ${bestStore ? `
                            <div class="p-2 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg mb-2">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    <span class="text-xs font-bold text-blue-700 dark:text-blue-300">Loja: ${bestStore.store_name}</span>
                                </div>
                            </div>
                        ` : ''}
                        <div class="flex items-center justify-between p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-sm font-bold text-green-700 dark:text-green-300">${totalAvailable} disponível</span>
                            </div>
                            <span class="text-xs font-semibold text-green-600 dark:text-green-400 bg-green-200 dark:bg-green-800 px-2 py-1 rounded">Suficiente</span>
                        </div>`;
                    
                    if (stores.length > 0) {
                        stockHtml += `<div class="space-y-1.5 pt-2 border-t border-green-200 dark:border-green-700">`;
                        stockHtml += `<p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Por Loja:</p>`;
                        stores.forEach(store => {
                            if (store.available > 0) {
                                const isPriority = priorityStore && store.store_id === priorityStore.store_id;
                                const storeBg = isPriority ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800' : 'bg-white dark:bg-gray-800 border-green-100 dark:border-green-800';
                                stockHtml += `<div class="flex items-center justify-between text-xs ${storeBg} p-1.5 rounded border">
                                    <span class="font-medium text-gray-700 dark:text-gray-300">${store.store_name}${isPriority ? ' ⭐' : ''}</span> 
                                    <span class="font-bold text-green-600 dark:text-green-400">${store.available} un.</span>
                                </div>`;
                            }
                        });
                        stockHtml += `</div>`;
                    }
                    stockHtml += `</div>`;
                    
                    stockDiv.innerHTML = stockHtml;
                    stockDiv.className = 'text-xs transition-all';
                } else {
                    stockHtml = `<div class="space-y-2">
                        ${bestStore ? `
                            <div class="p-2 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg mb-2">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    <span class="text-xs font-bold text-blue-700 dark:text-blue-300">Loja: ${bestStore.store_name}</span>
                                </div>
                            </div>
                        ` : ''}
                        <div class="flex items-center justify-between p-2 bg-red-100 dark:bg-red-900/30 rounded-lg">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-sm font-bold text-red-700 dark:text-red-300">${totalAvailable} disponível</span>
                            </div>
                            <span class="text-xs font-semibold text-red-600 dark:text-red-400 bg-red-200 dark:bg-red-800 px-2 py-1 rounded">Insuficiente</span>
                        </div>`;
                    
                    if (stores.length > 0) {
                        stockHtml += `<div class="space-y-1.5 pt-2 border-t border-red-200 dark:border-red-700">`;
                        stockHtml += `<p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Por Loja:</p>`;
                        stores.forEach(store => {
                            const storeClass = store.available > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400';
                            const isPriority = priorityStore && store.store_id === priorityStore.store_id;
                            const storeBg = isPriority ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800' : 
                                (store.available > 0 ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800' : 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800');
                            stockHtml += `<div class="flex items-center justify-between text-xs ${storeBg} p-1.5 rounded border">
                                <span class="font-medium text-gray-700 dark:text-gray-300">${store.store_name}${isPriority ? ' ⭐' : ''}</span> 
                                <span class="font-bold ${storeClass}">${store.available} un.</span>
                            </div>`;
                        });
                        stockHtml += `</div>`;
                    }
                    
                    stockHtml += `<div class="mt-2 p-2 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-xs font-semibold text-yellow-700 dark:text-yellow-300">Solicitação será criada automaticamente</span>
                        </div>
                    </div>`;
                    stockHtml += `</div>`;
                    
                    stockDiv.innerHTML = stockHtml;
                    stockDiv.className = 'text-xs transition-all';
                }
            } else {
                // Sem estoque cadastrado
                stockBadge.innerHTML = `<span class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 px-2 py-0.5 rounded text-xs font-bold">-</span>`;
                stockDiv.innerHTML = `<div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 border-2 border-yellow-200 dark:border-yellow-700 rounded-lg">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm font-bold text-yellow-700 dark:text-yellow-300">Sem estoque cadastrado</span>
                    </div>
                    <p class="text-xs text-yellow-600 dark:text-yellow-400">Solicitação será criada automaticamente ao finalizar a venda</p>
                </div>`;
                stockDiv.className = 'text-xs transition-all';
            }
        } else {
            // Sem quantidade selecionada, limpar apenas o conteúdo detalhado
            if (stockDiv) {
                stockDiv.innerHTML = '';
                stockDiv.className = 'text-xs transition-all';
            }
        }
    }
}

// Buscar estoque por tipo de corte
async function loadStockByCutType(cutTypeId) {
    if (!cutTypeId) {
        return;
    }
    
    const stockList = document.getElementById('stock-by-size-list');
    const colorSelect = document.getElementById('modal-color-select');
    if (!stockList) return;
    
    const colorId = colorSelect?.value;
    
    try {
        // Buscar de todas as lojas (não filtrar por loja específica)
        const params = new URLSearchParams({
            cut_type_id: cutTypeId
        });
        
        if (colorId) {
            params.append('color_id', colorId);
        }
        
        const response = await fetch(`/api/stocks/by-cut-type?${params}`);
        const data = await response.json();
        
        if (data.success && data.stock_by_size && data.stock_by_size.length > 0) {
            let html = '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">';
            
            data.stock_by_size.forEach(item => {
                const hasStock = item.available > 0;
                const bgColor = hasStock ? 'bg-green-50 dark:bg-green-900/20 border-green-300 dark:border-green-700' : 'bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600';
                const textColor = hasStock ? 'text-green-800 dark:text-green-200' : 'text-gray-500 dark:text-gray-400';
                
                html += `
                    <div class="p-4 rounded-lg border-2 ${bgColor} transition-all hover:shadow-lg">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <span class="text-base font-bold text-gray-900 dark:text-gray-100">Tamanho ${item.size}</span>
                            </div>
                            ${hasStock ? `
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-200 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Em estoque
                                </span>
                            ` : `
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 dark:bg-red-800 text-red-800 dark:text-red-200">
                                    ✗ Sem estoque
                                </span>
                            `}
                        </div>
                        
                        <div class="mb-2">
                            <div class="text-sm ${textColor}">
                                <span class="font-semibold">${item.available}</span> disponível
                                ${item.reserved > 0 ? `<span class="text-orange-600 dark:text-orange-400">(${item.reserved} reservado)</span>` : ''}
                            </div>
                        </div>
                        
                        ${item.stores && item.stores.length > 0 ? `
                            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                                <div class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">Por Loja:</div>
                                <div class="space-y-1.5">
                                    ${item.stores.map(store => `
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="text-gray-600 dark:text-gray-400 font-medium">${store.store_name}:</span>
                                            <span class="font-semibold ${store.available > 0 ? 'text-green-700 dark:text-green-300' : 'text-gray-500 dark:text-gray-500'}">
                                                ${store.available} disp.
                                                ${store.reserved > 0 ? `<span class="text-orange-600 dark:text-orange-400">(${store.reserved} res.)</span>` : ''}
                                            </span>
                                        </div>
                                        ${store.items && store.items.length > 0 ? `
                                            <div class="ml-2 text-xs text-gray-500 dark:text-gray-500">
                                                ${store.items.map(i => `${i.fabric} ${i.color}`).join(', ')}
                                            </div>
                                        ` : ''}
                                    `).join('')}
                                </div>
                            </div>
                        ` : ''}
                    </div>
                `;
            });
            
            html += '</div>';
            stockList.innerHTML = html;
        } else {
            stockList.innerHTML = `
                <div class="text-center py-8 bg-white dark:bg-gray-800 rounded-xl border-2 border-dashed border-yellow-300 dark:border-yellow-700">
                    <svg class="w-16 h-16 mx-auto mb-4 text-yellow-500 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <p class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-2">
                        Nenhum estoque cadastrado
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        ${colorId ? 'Para esta cor selecionada' : 'Selecione uma cor para verificar o estoque'}
                    </p>
                    ${colorId ? `
                        <div class="mt-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                            <p class="text-xs text-yellow-700 dark:text-yellow-300">
                                ⚠ Solicitações de estoque serão criadas automaticamente ao finalizar a venda
                            </p>
                        </div>
                    ` : ''}
                </div>
            `;
        }
    } catch (error) {
        console.error('Erro ao buscar estoque:', error);
        stockList.innerHTML = `
            <div class="text-sm text-red-600 dark:text-red-400 text-center py-2">
                Erro ao carregar estoque
            </div>
        `;
    }
}

// Verificar disponibilidade de estoque em tempo real
async function checkStockAvailability() {
    if (!currentStoreId) {
        return;
    }
    
    const fabricId = document.getElementById('modal-fabric')?.value;
    const colorId = document.getElementById('modal-color')?.value;
    const cutTypeId = document.getElementById('modal-cut-type-id')?.value;
    const size = document.getElementById('modal-size')?.value;
    const quantity = parseInt(document.getElementById('modal-quantity')?.value || 1);
    
    const stockInfo = document.getElementById('stock-info');
    const stockQuantity = document.getElementById('stock-quantity');
    const stockWarning = document.getElementById('stock-warning');
    const stockSuccess = document.getElementById('stock-success');
    
    // Ocultar mensagens anteriores
    if (stockWarning) stockWarning.classList.add('hidden');
    if (stockSuccess) stockSuccess.classList.add('hidden');
    
    // Verificar se todos os campos estão preenchidos
    if (!fabricId || !colorId || !cutTypeId || !size) {
        if (stockInfo) stockInfo.classList.add('hidden');
        return;
    }
    
    try {
        const params = new URLSearchParams({
            store_id: currentStoreId,
            fabric_id: fabricId,
            color_id: colorId,
            cut_type_id: cutTypeId,
            size: size,
            quantity: quantity
        });
        
        const response = await fetch(`/api/stocks/check?${params}`);
        const data = await response.json();
        
        if (data.success && stockInfo) {
            stockInfo.classList.remove('hidden');
            const available = data.available_quantity || 0;
            const hasStock = data.has_stock || false;
            
            if (stockQuantity) {
                stockQuantity.textContent = `${available} unidade(s)`;
            }
            
            if (hasStock) {
                stockInfo.className = 'mt-3 p-3 rounded-lg border border-green-300 dark:border-green-700 bg-green-50 dark:bg-green-900/20';
                if (stockQuantity) stockQuantity.className = 'text-sm font-bold text-green-600 dark:text-green-400';
                if (stockSuccess) {
                    stockSuccess.classList.remove('hidden');
                    stockSuccess.textContent = `✓ Estoque suficiente para ${quantity} unidade(s)`;
                }
            } else {
                stockInfo.className = 'mt-3 p-3 rounded-lg border border-red-300 dark:border-red-700 bg-red-50 dark:bg-red-900/20';
                if (stockQuantity) stockQuantity.className = 'text-sm font-bold text-red-600 dark:text-red-400';
                if (stockWarning) {
                    stockWarning.classList.remove('hidden');
                    stockWarning.innerHTML = `⚠ Estoque insuficiente! Disponível: ${available} unidade(s). <button type="button" onclick="createStockRequest()" class="text-blue-600 dark:text-blue-400 underline ml-1">Solicitar estoque</button>`;
                }
            }
        } else {
            if (stockInfo) stockInfo.classList.add('hidden');
        }
    } catch (error) {
        console.error('Erro ao verificar estoque:', error);
        if (stockInfo) stockInfo.classList.add('hidden');
    }
}

// Criar solicitação de estoque
async function createStockRequest() {
    if (!currentStoreId) {
        alert('Loja não identificada');
        return;
    }
    
    const fabricId = document.getElementById('modal-fabric')?.value;
    const colorId = document.getElementById('modal-color')?.value;
    const cutTypeId = document.getElementById('modal-cut-type-id')?.value;
    const size = document.getElementById('modal-size')?.value;
    const quantity = parseInt(document.getElementById('modal-quantity')?.value || 1);
    
    if (!fabricId || !colorId || !cutTypeId || !size) {
        alert('Preencha todos os campos de especificação');
        return;
    }
    
    const fabricName = window.fabricsData.find(f => f.id == fabricId)?.name || 'Tecido';
    const colorName = window.colorsData.find(c => c.id == colorId)?.name || 'Cor';
    const cutTypeName = document.getElementById('modal-cut-type')?.value || 'Tipo de Corte';
    
    if (!confirm(`Deseja criar uma solicitação de estoque para:\n\nTecido: ${fabricName}\nCor: ${colorName}\nTipo de Corte: ${cutTypeName}\nTamanho: ${size}\nQuantidade: ${quantity} unidade(s)?`)) {
        return;
    }
    
    try {
        const response = await fetch('/stock-requests', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                requesting_store_id: currentStoreId,
                fabric_id: fabricId,
                color_id: colorId,
                cut_type_id: cutTypeId,
                size: size,
                requested_quantity: quantity,
                request_notes: `Solicitação criada automaticamente do PDV - Quantidade necessária: ${quantity}`
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Solicitação de estoque criada com sucesso!');
            // Atualizar informações de estoque
            checkStockAvailability();
        } else {
            alert('Erro ao criar solicitação: ' + (data.message || 'Erro desconhecido'));
        }
    } catch (error) {
        console.error('Erro ao criar solicitação:', error);
        alert('Erro ao criar solicitação de estoque');
    }
}

// Calcular acréscimos de tamanhos especiais
// IMPORTANTE: Usar preço unitário, não o total, para determinar a faixa de acréscimo
async function calculateSizeSurcharges() {
    const unitPrice = parseFloat(document.getElementById('modal-unit-price')?.value || 0);
    const quantity = parseFloat(document.getElementById('modal-quantity')?.value || 1);
    // Usar preço unitário para buscar a faixa de acréscimo, não o total
    const priceForSurcharge = unitPrice;
    
    // Verificar quais tamanhos estão disponíveis (GG e EXG só para produtos não-tecido)
    const hasGG = document.getElementById('modal-size-gg') !== null;
    const hasEXG = document.getElementById('modal-size-exg') !== null;
    
    let sizes = ['G1', 'G2', 'G3'];
    if (hasGG) sizes.unshift('GG');
    if (hasEXG) sizes.unshift('EXG');
    
    let totalSurcharges = 0;
    
    for (const size of sizes) {
        const quantityInput = document.getElementById(`modal-size-${size.toLowerCase()}`);
        const surchargeDisplay = document.getElementById(`surcharge-${size.toLowerCase()}`);
        
        if (!quantityInput || !surchargeDisplay) continue;
        
        const qty = parseInt(quantityInput.value || 0);
        
        if (qty > 0 && priceForSurcharge > 0) {
            try {
                // Usar preço unitário para buscar a faixa de acréscimo
                const response = await fetch(`{{ url('/api/size-surcharge') }}/${size}/${priceForSurcharge}`);
                const data = await response.json();
                
                if (data.surcharge) {
                    const surchargePerUnit = parseFloat(data.surcharge);
                    const totalSurcharge = surchargePerUnit * qty;
                    totalSurcharges += totalSurcharge;
                    
                    surchargeDisplay.textContent = `R$ ${totalSurcharge.toFixed(2).replace('.', ',')}`;
                    surchargeDisplay.className = 'text-xs text-orange-600 dark:text-orange-400 mt-1 font-medium';
                } else {
                    surchargeDisplay.textContent = 'R$ 0,00';
                    surchargeDisplay.className = 'text-xs text-gray-500 dark:text-gray-400 mt-1';
                }
            } catch (error) {
                console.error(`Erro ao calcular acréscimo ${size}:`, error);
                surchargeDisplay.textContent = 'R$ 0,00';
                surchargeDisplay.className = 'text-xs text-gray-500 dark:text-gray-400 mt-1';
            }
        } else {
            surchargeDisplay.textContent = 'R$ 0,00';
            surchargeDisplay.className = 'text-xs text-gray-500 dark:text-gray-400 mt-1';
        }
    }
    
    const totalSurchargesElement = document.getElementById('total-surcharges-modal');
    if (totalSurchargesElement) {
        totalSurchargesElement.textContent = `R$ ${totalSurcharges.toFixed(2).replace('.', ',')}`;
    }
}

// Variável para controlar personalizações sub.local
let sublocalPersonalizations = [];
let sublocalCounter = 0;
let sublocalSizes = [];

// Carregar tamanhos disponíveis para SUB.LOCAL
async function loadSublocalSizes() {
    try {
        const response = await fetch('/api/personalization-prices/sizes?type=SUB. LOCAL');
        const data = await response.json();
        
        if (data.success && data.sizes) {
            sublocalSizes = data.sizes;
            const sizeSelect = document.getElementById('sublocal-modal-size');
            sizeSelect.innerHTML = '<option value="">Selecione...</option>';
            
            data.sizes.forEach(size => {
                const option = document.createElement('option');
                option.value = size.size_name;
                const dimensions = size.size_dimensions || '';
                option.textContent = dimensions ? `${size.size_name} (${dimensions})` : size.size_name;
                sizeSelect.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Erro ao carregar tamanhos:', error);
    }
}

// Abrir modal de sub.local
function openSublocalModal() {
    // Resetar campos
    document.getElementById('sublocal-modal-location').value = '';
    document.getElementById('sublocal-modal-size').value = '';
    document.getElementById('sublocal-modal-quantity').value = '1';
    document.getElementById('sublocal-modal-price-display').classList.add('hidden');
    
    // Carregar tamanhos
    loadSublocalSizes();
    
    // Mostrar modal
    document.getElementById('sublocal-modal').classList.remove('hidden');
    
    // Adicionar event listeners
    document.getElementById('sublocal-modal-location').addEventListener('change', calculateSublocalModalPrice);
    document.getElementById('sublocal-modal-size').addEventListener('change', calculateSublocalModalPrice);
    document.getElementById('sublocal-modal-quantity').addEventListener('input', calculateSublocalModalPrice);
}

// Fechar modal de sub.local
window.closeSublocalModal = function closeSublocalModal() {
    document.getElementById('sublocal-modal').classList.add('hidden');
}

// Calcular preço no modal de sub.local
async function calculateSublocalModalPrice() {
    const location = document.getElementById('sublocal-modal-location').value;
    const size = document.getElementById('sublocal-modal-size').value;
    const quantity = parseInt(document.getElementById('sublocal-modal-quantity').value || 1);
    
    if (!location || !size || quantity < 1) {
        document.getElementById('sublocal-modal-price-display').classList.add('hidden');
        return;
    }
    
    try {
        const response = await fetch(`/api/personalization-prices/price?type=SUB. LOCAL&size=${encodeURIComponent(size)}&quantity=${quantity}`);
        const data = await response.json();
        
        if (data.success && data.price) {
            const unitPrice = parseFloat(data.price);
            const totalPrice = unitPrice * quantity;
            
            document.getElementById('sublocal-modal-unit-price').textContent = `R$ ${unitPrice.toFixed(2).replace('.', ',')}`;
            document.getElementById('sublocal-modal-total-price').textContent = `R$ ${totalPrice.toFixed(2).replace('.', ',')}`;
            document.getElementById('sublocal-modal-unit-price-value').value = unitPrice;
            document.getElementById('sublocal-modal-final-price-value').value = totalPrice;
            document.getElementById('sublocal-modal-price-display').classList.remove('hidden');
        } else {
            document.getElementById('sublocal-modal-price-display').classList.add('hidden');
        }
    } catch (error) {
        console.error('Erro ao calcular preço:', error);
        document.getElementById('sublocal-modal-price-display').classList.add('hidden');
    }
}

// Confirmar e adicionar personalização sub.local
window.confirmSublocalPersonalization = function confirmSublocalPersonalization() {
    const locationId = document.getElementById('sublocal-modal-location').value;
    const locationName = document.getElementById('sublocal-modal-location').selectedOptions[0]?.text || '';
    const sizeName = document.getElementById('sublocal-modal-size').value;
    const quantity = parseInt(document.getElementById('sublocal-modal-quantity').value || 1);
    const unitPrice = parseFloat(document.getElementById('sublocal-modal-unit-price-value').value || 0);
    const finalPrice = parseFloat(document.getElementById('sublocal-modal-final-price-value').value || 0);
    
    if (!locationId || !sizeName || quantity < 1 || unitPrice <= 0) {
        showNotification('Preencha todos os campos obrigatórios e verifique o preço', 'error');
        return;
    }
    
    const container = document.getElementById('sublocal-personalizations-list');
    if (!container) return;
    
    const id = sublocalCounter++;
    const personalizationHtml = `
        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3 bg-gray-50 dark:bg-gray-700" data-sublocal-id="${id}">
            <div class="flex justify-between items-center mb-2">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">${locationName}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Tamanho: ${sizeName} | Qtd: ${quantity}</p>
                    <p class="text-xs font-semibold text-green-600 dark:text-green-400">Total: R$ ${finalPrice.toFixed(2).replace('.', ',')}</p>
                </div>
                <button type="button" onclick="removeSublocalPersonalization(${id})" class="text-red-600 dark:text-red-400 hover:text-red-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', personalizationHtml);
    
    // Adicionar aos dados
    sublocalPersonalizations.push({
        id: id,
        location_id: locationId,
        location_name: locationName,
        size_name: sizeName,
        quantity: quantity,
        unit_price: unitPrice,
        final_price: finalPrice
    });
    
    // Fechar modal
    closeSublocalModal();
}

// Remover personalização sub.local
function removeSublocalPersonalization(id) {
    const element = document.querySelector(`[data-sublocal-id="${id}"]`);
    if (element) {
        element.remove();
    }
    sublocalPersonalizations = sublocalPersonalizations.filter(p => p.id !== id);
}

// Fechar modal
window.closeAddProductModal = function closeAddProductModal() {
    document.getElementById('add-product-modal').classList.add('hidden');
    currentProductId = null;
    currentProductType = 'product';
    
    // Resetar campos de tamanho (apenas os que existem)
    ['pp', 'p', 'm', 'g', 'gg', 'exg', 'g1', 'g2', 'g3'].forEach(size => {
        const input = document.getElementById(`modal-size-${size}`);
        if (input) input.value = 0;
        const stockDiv = document.getElementById(`stock-${size}`);
        if (stockDiv) stockDiv.innerHTML = '';
        const stockBadge = document.getElementById(`stock-badge-${size}`);
        if (stockBadge) {
            stockBadge.innerHTML = '';
            stockBadge.className = '';
        }
        const display = document.getElementById(`surcharge-${size}`);
        if (display) {
            display.textContent = '+ R$ 0,00';
            display.className = 'text-xs font-semibold text-indigo-600 dark:text-indigo-400 mt-1 text-center';
        }
    });
    const totalSurchargesElement = document.getElementById('total-surcharges-modal');
    if (totalSurchargesElement) {
        totalSurchargesElement.textContent = 'R$ 0,00';
    }
    
    // Limpar total de quantidade
    const totalQuantityDisplay = document.getElementById('total-quantity-display');
    if (totalQuantityDisplay) totalQuantityDisplay.textContent = '0';
    
    const totalItemsDisplay = document.getElementById('total-items-display');
    if (totalItemsDisplay) totalItemsDisplay.textContent = '0';
    
    // Limpar seleção de cor
    const colorSelect = document.getElementById('modal-color-select');
    if (colorSelect) colorSelect.value = '';
    
    // Limpar informações de estoque
    const stockList = document.getElementById('stock-by-size-list');
    if (stockList) {
        stockList.innerHTML = `
            <div class="text-sm text-gray-600 dark:text-gray-400 text-center py-4 bg-white dark:bg-gray-800 rounded-lg border border-dashed border-gray-300 dark:border-gray-600">
                <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="font-medium">Selecione a cor acima para visualizar o estoque disponível</p>
                <p class="text-xs mt-1">O estoque será exibido por tamanho e por loja</p>
            </div>
        `;
    }
    
    // Limpar personalizações sub.local
    const sublocalContainer = document.getElementById('sublocal-personalizations-list');
    if (sublocalContainer) {
        sublocalContainer.innerHTML = '';
    }
    sublocalPersonalizations = [];
    sublocalCounter = 0;
    
    // Fechar modal de sub.local se estiver aberto
    const sublocalModal = document.getElementById('sublocal-modal');
    if (sublocalModal) {
        sublocalModal.classList.add('hidden');
    }
}

// Confirmar adicionar produto
window.confirmAddProduct = async function confirmAddProduct() {
    if (!currentProductId) return;
    
    const product = currentProductType === 'product' 
        ? window.productsData.find(p => p.id === currentProductId)
        : window.productOptionsData.find(p => p.id === currentProductId);
    
    if (!product) return;
    
    // Para product_option (tipo de corte), usar o preço fixo do produto
    // Para produtos normais, usar o preço do input (se existir)
    const unitPriceInput = document.getElementById('modal-unit-price');
    let unitPrice;
    
    if (currentProductType === 'product_option') {
        // Usar preço fixo do tipo de corte
        unitPrice = parseFloat(product.price || 0);
        if (!unitPrice || unitPrice <= 0) {
            showNotification('Preço do produto não configurado', 'error');
            return;
        }
    } else {
        // Para produtos normais, validar o preço do input
        unitPrice = unitPriceInput ? parseFloat(unitPriceInput.value) : null;
        if (!unitPrice || unitPrice <= 0) {
            showNotification('Informe um preço unitário válido', 'error');
            if (unitPriceInput) {
                unitPriceInput.focus();
                unitPriceInput.classList.add('border-red-500');
            }
            return;
        }
    }
    
    const applicationType = document.getElementById('modal-application-type')?.value || null;
    
    // Coletar cor se for product_option
    const colorSelect = document.getElementById('modal-color-select');
    const selectedColorId = colorSelect?.value || null;
    const cutTypeId = document.getElementById('modal-cut-type-id')?.value || null;
    const fabricId = document.getElementById('modal-fabric-id')?.value || null;
    
    // Validar cor para product_option
    if (currentProductType === 'product_option') {
        if (!selectedColorId) {
            showNotification('Selecione a cor', 'error');
            return;
        }
    }
    
    // Coletar quantidades de todos os tamanhos
    const sizeQuantities = {
        'PP': parseInt(document.getElementById('modal-size-pp')?.value || 0),
        'P': parseInt(document.getElementById('modal-size-p')?.value || 0),
        'M': parseInt(document.getElementById('modal-size-m')?.value || 0),
        'G': parseInt(document.getElementById('modal-size-g')?.value || 0),
        'GG': parseInt(document.getElementById('modal-size-gg')?.value || 0),
        'EXG': parseInt(document.getElementById('modal-size-exg')?.value || 0),
        'G1': parseInt(document.getElementById('modal-size-g1')?.value || 0),
        'G2': parseInt(document.getElementById('modal-size-g2')?.value || 0),
        'G3': parseInt(document.getElementById('modal-size-g3')?.value || 0),
    };
    
    // Calcular quantidade total
    const totalQuantity = Object.values(sizeQuantities).reduce((sum, qty) => sum + qty, 0);
    
    if (totalQuantity <= 0) {
        showNotification('Informe pelo menos uma quantidade para algum tamanho', 'error');
        return;
    }
    
    // Coletar personalizações sub.local (já estão no array sublocalPersonalizations)
    const sublocalPersonalizationsToSend = sublocalPersonalizations.map(p => ({
        location_id: p.location_id,
        location_name: p.location_name,
        size_name: p.size_name,
        quantity: p.quantity,
        unit_price: p.unit_price,
        final_price: p.final_price
    }));
    
    // Para product_option, adicionar cada tamanho como item separado ou enviar todos juntos
    if (currentProductType === 'product_option') {
        // Adicionar cada tamanho que tiver quantidade > 0 (sequencialmente para evitar problemas de sincronização)
        let itemsAdded = 0;
        let lastError = null;
        
        // Usar for...of com await para garantir que cada item seja adicionado antes do próximo
        const sizes = Object.entries(sizeQuantities).filter(([size, qty]) => qty > 0);
        
        for (const [size, qty] of sizes) {
            try {
                // Para tamanhos especiais (GG, EXG, G1, G2, G3), enviar size_quantities
                // para que o servidor calcule o acréscimo corretamente
                const sizeQuantitiesForSurcharge = {};
                if (['GG', 'EXG', 'G1', 'G2', 'G3'].includes(size)) {
                    sizeQuantitiesForSurcharge[size] = qty;
                }
                
                const result = await addProductToCart(
                    currentProductId, 
                    currentProductType, 
                    null, 
                    unitPrice, 
                    qty, 
                    applicationType, 
                    sizeQuantitiesForSurcharge, // Enviar size_quantities para calcular acréscimo
                    sublocalPersonalizationsToSend,
                    size, // tamanho específico
                    selectedColorId,
                    cutTypeId,
                    fabricId
                );
                if (result && result.success) {
                    itemsAdded++;
                    if (result.stock_request_created) {
                        // Marcar que houve solicitação de estoque (mostrar aviso no final)
                        lastError = { type: 'stock_request' };
                    }
                } else {
                    lastError = result || { type: 'unknown' };
                }
            } catch (error) {
                console.error(`Erro ao adicionar tamanho ${size}:`, error);
                lastError = error;
            }
        }
        
        if (itemsAdded > 0) {
            closeAddProductModal();
            // Buscar carrinho atualizado do servidor para garantir que temos todos os itens
            fetch('{{ route("pdv.cart.get") }}', {
                method: 'GET',
                headers: { 'Accept': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.cart) {
                    updateCartDisplay(data.cart, data.cart_total);
                }
            })
            .catch(err => console.error('Erro ao atualizar carrinho:', err));
            
            // Mostrar notificação apropriada
            if (lastError && lastError.type === 'stock_request') {
                showNotification(`${itemsAdded} item(ns) adicionado(s) ao carrinho. Algumas solicitações de estoque foram criadas.`, 'warning');
            } else if (lastError && lastError.type !== 'stock_request') {
                showNotification(`${itemsAdded} item(ns) adicionado(s), mas houve erros`, 'warning');
            } else {
                showNotification(`${itemsAdded} item(ns) adicionado(s) ao carrinho`, 'success');
            }
        } else if (lastError) {
            showNotification('Erro ao adicionar itens ao carrinho', 'error');
        }
    } else {
        // Para produtos normais, usar a lógica antiga
        const quantity = parseFloat(document.getElementById('modal-quantity')?.value || totalQuantity);
        try {
            const result = await addProductToCart(
                currentProductId, 
                currentProductType, 
                null, 
                unitPrice, 
                quantity, 
                applicationType, 
                sizeQuantities, 
                sublocalPersonalizationsToSend
            );
            closeAddProductModal();
            if (result && result.success) {
                if (result.stock_request_created) {
                    showNotification('Item adicionado ao carrinho. Solicitação de estoque criada automaticamente.', 'warning');
                } else {
                    showNotification('Item adicionado ao carrinho', 'success');
                }
            } else {
                showNotification(result?.message || 'Erro ao adicionar item ao carrinho', 'error');
            }
        } catch (error) {
            console.error('Erro ao adicionar produto:', error);
            showNotification('Erro ao adicionar item ao carrinho', 'error');
        }
    }
    
    // Limpar personalizações após adicionar
    sublocalPersonalizations = [];
    sublocalCounter = 0;
}

// CSRF Token
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

// Atualizar total quando desconto ou taxa mudar
document.getElementById('discount-input')?.addEventListener('input', updateTotal);
document.getElementById('delivery-fee-input')?.addEventListener('input', updateTotal);

// Busca de produtos
document.getElementById('product-search')?.addEventListener('input', function(e) {
    const search = e.target.value.toLowerCase();
    const productCards = document.querySelectorAll('.product-card');
    
    productCards.forEach(card => {
        const title = card.getAttribute('data-product-title');
        const category = card.getAttribute('data-product-category');
        
        if (title.includes(search) || category.includes(search)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
});

// Listener removido - não há mais select de cliente, apenas campo de busca
// O botão de checkout sempre está habilitado agora (cliente é opcional)
// Não há mais validação de cliente obrigatório no frontend

// Função para adicionar produto ao carrinho
async function addProductToCart(itemId, type, productTitle, unitPrice, quantity = 1, applicationType = null, sizeQuantities = {}, sublocalPersonalizations = [], selectedSize = null, selectedColorId = null, cutTypeId = null, fabricId = null) {
    try {
        const body = {
            quantity: quantity,
            unit_price: unitPrice,
            size_quantities: sizeQuantities,
        };
        
        if (type === 'product') {
            body.product_id = itemId;
            body.application_type = applicationType;
        } else if (type === 'product_option') {
            body.product_option_id = itemId;
            body.size = selectedSize;
            body.color_id = selectedColorId;
            body.cut_type_id = cutTypeId;
            body.fabric_id = fabricId;
            if (sublocalPersonalizations && sublocalPersonalizations.length > 0) {
                body.sublocal_personalizations = sublocalPersonalizations;
            }
        }
        
        const response = await fetch('{{ route("pdv.cart.add") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(body)
        });

        const data = await response.json();

        if (data.success) {
            updateCartDisplay(data.cart, data.cart_total);
            // Retornar resultado para a função chamadora decidir quando mostrar notificação
            return { success: true, stock_request_created: data.stock_request_created || false };
        } else {
            return { success: false, message: data.message || 'Erro ao adicionar item' };
        }
    } catch (error) {
        console.error('Erro:', error);
        return { success: false, message: 'Erro ao adicionar item ao carrinho' };
    }
}

// Função para atualizar item do carrinho
async function updateCartItem(itemId, quantity, unitPrice) {
    try {
        const response = await fetch('{{ route("pdv.cart.update") }}', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                item_id: itemId,
                quantity: quantity,
                unit_price: unitPrice
            })
        });

        const data = await response.json();

        if (data.success) {
            updateCartDisplay(data.cart, data.cart_total);
        } else {
            showNotification(data.message || 'Erro ao atualizar item', 'error');
        }
    } catch (error) {
        console.error('Erro:', error);
        showNotification('Erro ao atualizar item', 'error');
    }
}

// Função para remover item do carrinho
async function removeCartItem(itemId) {
    try {
        const response = await fetch('{{ route("pdv.cart.remove") }}', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                item_id: itemId
            })
        });

        const data = await response.json();

        if (data.success) {
            updateCartDisplay(data.cart, data.cart_total);
            showNotification('Item removido do carrinho', 'success');
        } else {
            showNotification(data.message || 'Erro ao remover item', 'error');
        }
    } catch (error) {
        console.error('Erro:', error);
        showNotification('Erro ao remover item', 'error');
    }
}

// Função para limpar carrinho
// Abrir modal de confirmação para limpar carrinho
window.clearCart = function clearCart() {
    document.getElementById('clear-cart-modal').classList.remove('hidden');
}

// Fechar modal de confirmação
window.closeClearCartModal = function closeClearCartModal() {
    document.getElementById('clear-cart-modal').classList.add('hidden');
}

// Confirmar limpeza do carrinho
window.confirmClearCart = async function confirmClearCart() {
    closeClearCartModal();
    
    try {
        const response = await fetch('{{ route("pdv.cart.clear") }}', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            updateCartDisplay([], 0);
            showNotification('Carrinho limpo', 'success');
        }
    } catch (error) {
        console.error('Erro:', error);
        showNotification('Erro ao limpar carrinho', 'error');
    }
}

// Função para atualizar exibição do carrinho
function updateCartDisplay(cart, cartTotal) {
    const cartItemsContainer = document.getElementById('cart-items');
    
    if (!cart || cart.length === 0) {
        cartItemsContainer.innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-center py-4">Carrinho vazio</p>';
    } else {
        cartItemsContainer.innerHTML = cart.map(item => {
            let surchargesHtml = '';
            if (item.size_surcharges && Object.keys(item.size_surcharges).length > 0) {
                surchargesHtml = '<div class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-700">';
                surchargesHtml += '<p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Adicionais:</p>';
                for (const [size, data] of Object.entries(item.size_surcharges)) {
                    if (data.quantity > 0) {
                        surchargesHtml += `<p class="text-xs text-orange-600 dark:text-orange-400">${size} (${data.quantity}x): +R$ ${parseFloat(data.total).toFixed(2).replace('.', ',')}</p>`;
                    }
                }
                surchargesHtml += '</div>';
            }
            
            let sublocalHtml = '';
            if (item.sublocal_personalizations && item.sublocal_personalizations.length > 0) {
                sublocalHtml = '<div class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-700">';
                sublocalHtml += '<p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Personalizações SUB.LOCAL:</p>';
                item.sublocal_personalizations.forEach((personalization, index) => {
                    const locationName = personalization.location_name || 'Local não informado';
                    const sizeName = personalization.size_name ? ` - ${personalization.size_name}` : '';
                    sublocalHtml += `<p class="text-xs text-green-600 dark:text-green-400">${locationName}${sizeName} (${personalization.quantity}x): R$ ${parseFloat(personalization.final_price || 0).toFixed(2).replace('.', ',')}</p>`;
                });
                sublocalHtml += '</div>';
            }
            
            return `
            <div class="cart-item border border-gray-200 dark:border-gray-700 rounded-lg p-3" data-item-id="${item.id}">
                <div class="flex justify-between items-start mb-2">
                    <div class="flex-1">
                        <p class="font-medium text-gray-900 dark:text-gray-100">${item.product_title}${item.size ? ` - ${item.size}` : ''}</p>
                        ${item.sale_type && item.sale_type !== 'unidade' ? `<p class="text-xs text-gray-500 dark:text-gray-400">Venda por ${item.sale_type === 'kg' ? 'Kg' : 'Metro'}</p>` : ''}
                        ${item.application_type ? `<p class="text-xs text-green-600 dark:text-green-400">Aplicação: ${item.application_type === 'sublimacao_local' ? 'Sublimação Local' : 'DTF'}</p>` : ''}
                        ${surchargesHtml}
                        ${sublocalHtml}
                        <div class="flex items-center gap-2 mt-1">
                            <input type="number" 
                                   value="${item.quantity}" 
                                   step="${item.sale_type && item.sale_type !== 'unidade' ? '0.01' : '1'}"
                                   min="${item.sale_type && item.sale_type !== 'unidade' ? '0.01' : '1'}"
                                   onchange="updateCartItem('${item.id}', this.value, null)"
                                   class="w-16 px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                            <span class="text-sm text-gray-500 dark:text-gray-400">x</span>
                            <input type="number" 
                                   step="0.01"
                                   value="${parseFloat(item.unit_price).toFixed(2)}" 
                                   min="0"
                                   onchange="updateCartItem('${item.id}', null, this.value)"
                                   class="w-20 px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        </div>
                    </div>
                    <button onclick="removeCartItem('${item.id}')" 
                            class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                    Total: R$ ${parseFloat(item.total_price).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                </p>
            </div>
        `;
        }).join('');
    }

    // Atualizar total usando os dados do servidor
    if (cart && cart.length > 0) {
        let subtotal = 0;
        let totalQuantity = 0; // Total de itens (quantidade)
        
        cart.forEach(item => {
            subtotal += parseFloat(item.total_price || 0);
            // Somar a quantidade de cada item
            totalQuantity += parseFloat(item.quantity || 0);
        });
        
        const discount = parseFloat(document.getElementById('discount-input')?.value || 0);
        const deliveryFee = parseFloat(document.getElementById('delivery-fee-input')?.value || 0);
        const total = subtotal - discount + deliveryFee;

        document.getElementById('cart-subtotal').textContent = 
            'R$ ' + subtotal.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('cart-total').textContent = 
            'R$ ' + total.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        
        // Atualizar total de itens no carrinho (se existir elemento)
        const cartTotalItems = document.getElementById('cart-total-items');
        if (cartTotalItems) {
            cartTotalItems.textContent = totalQuantity;
        }
    } else {
        updateTotal();
        // Se carrinho vazio, zerar total de itens
        const cartTotalItems = document.getElementById('cart-total-items');
        if (cartTotalItems) {
            cartTotalItems.textContent = '0';
        }
    }
    
    updateCheckoutButtonState();
}

// Função para atualizar estado do botão de finalizar
function updateCheckoutButtonState() {
    const cartItems = document.querySelectorAll('.cart-item');
    const checkoutBtn = document.getElementById('checkout-btn');
    
    if (checkoutBtn) {
        // Habilitar apenas se tiver itens no carrinho (cliente é opcional)
        if (cartItems.length > 0) {
            checkoutBtn.disabled = false;
        } else {
            checkoutBtn.disabled = true;
        }
    }
}

// Função para atualizar total
function updateTotal() {
    // Buscar carrinho do servidor para ter os valores corretos (incluindo sub.local)
    fetch('{{ route("pdv.cart.get") }}', {
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        const cart = data.cart || [];
        
        // Calcular subtotal somando o total_price de cada item (que já inclui sub.local e acréscimos)
        let subtotal = 0;
        cart.forEach(item => {
            // Usar o total_price do item que já vem do servidor (inclui sub.local, acréscimos de tamanho, etc)
            subtotal += parseFloat(item.total_price || 0);
        });

        const discount = parseFloat(document.getElementById('discount-input')?.value || 0);
        const deliveryFee = parseFloat(document.getElementById('delivery-fee-input')?.value || 0);
        const total = subtotal - discount + deliveryFee;

        document.getElementById('cart-subtotal').textContent = 
            'R$ ' + subtotal.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('cart-total').textContent = 
            'R$ ' + total.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    })
    .catch(error => {
        console.error('Erro ao buscar carrinho:', error);
        // Fallback: extrair total_price dos itens exibidos no DOM
        const cartItems = document.querySelectorAll('.cart-item');
        let subtotal = 0;

        cartItems.forEach(item => {
            // Extrair o total_price do texto exibido
            const totalPriceText = item.querySelector('p.text-sm.font-semibold')?.textContent;
            if (totalPriceText) {
                const match = totalPriceText.match(/R\$\s*([\d.,]+)/);
                if (match) {
                    // Converter formato brasileiro para número (ex: "256,10" -> 256.10)
                    const priceStr = match[1].replace(/\./g, '').replace(',', '.');
                    const price = parseFloat(priceStr);
                    if (!isNaN(price)) {
                        subtotal += price;
                    }
                }
            }
        });

        const discount = parseFloat(document.getElementById('discount-input')?.value || 0);
        const deliveryFee = parseFloat(document.getElementById('delivery-fee-input')?.value || 0);
        const total = subtotal - discount + deliveryFee;

        document.getElementById('cart-subtotal').textContent = 
            'R$ ' + subtotal.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('cart-total').textContent = 
            'R$ ' + total.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    });
}

// Função para finalizar venda
// Variáveis globais para pagamento
let paymentMethods = [];
let checkoutData = null;

// Função para finalizar venda - abre modal de pagamento
async function checkout() {
    // Buscar valor do client_id - pode ser vazio, null ou um ID
    const clientIdElement = document.getElementById('client_id');
    let clientId = clientIdElement ? clientIdElement.value : null;
    
    // Normalizar: se for string vazia, undefined, null ou 'null', converter para null
    if (!clientId || clientId === '' || clientId === 'null' || clientId === 'undefined') {
        clientId = null;
    }
    
    console.log('Checkout - client_id:', clientId);
    
    // Buscar carrinho do servidor
    try {
        const response = await fetch('{{ route("pdv.cart.get") }}', {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        const cart = data.cart || [];
        
        if (cart.length === 0) {
            showNotification('Carrinho vazio', 'error');
            return;
        }
        
        // Calcular totais
        const subtotal = cart.reduce((sum, item) => sum + parseFloat(item.total_price || 0), 0);
        const discount = parseFloat(document.getElementById('discount-input')?.value || 0);
        const deliveryFee = parseFloat(document.getElementById('delivery-fee-input')?.value || 0);
        const total = subtotal - discount + deliveryFee;
        
        // Salvar dados do checkout
        // Garantir que client_id seja null se vazio
        checkoutData = {
            client_id: clientId, // Já normalizado acima
            discount: discount,
            delivery_fee: deliveryFee,
            notes: document.getElementById('notes-input')?.value || '',
            total: total
        };
        
        console.log('Checkout data:', checkoutData);
        
        // Resetar métodos de pagamento
        paymentMethods = [];
        
        // Atualizar modal de pagamento
        document.getElementById('payment-total').textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
        renderPaymentMethods();
        updatePaymentTotals();
        
        // Abrir modal de pagamento
        document.getElementById('payment-modal').classList.remove('hidden');
    } catch (error) {
        console.error('Erro ao buscar carrinho:', error);
        showNotification('Erro ao buscar carrinho', 'error');
    }
}

// Função para finalizar venda sem cliente - vai direto para o checkout
window.checkoutWithoutClient = async function checkoutWithoutClient() {
    // Buscar carrinho do servidor
    try {
        const response = await fetch('{{ route("pdv.cart.get") }}', {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        const cart = data.cart || [];
        
        if (cart.length === 0) {
            showNotification('Carrinho vazio', 'error');
            return;
        }
        
        // Calcular totais
        const subtotal = cart.reduce((sum, item) => sum + parseFloat(item.total_price || 0), 0);
        const discount = parseFloat(document.getElementById('discount-input')?.value || 0);
        const deliveryFee = parseFloat(document.getElementById('delivery-fee-input')?.value || 0);
        const total = subtotal - discount + deliveryFee;
        
        // Preparar dados do checkout SEM cliente (sempre null)
        const checkoutPayload = {
            client_id: null, // Sempre null para vendas sem cliente
            discount: discount,
            delivery_fee: deliveryFee,
            notes: document.getElementById('notes-input')?.value || '',
            payment_methods: [
                {
                    method: 'dinheiro',
                    amount: total
                }
            ] // Pagamento único em dinheiro no valor total
        };
        
        console.log('Finalizando sem cliente:', checkoutPayload);
        
        const confirmBtn = document.getElementById('checkout-without-client-btn');
        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Processando...';
        
        try {
            const response = await fetch('{{ route("pdv.checkout") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(checkoutPayload)
            });
            
            const data = await response.json();
            
            if (data.success) {
                let message = `Pedido #${data.order_number} criado com sucesso!`;
                if (data.stock_requests_created && data.stock_requests_created.length > 0) {
                    message += ` ${data.stock_requests_created.length} solicitação(ões) de estoque criada(s).`;
                }
                showNotification(message, data.stock_requests_created && data.stock_requests_created.length > 0 ? 'warning' : 'success');
                
                // Gerar nota da venda (abrir em nova aba)
                if (data.receipt_url) {
                    window.open(data.receipt_url, '_blank');
                }
                
                // Limpar carrinho e redirecionar para o pedido
                sessionStorage.removeItem('pdv_cart');
                setTimeout(() => {
                    window.location.href = '{{ route("orders.show", ":id") }}'.replace(':id', data.order_id);
                }, 1500);
            } else {
                const errorMessage = data.message || (data.errors ? JSON.stringify(data.errors) : 'Erro ao finalizar venda');
                console.error('Erro no checkout:', data);
                showNotification(errorMessage, 'error');
                confirmBtn.disabled = false;
                confirmBtn.textContent = 'Finalizar Sem Cliente';
            }
        } catch (error) {
            console.error('Erro:', error);
            showNotification('Erro ao finalizar venda: ' + error.message, 'error');
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Finalizar Sem Cliente';
        }
    } catch (error) {
        console.error('Erro:', error);
        showNotification('Erro ao buscar carrinho', 'error');
    }
}

// Adicionar método de pagamento
function addPaymentMethod() {
    const method = document.getElementById('new-payment-method').value;
    const amount = parseFloat(document.getElementById('new-payment-amount').value);
    
    if (!method) {
        showNotification('Selecione uma forma de pagamento', 'error');
        return;
    }
    
    if (!amount || amount <= 0) {
        showNotification('Informe um valor válido', 'error');
        return;
    }
    
    paymentMethods.push({
        id: Date.now() + Math.random(),
        method: method,
        amount: amount
    });
    
    document.getElementById('new-payment-method').value = '';
    document.getElementById('new-payment-amount').value = '';
    
    renderPaymentMethods();
    updatePaymentTotals();
}

// Remover método de pagamento
function removePaymentMethod(id) {
    paymentMethods = paymentMethods.filter(pm => pm.id !== id);
    renderPaymentMethods();
    updatePaymentTotals();
}

// Renderizar lista de métodos de pagamento
function renderPaymentMethods() {
    const container = document.getElementById('payment-methods-list');
    
    if (paymentMethods.length === 0) {
        container.innerHTML = '<p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Nenhuma forma de pagamento adicionada</p>';
        return;
    }
    
    container.innerHTML = paymentMethods.map(pm => `
        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <div>
                <span class="font-medium text-gray-900 dark:text-gray-100 capitalize">${pm.method}</span>
                <span class="text-sm text-gray-600 dark:text-gray-400 ml-2">R$ ${pm.amount.toFixed(2).replace('.', ',')}</span>
            </div>
            <button onclick="removePaymentMethod(${pm.id})" 
                    class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `).join('');
}

// Atualizar totais do pagamento
function updatePaymentTotals() {
    const total = checkoutData?.total || 0;
    const totalPaid = paymentMethods.reduce((sum, pm) => sum + pm.amount, 0);
    const remaining = total - totalPaid;
    
    document.getElementById('payment-total-paid').textContent = `R$ ${totalPaid.toFixed(2).replace('.', ',')}`;
    
    const remainingElement = document.getElementById('payment-remaining');
    remainingElement.textContent = `R$ ${Math.abs(remaining).toFixed(2).replace('.', ',')}`;
    remainingElement.className = remaining >= 0 
        ? 'text-lg font-semibold text-gray-900 dark:text-gray-100' 
        : 'text-lg font-semibold text-orange-600 dark:text-orange-400';
    
    // Habilitar botão apenas se houver pelo menos um método de pagamento
    const confirmBtn = document.getElementById('confirm-payment-btn');
    confirmBtn.disabled = paymentMethods.length === 0;
}

// Fechar modal de pagamento
function closePaymentModal() {
    document.getElementById('payment-modal').classList.add('hidden');
    paymentMethods = [];
    checkoutData = null;
}

// Confirmar pagamento e finalizar venda
async function confirmPayment() {
    if (paymentMethods.length === 0) {
        showNotification('Adicione pelo menos uma forma de pagamento', 'error');
        return;
    }
    
    const confirmBtn = document.getElementById('confirm-payment-btn');
    confirmBtn.disabled = true;
    confirmBtn.textContent = 'Processando...';
    
    try {
        // Preparar dados do checkout, garantindo que client_id seja null se vazio
        const checkoutPayload = {
            ...checkoutData,
            payment_methods: paymentMethods
        };
        
        // Garantir que client_id seja null se vazio, undefined ou string vazia
        if (!checkoutPayload.client_id || 
            checkoutPayload.client_id === '' || 
            checkoutPayload.client_id === 'null' || 
            checkoutPayload.client_id === 'undefined') {
            checkoutPayload.client_id = null;
        }
        
        console.log('Enviando checkout payload:', JSON.stringify(checkoutPayload, null, 2));
        
        const response = await fetch('{{ route("pdv.checkout") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(checkoutPayload)
        });
        
        console.log('Response status:', response.status);
        
        const data = await response.json();
        
        if (data.success) {
            // Verificar se há solicitações de estoque criadas
            let message = `Pedido #${data.order_number} criado com sucesso!`;
            let notificationType = 'success';
            
            if (data.stock_requests_created && data.stock_requests_created.length > 0) {
                const requestsCount = data.stock_requests_created.length;
                const requestsInfo = data.stock_requests_created.map(r => 
                    `${r.size}: ${r.quantity}`
                ).join(', ');
                message += ` ${requestsCount} solicitação(ões) de estoque criada(s): ${requestsInfo}`;
                notificationType = 'warning';
            }
            
            showNotification(message, notificationType);
            
            // Fechar modal
            closePaymentModal();
            
            // Gerar nota da venda (abrir em nova aba)
            if (data.receipt_url) {
                window.open(data.receipt_url, '_blank');
            }
            
            // Limpar carrinho e redirecionar para o pedido
            sessionStorage.removeItem('pdv_cart');
            setTimeout(() => {
                window.location.href = '{{ route("orders.show", ":id") }}'.replace(':id', data.order_id);
            }, 1500);
        } else {
            const errorMessage = data.message || (data.errors ? JSON.stringify(data.errors) : 'Erro ao finalizar venda');
            console.error('Erro no checkout:', data);
            showNotification(errorMessage, 'error');
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Finalizar Venda';
        }
    } catch (error) {
        console.error('Erro:', error);
        showNotification('Erro ao finalizar venda: ' + error.message, 'error');
        confirmBtn.disabled = false;
        confirmBtn.textContent = 'Finalizar Venda';
    }
}

// Função para mostrar notificações
function showNotification(message, type = 'info') {
    // Criar elemento de notificação
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        'bg-blue-500 text-white'
    }`;
    notification.textContent = message;

    document.body.appendChild(notification);

    // Remover após 3 segundos
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Função para buscar clientes
window.searchClient = function searchClient() {
    const query = document.getElementById('search-client').value;
    const resultsDiv = document.getElementById('search-results');
    
    if (query.length < 3) {
        resultsDiv.innerHTML = '<p class="text-sm text-gray-500 dark:text-gray-400 p-2">Digite ao menos 3 caracteres para buscar</p>';
        return;
    }

    fetch(`/api/clients/search?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                resultsDiv.innerHTML = '<p class="text-sm text-gray-500 dark:text-gray-400 p-2">Nenhum cliente encontrado</p>';
                return;
            }

            resultsDiv.innerHTML = data.map(client => `
                <div class="p-3 bg-white dark:bg-slate-800 rounded-lg border-2 border-gray-200 dark:border-slate-700 hover:border-indigo-400 dark:hover:border-indigo-500 hover:shadow-md cursor-pointer transition-all"
                     onclick='selectClient(${JSON.stringify(client)})'>
                    <div class="flex items-center space-x-3">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900 dark:text-white">${client.name || 'Sem nome'}</p>
                            ${client.phone_primary ? `<p class="text-sm text-gray-600 dark:text-gray-400">${client.phone_primary}</p>` : ''}
                            ${client.cpf_cnpj ? `<p class="text-xs text-gray-500 dark:text-gray-500">${client.cpf_cnpj}</p>` : ''}
                        </div>
                    </div>
                </div>
            `).join('');
        })
        .catch(error => {
            console.error('Erro ao buscar clientes:', error);
            resultsDiv.innerHTML = '<p class="text-sm text-red-500 dark:text-red-400 p-2">Erro ao buscar clientes. Tente novamente.</p>';
        });
}

// Função para selecionar cliente
function selectClient(client) {
    document.getElementById('client_id').value = client.id;
    
    // Mostrar cliente selecionado
    const displayDiv = document.getElementById('selected-client-display');
    const nameDiv = document.getElementById('selected-client-name');
    const infoDiv = document.getElementById('selected-client-info');
    
    nameDiv.textContent = client.name || 'Sem nome';
    
    let info = [];
    if (client.phone_primary) info.push(client.phone_primary);
    if (client.cpf_cnpj) info.push(client.cpf_cnpj);
    infoDiv.textContent = info.join(' • ') || '';
    
    displayDiv.classList.remove('hidden');
    
    // Limpar busca
    document.getElementById('search-client').value = '';
    document.getElementById('search-client').value = '';
    document.getElementById('search-results').innerHTML = '';
    
    updateCheckoutButtonState();
}

// Função para limpar cliente selecionado
window.clearSelectedClient = function clearSelectedClient() {
    const clientIdElement = document.getElementById('client_id');
    if (clientIdElement) {
        clientIdElement.value = '';
        clientIdElement.removeAttribute('value'); // Garantir que não tenha valor
    }
    document.getElementById('selected-client-display').classList.add('hidden');
    document.getElementById('search-client').value = '';
    document.getElementById('search-client').value = '';
    document.getElementById('search-results').innerHTML = '';
    
    updateCheckoutButtonState();
}

// Permitir buscar ao pressionar Enter
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-client');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchClient();
            }
        });
    }
});

// Calcular acréscimos de tamanho automaticamente quando GG, EXG, G1, G2, G3 são selecionados
// Usa os valores fixos do modelo SizeSurcharge
window.calculateSizeSurcharges = async function calculateSizeSurcharges() {
    const unitPrice = parseFloat(document.getElementById('modal-unit-price')?.value || 0);
    if (unitPrice <= 0) {
        // Se não houver preço, limpar todos os acréscimos
        ['gg', 'exg', 'g1', 'g2', 'g3'].forEach(size => {
            const surchargeDisplay = document.getElementById(`surcharge-${size}`);
            if (surchargeDisplay) {
                surchargeDisplay.textContent = '+ R$ 0,00';
            }
        });
        const totalSurchargesElement = document.getElementById('total-surcharges-modal');
        if (totalSurchargesElement) {
            totalSurchargesElement.textContent = 'R$ 0,00';
        }
        return;
    }
    
    // Tabela de acréscimos (valores fixos do modelo SizeSurcharge)
    const surchargeTable = {
        'GG': [
            { from: 0, to: 19.99, surcharge: 1.00 },
            { from: 20.00, to: 49.99, surcharge: 2.00 },
            { from: 50.00, to: 549.99, surcharge: 5.00 },
            { from: 550.00, to: null, surcharge: 5.00 }
        ],
        'EXG': [
            { from: 0, to: 19.99, surcharge: 2.00 },
            { from: 20.00, to: 49.99, surcharge: 4.00 },
            { from: 50.00, to: 549.99, surcharge: 10.00 },
            { from: 550.00, to: null, surcharge: 10.00 }
        ],
        'G1': [
            { from: 20.00, to: 49.99, surcharge: 10.00 },
            { from: 50.00, to: 549.99, surcharge: 15.00 },
            { from: 550.00, to: null, surcharge: 20.00 }
        ],
        'G2': [
            { from: 20.00, to: 49.99, surcharge: 20.00 },
            { from: 50.00, to: 549.99, surcharge: 25.00 },
            { from: 550.00, to: null, surcharge: 40.00 }
        ],
        'G3': [
            { from: 20.00, to: 49.99, surcharge: 30.00 },
            { from: 50.00, to: 549.99, surcharge: 35.00 },
            { from: 550.00, to: null, surcharge: 60.00 }
        ]
    };
    
    // Função para encontrar o acréscimo baseado no preço
    function getSurchargeForSize(size, price) {
        const ranges = surchargeTable[size];
        if (!ranges) return 0;
        
        for (const range of ranges) {
            if (price >= range.from && (range.to === null || price <= range.to)) {
                return range.surcharge;
            }
        }
        return 0;
    }
    
    // Tamanhos que têm acréscimo: GG, EXG, G1, G2, G3
    const sizes = ['GG', 'EXG', 'G1', 'G2', 'G3'];
    let totalSurcharges = 0;
    
    for (const size of sizes) {
        const quantityInput = document.getElementById(`modal-size-${size.toLowerCase()}`);
        const surchargeDisplay = document.getElementById(`surcharge-${size.toLowerCase()}`);
        
        if (!quantityInput || !surchargeDisplay) continue;
        
        const qty = parseInt(quantityInput.value || 0);
        
        if (qty > 0 && unitPrice > 0) {
            const surchargePerUnit = getSurchargeForSize(size, unitPrice);
            const surchargeTotal = surchargePerUnit * qty;
            totalSurcharges += surchargeTotal;
            
            if (surchargeTotal > 0) {
                surchargeDisplay.textContent = `+ R$ ${surchargeTotal.toFixed(2).replace('.', ',')}`;
                surchargeDisplay.className = 'text-xs font-semibold text-indigo-600 dark:text-indigo-400 mt-1 text-center';
            } else {
                surchargeDisplay.textContent = '+ R$ 0,00';
                surchargeDisplay.className = 'text-xs font-semibold text-indigo-600 dark:text-indigo-400 mt-1 text-center';
            }
        } else {
            surchargeDisplay.textContent = '+ R$ 0,00';
            surchargeDisplay.className = 'text-xs font-semibold text-indigo-600 dark:text-indigo-400 mt-1 text-center';
        }
    }
    
    // Atualizar total de acréscimos
    const totalSurchargesElement = document.getElementById('total-surcharges-modal');
    if (totalSurchargesElement) {
        totalSurchargesElement.textContent = `R$ ${totalSurcharges.toFixed(2).replace('.', ',')}`;
    }
}
</script>
@endsection

