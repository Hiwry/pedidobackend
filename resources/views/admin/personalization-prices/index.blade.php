<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Preços de Personalização - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .grid-3-cols {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        @media (min-width: 1024px) {
            .grid-3-cols {
                grid-template-columns: repeat(3, 1fr);
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    <x-app-header />

    <div class="max-w-7xl mx-auto p-6 w-full">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Preços de Personalização</h1>
            <p class="text-gray-600 mt-2">Gerencie os preços para todos os tipos de personalização</p>
        </div>

        @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
        @endif

        <div class="grid-3-cols">
            @foreach($pricesByType as $typeKey => $typeData)
            <div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition-shadow duration-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-purple-50">
                    <div class="flex items-center">
                        @if($typeKey === 'DTF')
                            <i class="fas fa-print text-indigo-600 text-xl mr-3"></i>
                        @elseif($typeKey === 'SERIGRAFIA')
                            <i class="fas fa-paint-brush text-blue-600 text-xl mr-3"></i>
                        @elseif($typeKey === 'BORDADO')
                            <i class="fas fa-sewing text-green-600 text-xl mr-3"></i>
                        @elseif($typeKey === 'SUBLIMACAO')
                            <i class="fas fa-thermometer-half text-purple-600 text-xl mr-3"></i>
                        @elseif($typeKey === 'EMBORRACHADO')
                            <i class="fas fa-circle text-orange-600 text-xl mr-3"></i>
                        @elseif($typeKey === 'SUBLIMACAO_TOTAL')
                            <i class="fas fa-tshirt text-pink-600 text-xl mr-3"></i>
                        @else
                            <i class="fas fa-tags text-gray-600 text-xl mr-3"></i>
                        @endif
                        <h2 class="text-lg font-semibold text-gray-900">{{ $typeData['label'] }}</h2>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Tamanhos:</span>
                            <span class="font-medium">{{ $typeData['sizes']->count() }}</span>
                        </div>
                        
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Faixas de Preço:</span>
                            <span class="font-medium">{{ $typeData['total_ranges'] }}</span>
                        </div>
                        
                        @if($typeData['sizes']->count() > 0)
                        <div class="mt-4">
                            <p class="text-xs text-gray-500 mb-2">Tamanhos disponíveis:</p>
                            <div class="space-y-1">
                                @foreach($typeData['sizes']->take(3) as $size)
                                <div class="text-xs text-gray-600">
                                    {{ $size->size_name }}
                                    @if($size->size_dimensions)
                                        <span class="text-gray-400">({{ $size->size_dimensions }})</span>
                                    @endif
                                </div>
                                @endforeach
                                @if($typeData['sizes']->count() > 3)
                                <div class="text-xs text-gray-400">
                                    +{{ $typeData['sizes']->count() - 3 }} mais
                                </div>
                                @endif
                            </div>
                        @else
                        <div class="text-center py-4">
                            <p class="text-sm text-gray-500">Nenhum preço configurado</p>
                        </div>
                        @endif
                    </div>
                    
                    <div class="mt-6">
                        <a href="{{ route('admin.personalization-prices.edit', $typeKey) }}" 
                           class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-md text-center block transition-colors">
                            ✏️ Gerenciar Preços
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Como funciona</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li>Configure preços por tipo de personalização (DTF, Serigrafia, Bordado, Sublimação)</li>
                            <li>Defina faixas de quantidade para cada tamanho</li>
                            <li>Os preços se aplicam automaticamente aos novos pedidos</li>
                            <li>Pedidos existentes mantêm os preços originais</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
