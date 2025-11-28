@extends('layouts.admin')

@section('content')
@if(session('success'))
<div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
    <div class="flex items-center">
        <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
    </div>
</div>
@endif

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Controle de Estoque</h1>
    <div class="flex gap-3">
        <a href="{{ route('stocks.create') }}" 
           class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Cadastrar Estoque
        </a>
        <a href="{{ route('stock-requests.index') }}" 
           class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
            Solicitações de Estoque
        </a>
    </div>
</div>

<!-- Filtros -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/25 p-6 mb-6">
    <form method="GET" action="{{ route('stocks.index') }}" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Loja</label>
                <select name="store_id" class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                    <option value="">Todas</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}" {{ $storeId == $store->id ? 'selected' : '' }}>
                            {{ $store->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tecido</label>
                <select name="fabric_id" class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                    <option value="">Todos</option>
                    @foreach($fabrics as $fabric)
                        <option value="{{ $fabric->id }}" {{ $fabricId == $fabric->id ? 'selected' : '' }}>
                            {{ $fabric->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Cor</label>
                <select name="color_id" class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                    <option value="">Todas</option>
                    @foreach($colors as $color)
                        <option value="{{ $color->id }}" {{ $colorId == $color->id ? 'selected' : '' }}>
                            {{ $color->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tipo de Corte</label>
                <select name="cut_type_id" class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                    <option value="">Todos</option>
                    @foreach($cutTypes as $cutType)
                        <option value="{{ $cutType->id }}" {{ $cutTypeId == $cutType->id ? 'selected' : '' }}>
                            {{ $cutType->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tamanho</label>
                <select name="size" class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                    <option value="">Todos</option>
                    @foreach($sizes as $size)
                        <option value="{{ $size }}" {{ $size == request('size') ? 'selected' : '' }}>
                            {{ $size }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <div class="flex items-center">
                <input type="checkbox" name="low_stock" id="low_stock" value="1" {{ $lowStock ? 'checked' : '' }} class="rounded border-gray-300">
                <label for="low_stock" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Mostrar apenas estoque baixo</label>
            </div>

            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                Filtrar
            </button>
            <a href="{{ route('stocks.index') }}" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                Limpar
            </a>
        </div>
    </form>
</div>

<!-- Lista de Estoque Agrupada por Loja e Cor -->
<div class="space-y-6">
@forelse($groupedStocks as $key => $group)
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg dark:shadow-gray-900/50 overflow-hidden border border-gray-200 dark:border-gray-700">
        <!-- Cabeçalho da Loja -->
        <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-6">
                    <div>
                        <h3 class="text-lg font-bold">{{ $group['store']['name'] }}</h3>
                        <div class="flex items-center gap-4 mt-1 text-sm text-indigo-100">
                            <span><strong>Tecido:</strong> {{ $group['fabric']['name'] ?? '-' }}</span>
                            <span>•</span>
                            <span><strong>Tipo:</strong> {{ $group['cut_type']['name'] ?? '-' }}</span>
                            @php
                                // Pegar a prateleira do primeiro tamanho (todos têm a mesma)
                                $shelf = null;
                                foreach($sizes as $size) {
                                    if(isset($group['sizes'][$size]['shelf']) && $group['sizes'][$size]['shelf']) {
                                        $shelf = $group['sizes'][$size]['shelf'];
                                        break;
                                    }
                                }
                            @endphp
                            @if($shelf)
                            <span>•</span>
                            <span><strong>Prateleira:</strong> {{ $shelf }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold">{{ $group['total_available'] }}</div>
                    <div class="text-sm text-indigo-100">Total Disponível</div>
                    @if($group['total_reserved'] > 0)
                    <div class="text-xs text-orange-200 mt-1">
                        {{ $group['total_reserved'] }} reservado(s)
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Tabela de Estoque por Cor e Tamanho -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-2 py-2 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider bg-gray-100 dark:bg-gray-800">
                            COR
                        </th>
                        <th class="px-1 py-2 text-center text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">EST.</th>
                        @foreach($sizes as $size)
                        <th class="px-1 py-2 text-center text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">{{ $size }}</th>
                        @endforeach
                        <th class="px-2 py-2 text-center text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider bg-indigo-50 dark:bg-indigo-900/20">TOTAL</th>
                        <th class="px-2 py-2 text-center text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">PRATELEIRA</th>
                        <th class="px-2 py-2 text-center text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">AÇÕES</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        <td class="px-2 py-2 whitespace-nowrap">
                            <div class="text-xs font-bold text-gray-900 dark:text-gray-100">
                                {{ $group['color']['name'] ?? '-' }}
                            </div>
                        </td>
                        <td class="px-1 py-2 whitespace-nowrap text-center">
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($group['last_updated'])->format('d/m') }}
                            </span>
                        </td>
                        @foreach($sizes as $size)
                        <td class="px-1 py-2 whitespace-nowrap text-center">
                            @if(isset($group['sizes'][$size]))
                            @php
                                $sizeData = $group['sizes'][$size];
                                $isLowStock = $sizeData['available_quantity'] < ($sizeData['min_stock'] ?? 0);
                                $hasStock = $sizeData['available_quantity'] > 0;
                            @endphp
                            <div class="flex flex-col items-center">
                                <span class="text-xs font-bold {{ $hasStock ? 'text-green-700 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} {{ $isLowStock && $hasStock ? 'bg-yellow-100 dark:bg-yellow-900/40 px-1 py-0.5 rounded' : '' }}">
                                    {{ $sizeData['available_quantity'] }}
                                </span>
                                @if($sizeData['reserved_quantity'] > 0)
                                <span class="text-xs text-orange-600 dark:text-orange-400">
                                    ({{ $sizeData['reserved_quantity'] }})
                                </span>
                                @endif
                            </div>
                            @else
                            <span class="text-xs text-gray-300 dark:text-gray-600">-</span>
                            @endif
                        </td>
                        @endforeach
                        <td class="px-2 py-2 whitespace-nowrap text-center bg-indigo-50 dark:bg-indigo-900/20">
                            <div class="flex flex-col items-center">
                                <span class="text-sm font-bold text-indigo-700 dark:text-indigo-300">
                                    {{ $group['total_available'] }}
                                </span>
                                @if($group['total_reserved'] > 0)
                                <span class="text-xs text-orange-600 dark:text-orange-400">
                                    ({{ $group['total_reserved'] }})
                                </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-2 py-2 whitespace-nowrap text-center">
                            @php
                                // Pegar a prateleira e o ID do primeiro tamanho com estoque (todos têm a mesma prateleira)
                                $shelf = null;
                                $firstStockId = null;
                                foreach($sizes as $size) {
                                    if(isset($group['sizes'][$size])) {
                                        // Pegar prateleira se ainda não tiver
                                        if(!$shelf && isset($group['sizes'][$size]['shelf']) && $group['sizes'][$size]['shelf']) {
                                            $shelf = $group['sizes'][$size]['shelf'];
                                        }
                                        // Pegar ID do primeiro estoque disponível
                                        if(!$firstStockId && isset($group['sizes'][$size]['id'])) {
                                            $firstStockId = $group['sizes'][$size]['id'];
                                        }
                                        // Se já tem ambos, pode parar
                                        if($shelf && $firstStockId) {
                                            break;
                                        }
                                    }
                                }
                            @endphp
                            @if($shelf)
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-700">
                                {{ $shelf }}
                            </span>
                            @else
                            <span class="text-xs text-gray-400 dark:text-gray-500">-</span>
                            @endif
                        </td>
                        <td class="px-2 py-2 whitespace-nowrap text-center">
                            @if($firstStockId)
                            <button onclick="editStock({{ $firstStockId }})" 
                                    class="p-1.5 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded transition"
                                    title="Editar Estoque">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            @else
                            <span class="text-xs text-gray-400 dark:text-gray-500">-</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @empty
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-12 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Nenhum estoque encontrado</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Comece cadastrando um novo estoque.</p>
    </div>
    @endforelse
</div>

<!-- Modal de Edição de Estoque -->
<div id="edit-stock-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 max-w-2xl w-full mx-4 my-8">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Editar Estoque</h3>
            <button onclick="closeEditStockModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form id="edit-stock-form" onsubmit="updateStockGroup(event)">
            <input type="hidden" id="edit-store-id" name="store_id">
            <input type="hidden" id="edit-fabric-id" name="fabric_id">
            <input type="hidden" id="edit-color-id" name="color_id">
            <input type="hidden" id="edit-cut-type-id" name="cut_type_id">
            
            <!-- Quantidades por Tamanho -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                    Quantidades por Tamanho <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-3 md:grid-cols-5 gap-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                    @foreach($sizes as $size)
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1 text-center">
                            {{ $size }}
                        </label>
                        <input type="number" 
                               id="edit-size-{{ $size }}"
                               name="sizes[{{ $size }}]" 
                               value="0"
                               min="0"
                               step="1"
                               placeholder="0"
                               class="w-full px-2 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-center focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    @endforeach
                </div>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    Informe a quantidade para cada tamanho. Deixe em 0 para remover o tamanho do estoque.
                </p>
            </div>
            
            <!-- Prateleira/Estante -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Prateleira/Estante
                </label>
                <input type="text" 
                       id="edit-stock-shelf" 
                       name="shelf" 
                       placeholder="Ex: A1, B5, C3"
                       maxlength="50"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Informe a prateleira/estante onde todos os tamanhos estão armazenados.
                </p>
            </div>
            
            <!-- Configurações de Estoque -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Estoque Mínimo
                    </label>
                    <input type="number" 
                           id="edit-stock-min" 
                           name="min_stock" 
                           value="0"
                           min="0"
                           step="0.01"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Aplicado a todos os tamanhos</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Estoque Máximo
                    </label>
                    <input type="number" 
                           id="edit-stock-max" 
                           name="max_stock" 
                           min="0"
                           step="0.01"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Aplicado a todos os tamanhos</p>
                </div>
            </div>
            
            <!-- Observações -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Observações
                </label>
                <textarea id="edit-stock-notes" 
                          name="notes" 
                          rows="3"
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            </div>
            
            <div class="flex gap-3">
                <button type="button" onclick="closeEditStockModal()" 
                        class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Cancelar
                </button>
                <button type="submit" 
                        class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Salvar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Solicitar Transferência -->
<div id="request-transfer-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 max-w-lg w-full mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Solicitar Transferência</h3>
            <button onclick="closeRequestTransferModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form id="request-transfer-form" onsubmit="submitRequestTransfer(event)">
            <input type="hidden" id="transfer-store-id" name="requesting_store_id">
            <input type="hidden" id="transfer-fabric-id" name="fabric_id">
            <input type="hidden" id="transfer-color-id" name="color_id">
            <input type="hidden" id="transfer-cut-type-id" name="cut_type_id">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Loja de Destino:</label>
                <select id="transfer-target-store" name="target_store_id" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">Selecione a loja</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}" data-store-id="{{ $store->id }}">{{ $store->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tamanho:</label>
                <select id="transfer-size" name="size" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">Selecione o tamanho</option>
                    @foreach($sizes as $size)
                        <option value="{{ $size }}">{{ $size }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quantidade:</label>
                <input type="number" 
                       id="transfer-quantity" 
                       name="requested_quantity" 
                       min="1"
                       required
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Observações:</label>
                <textarea id="transfer-notes" 
                          name="request_notes" 
                          rows="3"
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"></textarea>
            </div>
            
            <div class="flex gap-3">
                <button type="button" onclick="closeRequestTransferModal()" 
                        class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Cancelar
                </button>
                <button type="submit" 
                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Solicitar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Solicitar Acréscimo -->
<div id="request-increment-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 max-w-lg w-full mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Solicitar Acréscimo</h3>
            <button onclick="closeRequestIncrementModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form id="request-increment-form" onsubmit="submitRequestIncrement(event)">
            <input type="hidden" id="increment-store-id" name="requesting_store_id">
            <input type="hidden" id="increment-fabric-id" name="fabric_id">
            <input type="hidden" id="increment-color-id" name="color_id">
            <input type="hidden" id="increment-cut-type-id" name="cut_type_id">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tamanho:</label>
                <select id="increment-size" name="size" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">Selecione o tamanho</option>
                    @foreach($sizes as $size)
                        <option value="{{ $size }}">{{ $size }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quantidade a Adicionar:</label>
                <input type="number" 
                       id="increment-quantity" 
                       name="requested_quantity" 
                       min="1"
                       required
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Observações:</label>
                <textarea id="increment-notes" 
                          name="request_notes" 
                          rows="3"
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"></textarea>
            </div>
            
            <div class="flex gap-3">
                <button type="button" onclick="closeRequestIncrementModal()" 
                        class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Cancelar
                </button>
                <button type="submit" 
                        class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Solicitar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Solicitar Retirada -->
<div id="request-decrement-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 max-w-lg w-full mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Solicitar Retirada</h3>
            <button onclick="closeRequestDecrementModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form id="request-decrement-form" onsubmit="submitRequestDecrement(event)">
            <input type="hidden" id="decrement-store-id" name="requesting_store_id">
            <input type="hidden" id="decrement-fabric-id" name="fabric_id">
            <input type="hidden" id="decrement-color-id" name="color_id">
            <input type="hidden" id="decrement-cut-type-id" name="cut_type_id">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tamanho:</label>
                <select id="decrement-size" name="size" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">Selecione o tamanho</option>
                    @foreach($sizes as $size)
                        <option value="{{ $size }}">{{ $size }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quantidade a Retirar:</label>
                <input type="number" 
                       id="decrement-quantity" 
                       name="requested_quantity" 
                       min="1"
                       required
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Observações:</label>
                <textarea id="decrement-notes" 
                          name="request_notes" 
                          rows="3"
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"></textarea>
            </div>
            
            <div class="flex gap-3">
                <button type="button" onclick="closeRequestDecrementModal()" 
                        class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Cancelar
                </button>
                <button type="submit" 
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Solicitar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Confirmação -->
<div id="confirm-edit-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 max-w-md w-full">
        <div class="flex items-center mb-4">
            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900/30">
                <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="ml-3 text-lg font-semibold text-gray-900 dark:text-gray-100">Confirmar Edição</h3>
        </div>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
            Tem certeza que deseja salvar as alterações no estoque? Esta ação irá atualizar todos os tamanhos do grupo.
        </p>
        <div class="flex gap-3">
            <button type="button" onclick="closeConfirmEditModal()" 
                    class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                Cancelar
            </button>
            <button type="button" onclick="confirmUpdateStockGroup()" 
                    class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                Confirmar
            </button>
        </div>
    </div>
</div>

<!-- Modal de Mensagem (Sucesso/Erro) -->
<div id="message-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 max-w-md w-full">
        <div class="flex items-center mb-4">
            <div id="message-icon">
                <!-- Ícone será inserido via JavaScript -->
            </div>
            <h3 id="message-title" class="ml-3 text-lg font-semibold text-gray-900 dark:text-gray-100"></h3>
        </div>
        <p id="message-text" class="text-sm text-gray-600 dark:text-gray-400 mb-6"></p>
        <div class="flex justify-end">
            <button type="button" onclick="closeMessageModal()" 
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                OK
            </button>
        </div>
    </div>
</div>

<script>
    (function() {
        // Evitar redeclaração ao carregar via AJAX
        if (typeof window.groupedStocksData !== 'undefined') {
            return; // Já foi declarado, não redeclarar
        }
        
        window.groupedStocksData = @json($groupedStocks);
        window.sizes = @json($sizes);
    })();
    
    function editGroupStock(groupKey) {
        const group = window.groupedStocksData[groupKey];
        if (!group) {
            showMessage('error', 'Erro!', 'Grupo de estoque não encontrado');
            return;
        }
        
        // Redirecionar para a página de criação com os parâmetros pré-preenchidos
        const params = new URLSearchParams({
            store_id: group.store.id,
            fabric_id: group.fabric?.id || '',
            color_id: group.color?.id || '',
            cut_type_id: group.cut_type?.id || '',
        });
        
        window.location.href = `{{ route('stocks.create') }}?${params.toString()}`;
    }
    
    function editStock(id) {
        // Buscar dados do estoque via API para obter informações do grupo
        fetch(`/stocks/${id}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
            .then(res => {
                if (!res.ok) throw new Error('Erro ao buscar estoque');
                return res.json();
            })
            .then(data => {
                if (data.success && data.stock) {
                    const stock = data.stock;
                    
                    // Preencher dados do grupo
                    document.getElementById('edit-store-id').value = stock.store_id;
                    document.getElementById('edit-fabric-id').value = stock.fabric_id || '';
                    document.getElementById('edit-color-id').value = stock.color_id || '';
                    document.getElementById('edit-cut-type-id').value = stock.cut_type_id || '';
                    
                    // Buscar todos os estoques do grupo
                    const groupKey = `${stock.store_id}_${stock.fabric_id || 0}_${stock.cut_type_id || 0}_${stock.color_id || 0}`;
                    let group = null;
                    
                    // Buscar o grupo no array groupedStocksData
                    if (Array.isArray(window.groupedStocksData)) {
                        group = window.groupedStocksData.find(g => {
                            const key = `${g.store?.id || 0}_${g.fabric?.id || 0}_${g.cut_type?.id || 0}_${g.color?.id || 0}`;
                            return key === groupKey;
                        });
                    } else {
                        group = window.groupedStocksData[groupKey];
                    }
                    
                    if (group && group.sizes) {
                        // Preencher quantidades de todos os tamanhos
                        window.sizes.forEach(size => {
                            const sizeInput = document.getElementById(`edit-size-${size}`);
                            if (sizeInput && group.sizes[size]) {
                                sizeInput.value = group.sizes[size].quantity || 0;
                            } else if (sizeInput) {
                                sizeInput.value = 0;
                            }
                        });
                        
                        // Pegar prateleira, min_stock, max_stock e notes do primeiro tamanho disponível
                        let shelf = null;
                        let minStock = 0;
                        let maxStock = null;
                        let notes = null;
                        
                        for (const size of window.sizes) {
                            if (group.sizes[size]) {
                                if (!shelf && group.sizes[size].shelf) {
                                    shelf = group.sizes[size].shelf;
                                }
                                if (group.sizes[size].min_stock !== undefined && group.sizes[size].min_stock !== null) {
                                    minStock = group.sizes[size].min_stock;
                                }
                                if (!maxStock && group.sizes[size].max_stock) {
                                    maxStock = group.sizes[size].max_stock;
                                }
                                if (!notes && stock.notes) {
                                    notes = stock.notes;
                                }
                                break;
                            }
                        }
                        
                        document.getElementById('edit-stock-shelf').value = shelf || '';
                        document.getElementById('edit-stock-min').value = minStock || 0;
                        document.getElementById('edit-stock-max').value = maxStock || '';
                        document.getElementById('edit-stock-notes').value = notes || '';
                    } else {
                        // Se não encontrou o grupo, usar dados do estoque individual
                        document.getElementById(`edit-size-${stock.size}`).value = stock.quantity || 0;
                        document.getElementById('edit-stock-shelf').value = stock.shelf || '';
                        document.getElementById('edit-stock-min').value = stock.min_stock || 0;
                        document.getElementById('edit-stock-max').value = stock.max_stock || '';
                        document.getElementById('edit-stock-notes').value = stock.notes || '';
                    }
                    
                    document.getElementById('edit-stock-modal').classList.remove('hidden');
                } else {
                    showMessage('error', 'Erro!', 'Erro ao carregar dados do estoque');
                }
            })
            .catch(error => {
                console.error('Erro ao buscar estoque:', error);
                showMessage('error', 'Erro!', 'Erro ao carregar dados do estoque. Por favor, tente novamente.');
            });
    }
    
    function closeEditStockModal() {
        document.getElementById('edit-stock-modal').classList.add('hidden');
        // Limpar formulário
        document.getElementById('edit-stock-form').reset();
    }
    
    let pendingUpdateData = null;
    
    function updateStockGroup(event) {
        event.preventDefault();
        
        const formData = new FormData(event.target);
        
        // Processar fabric_id, color_id, cut_type_id - converter strings vazias para null
        let fabricId = formData.get('fabric_id');
        if (!fabricId || fabricId === '' || fabricId === 'null' || fabricId === '0') {
            fabricId = null;
        } else {
            fabricId = parseInt(fabricId);
            if (isNaN(fabricId)) fabricId = null;
        }
        
        let colorId = formData.get('color_id');
        if (!colorId || colorId === '' || colorId === 'null' || colorId === '0') {
            colorId = null;
        } else {
            colorId = parseInt(colorId);
            if (isNaN(colorId)) colorId = null;
        }
        
        let cutTypeId = formData.get('cut_type_id');
        if (!cutTypeId || cutTypeId === '' || cutTypeId === 'null' || cutTypeId === '0') {
            cutTypeId = null;
        } else {
            cutTypeId = parseInt(cutTypeId);
            if (isNaN(cutTypeId)) cutTypeId = null;
        }
        
        // Processar store_id
        const storeIdInput = formData.get('store_id');
        if (!storeIdInput) {
            showMessage('error', 'Erro!', 'ID da loja não encontrado no formulário. Por favor, tente novamente.');
            return;
        }
        
        const storeId = parseInt(storeIdInput);
        if (isNaN(storeId) || storeId <= 0) {
            showMessage('error', 'Erro!', 'ID da loja inválido: ' + storeIdInput);
            return;
        }
        
        // Criar objeto de dados
        const data = {
            store_id: storeId,
            fabric_id: fabricId,
            color_id: colorId,
            cut_type_id: cutTypeId,
            sizes: {},
            shelf: formData.get('shelf') ? formData.get('shelf').trim() : null,
            min_stock: formData.get('min_stock') ? parseFloat(formData.get('min_stock')) : 0,
            max_stock: formData.get('max_stock') && formData.get('max_stock') !== '' ? parseFloat(formData.get('max_stock')) : null,
            notes: formData.get('notes') && formData.get('notes').trim() !== '' ? formData.get('notes').trim() : null,
        };
        
        // Log para debug
        console.log('Dados coletados do formulário (antes de coletar sizes):', data);
        
        // Coletar quantidades de todos os tamanhos
        if (!window.sizes || !Array.isArray(window.sizes)) {
            console.error('window.sizes não está definido:', window.sizes);
            showMessage('error', 'Erro!', 'Erro ao processar tamanhos. Por favor, recarregue a página.');
            return;
        }
        
        window.sizes.forEach(size => {
            const sizeInput = document.getElementById(`edit-size-${size}`);
            if (sizeInput) {
                const quantity = parseInt(sizeInput.value) || 0;
                data.sizes[size] = quantity;
            } else {
                // Se o input não existir, definir como 0
                data.sizes[size] = 0;
            }
        });
        
        // Validar que pelo menos um tamanho tem quantidade > 0
        const hasQuantities = Object.values(data.sizes).some(qty => qty > 0);
        if (!hasQuantities) {
            showMessage('error', 'Atenção!', 'Informe pelo menos uma quantidade para algum tamanho.');
            return;
        }
        
        // Validar que store_id está presente
        if (!data.store_id || isNaN(data.store_id)) {
            showMessage('error', 'Erro!', 'ID da loja inválido. Por favor, tente novamente.');
            return;
        }
        
        // Garantir que sizes é um objeto válido
        if (!data.sizes || typeof data.sizes !== 'object') {
            showMessage('error', 'Erro!', 'Erro ao processar tamanhos. Por favor, tente novamente.');
            return;
        }
        
        // Log dos dados antes de salvar
        console.log('Dados coletados do formulário:', data);
        console.log('Store ID:', data.store_id);
        console.log('Sizes:', data.sizes);
        console.log('Sizes keys:', Object.keys(data.sizes));
        console.log('Sizes values:', Object.values(data.sizes));
        
        // Validação final antes de salvar
        if (!data.store_id || isNaN(data.store_id)) {
            console.error('Store ID inválido:', data.store_id);
            showMessage('error', 'Erro!', 'ID da loja inválido. Por favor, tente novamente.');
            return;
        }
        
        if (!data.sizes || typeof data.sizes !== 'object' || Object.keys(data.sizes).length === 0) {
            console.error('Sizes inválido:', data.sizes);
            showMessage('error', 'Erro!', 'Nenhum tamanho encontrado. Por favor, tente novamente.');
            return;
        }
        
        // Salvar dados para confirmação
        pendingUpdateData = JSON.parse(JSON.stringify(data)); // Deep copy para evitar referências
        
        // Verificar se os dados foram salvos corretamente
        console.log('Dados salvos em pendingUpdateData:', pendingUpdateData);
        console.log('Store ID em pendingUpdateData:', pendingUpdateData.store_id);
        console.log('Sizes em pendingUpdateData:', pendingUpdateData.sizes);
        
        // Mostrar modal de confirmação
        document.getElementById('confirm-edit-modal').classList.remove('hidden');
    }
    
    function closeConfirmEditModal() {
        document.getElementById('confirm-edit-modal').classList.add('hidden');
        // NÃO limpar pendingUpdateData aqui, pois ainda será usado na requisição
    }
    
    function confirmUpdateStockGroup() {
        // Salvar uma cópia profunda dos dados ANTES de qualquer coisa
        // Isso garante que mesmo se pendingUpdateData for alterado, teremos os dados originais
        let dataToSend = null;
        
        if (pendingUpdateData) {
            try {
                dataToSend = JSON.parse(JSON.stringify(pendingUpdateData));
            } catch (e) {
                console.error('Erro ao copiar dados:', e);
                dataToSend = pendingUpdateData;
            }
        }
        
        if (!dataToSend) {
            console.error('pendingUpdateData está null ou undefined:', pendingUpdateData);
            showMessage('error', 'Erro!', 'Dados não encontrados. Por favor, preencha o formulário novamente.');
            return;
        }
        
        // Validar que os dados obrigatórios estão presentes
        if (!dataToSend.store_id) {
            console.error('store_id não encontrado em dataToSend:', dataToSend);
            showMessage('error', 'Erro!', 'ID da loja não encontrado. Por favor, tente novamente.');
            return;
        }
        
        if (!dataToSend.sizes || typeof dataToSend.sizes !== 'object' || Object.keys(dataToSend.sizes).length === 0) {
            console.error('sizes não encontrado ou inválido em dataToSend:', dataToSend);
            showMessage('error', 'Erro!', 'Nenhum tamanho informado. Por favor, preencha pelo menos um tamanho.');
            return;
        }
        
        // Fechar modal de confirmação (mas manter pendingUpdateData para possível retry)
        document.getElementById('confirm-edit-modal').classList.add('hidden');
        
        // Log dos dados antes de enviar
        console.log('Dados a serem enviados:', dataToSend);
        console.log('Store ID:', dataToSend.store_id);
        console.log('Sizes:', dataToSend.sizes);
        console.log('Sizes keys:', Object.keys(dataToSend.sizes));
        console.log('Sizes values:', Object.values(dataToSend.sizes));
        
        // Enviar requisição
        fetch('{{ route("stocks.update-group") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(dataToSend)
        })
        .then(async res => {
            const contentType = res.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await res.text();
                console.error('Resposta não é JSON:', text.substring(0, 500));
                throw new Error('Resposta do servidor não é JSON. Status: ' + res.status);
            }
            
            const data = await res.json();
            
            if (!res.ok) {
                // Se for erro de validação (422), mostrar erros específicos
                if (res.status === 422 && data.errors) {
                    const errorMessages = Object.values(data.errors).flat().join(', ');
                    throw new Error('Erro de validação: ' + errorMessages);
                }
                throw new Error(data.message || 'Erro desconhecido');
            }
            
            return data;
        })
        .then(data => {
            // Limpar dados apenas após sucesso
            pendingUpdateData = null;
            
            if (data.success) {
                showMessage('success', 'Sucesso!', 'Estoque atualizado com sucesso!');
                closeEditStockModal();
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showMessage('error', 'Erro!', 'Erro ao atualizar estoque: ' + (data.message || 'Erro desconhecido'));
            }
        })
        .catch(error => {
            console.error('Erro ao atualizar estoque:', error);
            showMessage('error', 'Erro!', error.message || 'Erro ao atualizar estoque. Verifique o console para mais detalhes.');
            // Não limpar pendingUpdateData em caso de erro, para permitir nova tentativa
        });
    }
    
    function showMessage(type, title, message) {
        const modal = document.getElementById('message-modal');
        const iconDiv = document.getElementById('message-icon');
        const titleEl = document.getElementById('message-title');
        const textEl = document.getElementById('message-text');
        
        // Limpar conteúdo anterior
        iconDiv.innerHTML = '';
        
        if (type === 'success') {
            iconDiv.innerHTML = `
                <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            `;
            titleEl.className = 'ml-3 text-lg font-semibold text-green-900 dark:text-green-100';
        } else {
            iconDiv.innerHTML = `
                <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
            `;
            titleEl.className = 'ml-3 text-lg font-semibold text-red-900 dark:text-red-100';
        }
        
        titleEl.textContent = title;
        textEl.textContent = message;
        modal.classList.remove('hidden');
    }
    
    function closeMessageModal() {
        document.getElementById('message-modal').classList.add('hidden');
    }
    
    // Funções para modais de solicitação
    function openRequestTransferModal(storeId, fabricId, colorId, cutTypeId) {
        document.getElementById('transfer-store-id').value = storeId;
        document.getElementById('transfer-fabric-id').value = fabricId || '';
        document.getElementById('transfer-color-id').value = colorId || '';
        document.getElementById('transfer-cut-type-id').value = cutTypeId || '';
        
        // Filtrar loja atual do select de destino
        const targetStoreSelect = document.getElementById('transfer-target-store');
        Array.from(targetStoreSelect.options).forEach(option => {
            if (option.value == storeId) {
                option.style.display = 'none';
            } else {
                option.style.display = 'block';
            }
        });
        targetStoreSelect.value = '';
        
        document.getElementById('request-transfer-modal').classList.remove('hidden');
    }
    
    function closeRequestTransferModal() {
        document.getElementById('request-transfer-modal').classList.add('hidden');
        document.getElementById('request-transfer-form').reset();
        
        // Restaurar todas as opções do select
        const targetStoreSelect = document.getElementById('transfer-target-store');
        Array.from(targetStoreSelect.options).forEach(option => {
            option.style.display = 'block';
        });
    }
    
    function submitRequestTransfer(event) {
        event.preventDefault();
        const formData = new FormData(event.target);
        const data = Object.fromEntries(formData);
        
        fetch('{{ route("stock-requests.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showMessage('success', 'Sucesso!', 'Solicitação de transferência criada com sucesso!');
                closeRequestTransferModal();
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showMessage('error', 'Erro!', 'Erro ao criar solicitação: ' + (data.message || 'Erro desconhecido'));
            }
        })
        .catch(error => {
            console.error('Erro ao criar solicitação:', error);
            showMessage('error', 'Erro!', 'Erro ao criar solicitação de transferência. Por favor, tente novamente.');
        });
    }
    
    function openRequestIncrementModal(storeId, fabricId, colorId, cutTypeId) {
        document.getElementById('increment-store-id').value = storeId;
        document.getElementById('increment-fabric-id').value = fabricId || '';
        document.getElementById('increment-color-id').value = colorId || '';
        document.getElementById('increment-cut-type-id').value = cutTypeId || '';
        document.getElementById('request-increment-modal').classList.remove('hidden');
    }
    
    function closeRequestIncrementModal() {
        document.getElementById('request-increment-modal').classList.add('hidden');
        document.getElementById('request-increment-form').reset();
    }
    
    function submitRequestIncrement(event) {
        event.preventDefault();
        const formData = new FormData(event.target);
        const data = Object.fromEntries(formData);
        // Para acréscimo, não precisa de target_store_id
        data.target_store_id = null;
        
        fetch('{{ route("stock-requests.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showMessage('success', 'Sucesso!', 'Solicitação de acréscimo criada com sucesso!');
                closeRequestIncrementModal();
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showMessage('error', 'Erro!', 'Erro ao criar solicitação: ' + (data.message || 'Erro desconhecido'));
            }
        })
        .catch(error => {
            console.error('Erro ao criar solicitação:', error);
            showMessage('error', 'Erro!', 'Erro ao criar solicitação de acréscimo. Por favor, tente novamente.');
        });
    }
    
    function openRequestDecrementModal(storeId, fabricId, colorId, cutTypeId) {
        document.getElementById('decrement-store-id').value = storeId;
        document.getElementById('decrement-fabric-id').value = fabricId || '';
        document.getElementById('decrement-color-id').value = colorId || '';
        document.getElementById('decrement-cut-type-id').value = cutTypeId || '';
        document.getElementById('request-decrement-modal').classList.remove('hidden');
    }
    
    function closeRequestDecrementModal() {
        document.getElementById('request-decrement-modal').classList.add('hidden');
        document.getElementById('request-decrement-form').reset();
    }
    
    function submitRequestDecrement(event) {
        event.preventDefault();
        const formData = new FormData(event.target);
        const data = Object.fromEntries(formData);
        // Para retirada, não precisa de target_store_id
        data.target_store_id = null;
        
        fetch('{{ route("stock-requests.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showMessage('success', 'Sucesso!', 'Solicitação de retirada criada com sucesso!');
                closeRequestDecrementModal();
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showMessage('error', 'Erro!', 'Erro ao criar solicitação: ' + (data.message || 'Erro desconhecido'));
            }
        })
        .catch(error => {
            console.error('Erro ao criar solicitação:', error);
            showMessage('error', 'Erro!', 'Erro ao criar solicitação de retirada. Por favor, tente novamente.');
        });
    }
    
    // Função stub para evitar erros se algum código tentar chamar openAddProductModal
    // Esta função é do PDV e não deve ser usada na página de estoque
    function openAddProductModal() {
        console.warn('openAddProductModal não está disponível na página de estoque. Use a página do PDV para adicionar produtos.');
        console.trace('Stack trace do erro:');
        return false;
    }
    
    // Garantir que as funções estão disponíveis globalmente
    window.openAddProductModal = openAddProductModal;
    window.openRequestTransferModal = openRequestTransferModal;
    window.openRequestIncrementModal = openRequestIncrementModal;
    window.openRequestDecrementModal = openRequestDecrementModal;
    window.showMessage = showMessage;
    window.closeMessageModal = closeMessageModal;
</script>
@endsection

