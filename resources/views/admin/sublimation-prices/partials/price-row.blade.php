<div class="price-row bg-gray-50 border border-gray-200 rounded-lg p-4">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">De (quantidade) *</label>
            <input type="number" 
                   name="prices[{{ $index }}][quantity_from]" 
                   value="{{ $price ? $price->quantity_from : '' }}"
                   min="1" 
                   required
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                   placeholder="Ex: 1">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Até (quantidade)</label>
            <input type="number" 
                   name="prices[{{ $index }}][quantity_to]" 
                   value="{{ $price ? $price->quantity_to : '' }}"
                   min="1"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                   placeholder="Ex: 10 (deixe vazio para infinito)">
            <p class="text-xs text-gray-500 mt-1">Deixe vazio para "infinito"</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Preço (R$) *</label>
            <input type="number" 
                   name="prices[{{ $index }}][price]" 
                   value="{{ $price ? $price->price : '' }}"
                   step="0.01" 
                   min="0" 
                   required
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                   placeholder="Ex: 15.00">
        </div>

        <div class="flex items-end">
            <button type="button" 
                    onclick="removePriceRow(this)"
                    class="w-full px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                🗑️ Remover
            </button>
        </div>
    </div>

    @if($price)
        <div class="mt-3 text-sm text-gray-600">
            <span class="font-medium">Faixa atual:</span>
            @if($price->quantity_to)
                {{ $price->quantity_from }} a {{ $price->quantity_to }} peças = R$ {{ number_format($price->price, 2, ',', '.') }}
            @else
                {{ $price->quantity_from }}+ peças = R$ {{ number_format($price->price, 2, ',', '.') }}
            @endif
        </div>
    @endif
</div>
