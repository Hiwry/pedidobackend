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
                        <th>AÇÕES</th>
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
                        <td>
                            @php
                                $firstStockId = null;
                                foreach($sizes as $size) {
                                    if(isset($group['sizes'][$size]['id'])) {
                                        $firstStockId = $group['sizes'][$size]['id'];
                                        break;
                                    }
                                }
                            @endphp
                            @if($firstStockId)
                            <div class="flex gap-1 justify-center">
                                <a href="{{ route('stocks.edit', $firstStockId) }}" 
                                   class="text-blue-600 hover:text-blue-800 dark:text-blue-400" title="Editar">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('stocks.destroy', $firstStockId) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400" title="Excluir">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                            @endif
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
@endsection

