<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Preços - {{ $size->name }} - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <x-app-header />

    <div class="max-w-4xl mx-auto p-6">
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Editar Preços</h1>
                    <p class="text-gray-600 mt-2">{{ $size->name }} ({{ $size->dimensions }})</p>
                </div>
                <a href="{{ route('admin.sublimation-prices.index') }}" 
                   class="px-4 py-2 text-gray-600 hover:text-gray-900 border border-gray-300 rounded-md">
                    ← Voltar
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.sublimation-prices.update', $size) }}" id="prices-form">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold">Faixas de Preço por Quantidade</h2>
                    <p class="text-sm text-gray-600 mt-1">Configure as faixas de quantidade e seus respectivos preços</p>
                </div>

                <div class="p-6">
                    <div id="prices-container" class="space-y-4">
                        @if($prices->count() > 0)
                            @foreach($prices as $index => $price)
                                @include('admin.sublimation-prices.partials.price-row', ['index' => $index, 'price' => $price])
                            @endforeach
                        @else
                            @include('admin.sublimation-prices.partials.price-row', ['index' => 0, 'price' => null])
                        @endif
                    </div>

                    <div class="mt-6 flex justify-between">
                        <button type="button" onclick="addPriceRow()" 
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                            + Adicionar Faixa
                        </button>
                        
                        <div class="flex space-x-3">
                            <a href="{{ route('admin.sublimation-prices.index') }}" 
                               class="px-4 py-2 text-gray-600 hover:text-gray-900 border border-gray-300 rounded-md">
                                Cancelar
                            </a>
                            <button type="submit" 
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                Salvar Preços
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Importante</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li>As faixas devem ser sequenciais e não se sobrepor</li>
                            <li>Deixe "Até" vazio para indicar "infinito" (última faixa)</li>
                            <li>As alterações se aplicam imediatamente a novos pedidos</li>
                            <li>Use o botão "Testar Preços" para verificar se está funcionando</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let priceRowIndex = {{ $prices->count() }};

        function addPriceRow() {
            fetch(`{{ route('admin.sublimation-prices.add-row') }}?index=${priceRowIndex}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('prices-container').insertAdjacentHTML('beforeend', html);
                    priceRowIndex++;
                })
                .catch(error => {
                    console.error('Erro ao adicionar linha:', error);
                    alert('Erro ao adicionar linha. Tente novamente.');
                });
        }

        function removePriceRow(button) {
            const row = button.closest('.price-row');
            row.remove();
        }

        function validatePrices() {
            const rows = document.querySelectorAll('.price-row');
            const quantities = [];
            
            for (let row of rows) {
                const from = parseInt(row.querySelector('input[name*="[quantity_from]"]').value);
                const to = row.querySelector('input[name*="[quantity_to]"]').value;
                const toValue = to ? parseInt(to) : null;
                
                if (from && from > 0) {
                    quantities.push({ from, to: toValue, row });
                }
            }
            
            // Ordenar por quantidade inicial
            quantities.sort((a, b) => a.from - b.from);
            
            // Verificar sobreposições
            for (let i = 0; i < quantities.length - 1; i++) {
                const current = quantities[i];
                const next = quantities[i + 1];
                
                if (current.to && current.to >= next.from) {
                    alert(`Sobreposição detectada: Faixa ${current.from}-${current.to} se sobrepõe com ${next.from}-${next.to || '∞'}`);
                    return false;
                }
            }
            
            return true;
        }

        document.getElementById('prices-form').addEventListener('submit', function(e) {
            if (!validatePrices()) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>
