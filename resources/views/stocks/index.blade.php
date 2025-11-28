@extends('layouts.admin')

@section('content')
<style>
/* Estilo minimalista inspirado em planilha */
.stock-table {
    font-size: 11px;
    border-collapse: collapse;
}
.stock-table th {
    background: #f8f9fa;
    font-weight: 700;
    text-transform: uppercase;
    padding: 6px 8px;
    border: 1px solid #dee2e6;
    font-size: 10px;
}
.dark .stock-table th {
    background: #1f2937;
    border-color: #374151;
}
.stock-table td {
    padding: 4px 6px;
    border: 1px solid #e9ecef;
    text-align: center;
}
.dark .stock-table td {
    border-color: #374151;
}
.stock-cell {
    min-width: 35px;
    font-weight: 600;
}
.stock-high { background: #d4edda !important; color: #155724; }
.stock-medium { background: #fff3cd !important; color: #856404; }
.stock-low { background: #f8d7da !important; color: #721c24; }
.stock-zero { background: #f8f9fa !important; color: #6c757d; }
.dark .stock-high { background: #064e3b !important; color: #6ee7b7; }
.dark .stock-medium { background: #78350f !important; color: #fcd34d; }
.dark .stock-low { background: #7f1d1d !important; color: #fca5a5; }
.dark .stock-zero { background: #1f2937 !important; color: #6b7280; }
</style>

@if(session('success'))
<div class="mb-4 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-3 text-sm">
    {{ session('success') }}
</div>
@endif

<!-- Header Compacto -->
<div class="flex justify-between items-center mb-4">
    <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Estoque</h1>
    <div class="flex gap-2">
        <a href="{{ route('stocks.create') }}" 
           class="px-3 py-1.5 bg-green-600 text-white rounded text-xs font-semibold hover:bg-green-700 transition">
            + Novo
        </a>
        <a href="{{ route('stock-requests.index') }}" 
           class="px-3 py-1.5 bg-indigo-600 text-white rounded text-xs font-semibold hover:bg-indigo-700 transition">
            Solicitações
        </a>
    </div>
</div>

<!-- Filtros Compactos -->
<div class="bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700 p-3 mb-4">
    <form method="GET" action="{{ route('stocks.index') }}">
        <div class="grid grid-cols-6 gap-2 mb-2">
            <select name="store_id" class="text-xs px-2 py-1.5 rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                <option value="">Todas Lojas</option>
                @foreach($stores as $store)
                    <option value="{{ $store->id }}" {{ $storeId == $store->id ? 'selected' : '' }}>{{ $store->name }}</option>
                @endforeach
            </select>

            <select name="fabric_id" class="text-xs px-2 py-1.5 rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                <option value="">Todos Tecidos</option>
                @foreach($fabrics as $fabric)
                    <option value="{{ $fabric->id }}" {{ $fabricId == $fabric->id ? 'selected' : '' }}>{{ $fabric->name }}</option>
                @endforeach
            </select>

            <select name="color_id" class="text-xs px-2 py-1.5 rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                <option value="">Todas Cores</option>
                @foreach($colors as $color)
                    <option value="{{ $color->id }}" {{ $colorId == $color->id ? 'selected' : '' }}>{{ $color->name }}</option>
                @endforeach
            </select>

            <select name="cut_type_id" class="text-xs px-2 py-1.5 rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                <option value="">Todos Tipos</option>
                @foreach($cutTypes as $cutType)
                    <option value="{{ $cutType->id }}" {{ $cutTypeId == $cutType->id ? 'selected' : '' }}>{{ $cutType->name }}</option>
                @endforeach
            </select>

            <select name="size" class="text-xs px-2 py-1.5 rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                <option value="">Todos Tamanhos</option>
                @foreach($sizes as $size)
                    <option value="{{ $size }}" {{ $size == request('size') ? 'selected' : '' }}>{{ $size }}</option>
                @endforeach
            </select>

            <div class="flex gap-1">
                <button type="submit" class="flex-1 px-2 py-1.5 bg-indigo-600 text-white rounded text-xs font-semibold hover:bg-indigo-700">
                    Filtrar
                </button>
                <a href="{{ route('stocks.index') }}" class="px-2 py-1.5 bg-gray-200 dark:bg-gray-700 rounded text-xs font-semibold hover:bg-gray-300 dark:hover:bg-gray-600">
                    Limpar
                </a>
            </div>
        </div>
        <label class="flex items-center text-xs text-gray-600 dark:text-gray-400">
            <input type="checkbox" name="low_stock" value="1" {{ $lowStock ? 'checked' : '' }} class="mr-1 rounded">
            Apenas estoque baixo
        </label>
    </form>
</div>

<!-- Tabela Estilo Planilha -->
<div class="bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="overflow-x-auto">
        @forelse($groupedStocks as $key => $group)
        <div class="border-b-2 border-gray-300 dark:border-gray-600">
            <!-- Cabeçalho do Grupo -->
            <div class="bg-gray-100 dark:bg-gray-700 px-3 py-2 flex items-center justify-between text-xs font-bold">
                <div class="flex items-center gap-4">
                    <span class="text-indigo-700 dark:text-indigo-400">{{ $group['store']['name'] }}</span>
                    <span class="text-gray-600 dark:text-gray-400">{{ $group['fabric']['name'] ?? '-' }}</span>
                    <span class="text-gray-600 dark:text-gray-400">{{ $group['cut_type']['name'] ?? '-' }}</span>
                    @php
                        $shelf = null;
                        foreach($sizes as $size) {
                            if(isset($group['sizes'][$size]['shelf']) && $group['sizes'][$size]['shelf']) {
                                $shelf = $group['sizes'][$size]['shelf'];
                                break;
                            }
                        }
                    @endphp
                    @if($shelf)
                    <span class="px-2 py-0.5 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded">📍 {{ $shelf }}</span>
                    @endif
                </div>
                <span class="text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($group['last_updated'])->format('d/m/Y') }}</span>
            </div>

            <!-- Tabela de Tamanhos -->
            <table class="w-full stock-table">
                <thead>
                    <tr>
                        <th class="text-left">COR</th>
                        @foreach($sizes as $size)
                        <th>{{ $size }}</th>
                        @endforeach
                        <th class="bg-indigo-50 dark:bg-indigo-900/30">TOTAL</th>
                        <th style="min-width: 90px;">AÇÕES</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="text-left font-semibold text-gray-900 dark:text-gray-100">
                            {{ $group['color']['name'] ?? '-' }}
                        </td>
                        @foreach($sizes as $size)
                        <td class="stock-cell">
                            @if(isset($group['sizes'][$size]))
                            @php
                                $sizeData = $group['sizes'][$size];
                                $qty = $sizeData['available_quantity'];
                                $reserved = $sizeData['reserved_quantity'];
                                $minStock = $sizeData['min_stock'] ?? 5;
                                
                                if ($qty == 0) $class = 'stock-zero';
                                elseif ($qty < $minStock) $class = 'stock-low';
                                elseif ($qty < $minStock * 2) $class = 'stock-medium';
                                else $class = 'stock-high';
                            @endphp
                            <div class="{{ $class }} rounded px-1 py-0.5">
                                {{ $qty }}
                                @if($reserved > 0)
                                <span class="text-orange-600 dark:text-orange-400 text-[9px]">({{ $reserved }})</span>
                                @endif
                            </div>
                            @else
                            <span class="text-gray-300 dark:text-gray-600">-</span>
                            @endif
                        </td>
                        @endforeach
                        <td class="bg-indigo-50 dark:bg-indigo-900/30 font-bold text-indigo-700 dark:text-indigo-300">
                            {{ $group['total_available'] }}
                        </td>
                        <td class="text-center">
                            @php
                                // Buscar ID do primeiro estoque disponível
                                $firstStockId = null;
                                
                                // Percorrer os tamanhos exatamente como são exibidos
                                if(isset($group['sizes']) && is_array($group['sizes'])) {
                                    foreach($sizes as $size) {
                                        if(isset($group['sizes'][$size]) && is_array($group['sizes'][$size])) {
                                            $sizeData = $group['sizes'][$size];
                                            // Verificar se tem ID válido
                                            if(isset($sizeData['id']) && !empty($sizeData['id']) && $sizeData['id'] > 0) {
                                                $firstStockId = (int)$sizeData['id'];
                                                break;
                                            }
                                        }
                                    }
                                }
                            @endphp
                            <div class="flex gap-1 justify-center items-center">
                                @if($firstStockId && $firstStockId > 0)
                                    <a href="{{ route('stocks.edit', $firstStockId) }}" 
                                       class="w-7 h-7 flex items-center justify-center bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors" title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <button type="button" onclick="openTransferModal({{ $firstStockId }}, '{{ addslashes($group['store']['name'] ?? '') }}', '{{ addslashes($group['fabric']['name'] ?? '') }}', '{{ addslashes($group['color']['name'] ?? '') }}')" 
                                       class="w-7 h-7 flex items-center justify-center bg-green-600 text-white rounded hover:bg-green-700 transition-colors" title="Transferir">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                        </svg>
                                    </button>
                                    <form action="{{ route('stocks.destroy', $firstStockId) }}" method="POST" onsubmit="return confirm('Tem certeza?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-7 h-7 flex items-center justify-center bg-red-600 text-white rounded hover:bg-red-700 transition-colors" title="Excluir">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                @else
                                    <button type="button" disabled class="w-7 h-7 flex items-center justify-center bg-gray-400 text-white rounded opacity-50 cursor-not-allowed" title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button type="button" disabled class="w-7 h-7 flex items-center justify-center bg-gray-400 text-white rounded opacity-50 cursor-not-allowed" title="Transferir">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                        </svg>
                                    </button>
                                    <button type="button" disabled class="w-7 h-7 flex items-center justify-center bg-gray-400 text-white rounded opacity-50 cursor-not-allowed" title="Excluir">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                @endif
                                <button type="button" onclick="openRequestModal('{{ $group['store']['id'] ?? '' }}', '{{ $group['fabric']['id'] ?? '' }}', '{{ $group['color']['id'] ?? '' }}', '{{ $group['cut_type']['id'] ?? '' }}')" 
                                   class="w-7 h-7 flex items-center justify-center bg-purple-600 text-white rounded hover:bg-purple-700 transition-colors" title="Solicitar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        @empty
        <div class="p-8 text-center text-gray-500 dark:text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
            </svg>
            <p class="font-medium">Nenhum estoque encontrado</p>
            <p class="text-sm mt-1">Tente ajustar os filtros ou cadastre um novo estoque</p>
        </div>
        @endforelse
    </div>
</div>

<!-- Legenda -->
<div class="mt-4 flex items-center gap-4 text-xs text-gray-600 dark:text-gray-400">
    <span class="font-semibold">Legenda:</span>
    <div class="flex items-center gap-1">
        <div class="w-4 h-4 stock-high rounded"></div>
        <span>Alto</span>
    </div>
    <div class="flex items-center gap-1">
        <div class="w-4 h-4 stock-medium rounded"></div>
        <span>Médio</span>
    </div>
    <div class="flex items-center gap-1">
        <div class="w-4 h-4 stock-low rounded"></div>
        <span>Baixo</span>
    </div>
    <div class="flex items-center gap-1">
        <div class="w-4 h-4 stock-zero rounded"></div>
        <span>Zerado</span>
    </div>
    <span class="ml-4 text-orange-600 dark:text-orange-400">(N) = Reservado</span>
</div>

<!-- Modal de Transferência -->
<div id="transfer-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100">Transferir Estoque</h3>
        <form id="transfer-form" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">De:</label>
                <input type="text" id="transfer-from" readonly class="w-full px-3 py-2 border rounded bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">Para:</label>
                <select name="target_store_id" required class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:border-gray-600 text-gray-900 dark:text-gray-100">
                    <option value="">Selecione a loja</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">Quantidade:</label>
                <input type="number" name="quantity" min="1" required class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:border-gray-600 text-gray-900 dark:text-gray-100">
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeTransferModal()" class="flex-1 px-4 py-2 border rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
                    Cancelar
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    Transferir
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Solicitação -->
<div id="request-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100">Solicitar Estoque</h3>
        <form action="{{ route('stock-requests.store') }}" method="POST">
            @csrf
            <input type="hidden" name="requesting_store_id" id="request-store-id">
            <input type="hidden" name="fabric_id" id="request-fabric-id">
            <input type="hidden" name="color_id" id="request-color-id">
            <input type="hidden" name="cut_type_id" id="request-cut-type-id">
            
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">Solicitar de:</label>
                <select name="target_store_id" required class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:border-gray-600 text-gray-900 dark:text-gray-100">
                    <option value="">Selecione a loja</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">Tamanho:</label>
                <select name="size" required class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:border-gray-600 text-gray-900 dark:text-gray-100">
                    <option value="">Selecione</option>
                    @foreach($sizes as $size)
                        <option value="{{ $size }}">{{ $size }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">Quantidade:</label>
                <input type="number" name="requested_quantity" min="1" required class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:border-gray-600 text-gray-900 dark:text-gray-100">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">Observações:</label>
                <textarea name="request_notes" rows="3" class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:border-gray-600 text-gray-900 dark:text-gray-100"></textarea>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeRequestModal()" class="flex-1 px-4 py-2 border rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
                    Cancelar
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
                    Solicitar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openTransferModal(stockId, storeName, fabric, color) {
    document.getElementById('transfer-from').value = `${storeName} - ${fabric} - ${color}`;
    document.getElementById('transfer-form').action = `/stocks/${stockId}/transfer`;
    document.getElementById('transfer-modal').classList.remove('hidden');
}

function closeTransferModal() {
    document.getElementById('transfer-modal').classList.add('hidden');
}

function openRequestModal(storeId, fabricId, colorId, cutTypeId) {
    document.getElementById('request-store-id').value = storeId;
    document.getElementById('request-fabric-id').value = fabricId;
    document.getElementById('request-color-id').value = colorId;
    document.getElementById('request-cut-type-id').value = cutTypeId;
    document.getElementById('request-modal').classList.remove('hidden');
}

function closeRequestModal() {
    document.getElementById('request-modal').classList.add('hidden');
}

// Fechar modal ao clicar fora
document.getElementById('transfer-modal')?.addEventListener('click', function(e) {
    if (e.target === this) closeTransferModal();
});

document.getElementById('request-modal')?.addEventListener('click', function(e) {
    if (e.target === this) closeRequestModal();
});
</script>
@endsection

