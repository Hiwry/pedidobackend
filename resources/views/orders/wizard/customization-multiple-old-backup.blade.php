<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Novo Pedido - Personalização Múltipla</title>
    <!-- TODO: Em produção, substituir pelo Tailwind compilado via PostCSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <x-app-header />

    <div class="max-w-7xl mx-auto p-6">
        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-indigo-600">Etapa 3 de 5</span>
                <span class="text-sm text-gray-500">Personalização</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-indigo-600 h-2 rounded-full" style="width: 60%"></div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-semibold mb-6">Personalização dos Itens</h1>

            @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                <span class="block sm:inline">{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="absolute top-0 right-0 px-4 py-3">
                    <span class="text-2xl">&times;</span>
                </button>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                <span class="block sm:inline">{{ session('error') }}</span>
                <button onclick="this.parentElement.remove()" class="absolute top-0 right-0 px-4 py-3">
                    <span class="text-2xl">&times;</span>
                </button>
            </div>
            @endif

            @if($errors->any())
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <strong>Atenção!</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Informações Gerais -->
            <div class="bg-blue-50 rounded-lg p-4 mb-6 border border-blue-200">
                <h3 class="font-semibold mb-2">Informações do Pedido</h3>
                <p class="text-sm">Total de itens: <strong>{{ $order->items->count() }}</strong></p>
                <p class="text-sm">Total de camisas: <strong>{{ $order->items->sum('quantity') }}</strong></p>
            </div>

            <!-- Abas para cada Item com Personalização -->
            <div class="mb-6">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-4 overflow-x-auto" role="tablist">
                        @foreach($itemPersonalizations as $itemId => $data)
                            @php
                                $item = $data['item'];
                                $persNames = $data['personalization_names'];
                            @endphp
                            @foreach($persNames as $persId => $persName)
                                @php
                                    $isFilled = session('customization_filled.' . $itemId . '.' . $persId, false);
                                @endphp
                                <button
                                    onclick="showTab('item-{{ $itemId }}-pers-{{ $persId }}')"
                                    class="tab-button whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm relative"
                                    data-tab="item-{{ $itemId }}-pers-{{ $persId }}"
                                >
                                    @if($isFilled)
                                        <span class="absolute -top-1 -right-1 bg-green-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">✓</span>
                                    @endif
                                    Item {{ $item->item_number }}: {{ $persName }}
                                </button>
                            @endforeach
                        @endforeach
                    </nav>
                </div>
            </div>

            <!-- Conteúdo das Abas -->
            @foreach($itemPersonalizations as $itemId => $data)
                @php
                    $item = $data['item'];
                    $persNames = $data['personalization_names'];
                @endphp
                @foreach($persNames as $persId => $persName)
                <div id="tab-item-{{ $itemId }}-pers-{{ $persId }}" class="tab-content hidden">
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <h3 class="font-semibold text-lg mb-3">Item {{ $item->item_number }} - {{ $persName }}</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                            <div>
                                <span class="text-gray-600">Tecido:</span>
                                <p class="font-medium">{{ $item->fabric }}</p>
                            </div>
                            <div>
                                <span class="text-gray-600">Cor:</span>
                                <p class="font-medium">{{ $item->color }}</p>
                            </div>
                            <div>
                                <span class="text-gray-600">Gola:</span>
                                <p class="font-medium">{{ $item->collar }}</p>
                            </div>
                            <div>
                                <span class="text-gray-600">Quantidade:</span>
                                <p class="font-medium">{{ $item->quantity }} peças</p>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('orders.wizard.customization') }}" enctype="multipart/form-data" class="space-y-6" id="form-{{ $itemId }}-{{ $persId }}">
                        @csrf
                        <input type="hidden" name="item_id" value="{{ $itemId }}">
                        <input type="hidden" name="personalization_type" value="{{ $persName }}">
                        <input type="hidden" name="personalization_id" value="{{ $persId }}">
                        <input type="hidden" name="applications_data" id="applications-data-{{ $itemId }}-{{ $persId }}">

                        <!-- Nome da Arte -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nome da Arte *</label>
                            <input type="text" name="art_name" required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-2 border"
                                   placeholder="Ex: Logo Empresa - {{ $persName }}">
                        </div>

                        <!-- Imagem de Capa com Drag & Drop e Ctrl+V -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Imagem de Capa do Item {{ $item->item_number }} - {{ $persName }}
                            </label>
                            <p class="text-xs text-gray-500 mb-2">
                                💡 Arraste e solte a imagem, clique para selecionar, ou pressione Ctrl+V para colar da área de transferência
                            </p>
                            
                            <!-- Zona de Drop -->
                            <div id="drop-zone-{{ $itemId }}-{{ $persId }}" 
                                 class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer hover:border-indigo-500 transition-colors bg-gray-50 hover:bg-indigo-50"
                                 onclick="document.getElementById('cover-input-{{ $itemId }}-{{ $persId }}').click()">
                                <div id="drop-text-{{ $itemId }}-{{ $persId }}">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-600">
                                        <span class="font-semibold text-indigo-600">Clique para enviar</span> ou arraste e solte
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF até 10MB</p>
                                    <p class="text-xs text-indigo-600 mt-2 font-semibold">Ou pressione Ctrl+V para colar</p>
                                </div>
                                <div id="drop-preview-{{ $itemId }}-{{ $persId }}" class="hidden">
                                    <img id="drop-preview-img-{{ $itemId }}-{{ $persId }}" src="" alt="Preview" class="max-w-full max-h-64 mx-auto rounded-lg">
                                    <p class="mt-2 text-sm text-green-600 font-medium">✓ Imagem carregada</p>
                                    <button type="button" onclick="event.stopPropagation(); clearCoverImage{{ $itemId }}{{ $persId }}()" class="mt-2 text-xs text-red-600 hover:text-red-800">
                                        ✕ Remover imagem
                                    </button>
                                </div>
                            </div>
                            
                            <input type="file" 
                                   id="cover-input-{{ $itemId }}-{{ $persId }}" 
                                   name="cover_image" 
                                   accept="image/*"
                                   class="hidden"
                                   onchange="handleCoverImage{{ $itemId }}{{ $persId }}(event)">
                        </div>

                        <!-- Arquivos da Arte -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Arquivos da Arte (múltiplos)</label>
                            <input type="file" name="art_files[]" multiple
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-2 border"
                                   onchange="displayFileList{{ $itemId }}{{ $persId }}(event)">
                            <p class="text-xs text-gray-500 mt-1">Você pode selecionar múltiplos arquivos (AI, PDF, PNG, JPG, etc)</p>
                            <div id="file-list-{{ $itemId }}-{{ $persId }}" class="mt-3 space-y-2"></div>
                        </div>

                        @if(in_array($persName, ['SUB. LOCAL', 'SUB.LOCAL', 'SUBLIMAÇÃO']))
                        <!-- Interface de Sublimação -->
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <h4 class="font-semibold mb-2">⚡ Aplicações de Sublimação</h4>
                            <p class="text-sm text-gray-600 mb-4">Adicione quantas aplicações precisar (ex: frente + costas + manga)</p>
                            
                            <input type="hidden" name="total_shirts" value="{{ $item->quantity }}">
                            <input type="hidden" name="sublimations" id="sublimations-{{ $itemId }}-{{ $persId }}">
                            
                            <!-- Lista de Aplicações -->
                            <div id="sublimation-list-{{ $itemId }}-{{ $persId }}" class="space-y-3 mb-4">
                                <!-- Aplicações serão adicionadas aqui -->
                            </div>
                            
                            <!-- Formulário de Nova Aplicação -->
                            <div class="border-t pt-4">
                                <h5 class="font-medium mb-3">Adicionar Nova Aplicação</h5>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Tamanho</label>
                                        <select id="sublimation-size-{{ $itemId }}-{{ $persId }}" class="w-full rounded-md border-gray-300 shadow-sm px-3 py-2 border text-sm" onchange="loadSublimationPrice{{ $itemId }}{{ $persId }}()">
                                            <option value="">Selecione</option>
                                            @if(isset($personalizationData['SUBLIMACAO']['sizes']))
                                                @foreach($personalizationData['SUBLIMACAO']['sizes'] as $size)
                                                    <option value="{{ $size->size_name }}" data-dimensions="{{ $size->size_dimensions }}" data-price="0">
                                                        {{ $size->size_name }}@if($size->size_dimensions) ({{ $size->size_dimensions }})@endif
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Localização</label>
                                        <select id="sublimation-location-{{ $itemId }}-{{ $persId }}" class="w-full rounded-md border-gray-300 shadow-sm px-3 py-2 border text-sm">
                                            <option value="">Selecione</option>
                                            @foreach($locations as $location)
                                                <option value="{{ $location->name }}">{{ $location->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">🖼️ Imagem desta Aplicação (opcional)</label>
                                    <input type="file" id="sublimation-image-{{ $itemId }}-{{ $persId }}" accept="image/*"
                                           class="w-full rounded-md border-gray-300 shadow-sm px-3 py-2 border text-sm">
                                    <p class="text-xs text-gray-500 mt-1">Envie uma imagem específica para esta aplicação</p>
                                </div>
                                <div id="sublimation-price-info-{{ $itemId }}-{{ $persId }}" class="mt-2 p-2 bg-white rounded border text-sm hidden">
                                    <strong>Preço:</strong> <span id="sublimation-price-display-{{ $itemId }}-{{ $persId }}">-</span>
                                </div>
                                <button type="button" onclick="addSublimationApp{{ $itemId }}{{ $persId }}()" class="mt-3 w-full bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-md text-sm">
                                    ➕ Adicionar Aplicação
                                </button>
                            </div>
                        </div>

                        @elseif($persName === 'DTF')
                        <!-- Interface DTF -->
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                            <h4 class="font-semibold mb-2">🎨 Aplicações de DTF</h4>
                            <p class="text-sm text-gray-600 mb-4">Adicione quantas aplicações precisar</p>
                            
                            <input type="hidden" name="total_shirts" value="{{ $item->quantity }}">
                            <input type="hidden" name="sublimations" value="[]">
                            
                            <!-- Lista de Aplicações DTF -->
                            <div id="dtf-list-{{ $itemId }}-{{ $persId }}" class="space-y-3 mb-4">
                                <!-- Aplicações serão adicionadas aqui -->
                            </div>
                            
                            <!-- Formulário de Nova Aplicação -->
                            <div class="border-t pt-4">
                                <h5 class="font-medium mb-3">Adicionar Nova Aplicação DTF</h5>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Tamanho</label>
                                        <select id="dtf-size-{{ $itemId }}-{{ $persId }}" class="w-full rounded-md border-gray-300 shadow-sm px-3 py-2 border text-sm" onchange="loadDTFPrice{{ $itemId }}{{ $persId }}()">
                                            <option value="">Selecione</option>
                                            @if(isset($personalizationData['DTF']['sizes']))
                                                @foreach($personalizationData['DTF']['sizes'] as $size)
                                                    <option value="{{ $size->size_name }}" data-dimensions="{{ $size->size_dimensions }}" data-price="0">
                                                        {{ $size->size_name }}@if($size->size_dimensions) ({{ $size->size_dimensions }})@endif
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Localização</label>
                                        <select id="dtf-location-{{ $itemId }}-{{ $persId }}" class="w-full rounded-md border-gray-300 shadow-sm px-3 py-2 border text-sm">
                                            <option value="">Selecione</option>
                                            @foreach($locations as $location)
                                                <option value="{{ $location->name }}">{{ $location->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">🖼️ Imagem desta Aplicação (opcional)</label>
                                    <input type="file" id="dtf-image-{{ $itemId }}-{{ $persId }}" accept="image/*"
                                           class="w-full rounded-md border-gray-300 shadow-sm px-3 py-2 border text-sm">
                                    <p class="text-xs text-gray-500 mt-1">Envie uma imagem específica para esta aplicação</p>
                                </div>
                                <div id="dtf-price-info-{{ $itemId }}-{{ $persId }}" class="mt-2 p-2 bg-white rounded border text-sm hidden">
                                    <strong>Preço:</strong> <span id="dtf-price-display-{{ $itemId }}-{{ $persId }}">-</span>
                                </div>
                                <button type="button" onclick="addDTFApp{{ $itemId }}{{ $persId }}()" class="mt-3 w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md text-sm">
                                    ➕ Adicionar Aplicação DTF
                                </button>
                            </div>
                        </div>

                        @elseif($persName === 'BORDADO')
                        <!-- Interface Bordado -->
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <h4 class="font-semibold mb-2">🧵 Aplicações de Bordado</h4>
                            <p class="text-sm text-gray-600 mb-4">Adicione quantos bordados precisar</p>
                            
                            <input type="hidden" name="total_shirts" value="{{ $item->quantity }}">
                            <input type="hidden" name="sublimations" value="[]">
                            
                            <!-- Lista de Aplicações Bordado -->
                            <div id="embroidery-list-{{ $itemId }}-{{ $persId }}" class="space-y-3 mb-4">
                                <!-- Aplicações serão adicionadas aqui -->
                            </div>
                            
                            <!-- Formulário de Nova Aplicação -->
                            <div class="border-t pt-4">
                                <h5 class="font-medium mb-3">Adicionar Novo Bordado</h5>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Tamanho</label>
                                        <select id="embroidery-size-{{ $itemId }}-{{ $persId }}" class="w-full rounded-md border-gray-300 shadow-sm px-3 py-2 border text-sm" onchange="loadEmbroideryPrice{{ $itemId }}{{ $persId }}()">
                                            <option value="">Selecione</option>
                                            @if(isset($personalizationData['BORDADO']['sizes']))
                                                @foreach($personalizationData['BORDADO']['sizes'] as $size)
                                                    <option value="{{ $size->size_name }}" data-dimensions="{{ $size->size_dimensions }}" data-price="0">
                                                        {{ $size->size_name }}@if($size->size_dimensions) ({{ $size->size_dimensions }})@endif
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div id="embroidery-price-info-{{ $itemId }}-{{ $persId }}" class="p-2 bg-white rounded border text-sm hidden">
                                        <strong>Preço:</strong> <span id="embroidery-price-display-{{ $itemId }}-{{ $persId }}">-</span>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Cores (separadas por vírgula)</label>
                                        <input type="text" id="embroidery-colors-{{ $itemId }}-{{ $persId }}" 
                                               class="w-full rounded-md border-gray-300 shadow-sm px-3 py-2 border text-sm"
                                               placeholder="Ex: Azul, Branco, Vermelho">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Localização</label>
                                        <select id="embroidery-location-{{ $itemId }}-{{ $persId }}" class="w-full rounded-md border-gray-300 shadow-sm px-3 py-2 border text-sm">
                                            <option value="">Selecione</option>
                                            @foreach($locations as $location)
                                                <option value="{{ $location->name }}">{{ $location->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">🖼️ Imagem desta Aplicação (opcional)</label>
                                        <input type="file" id="embroidery-image-{{ $itemId }}-{{ $persId }}" accept="image/*"
                                               class="w-full rounded-md border-gray-300 shadow-sm px-3 py-2 border text-sm">
                                        <p class="text-xs text-gray-500 mt-1">Envie uma imagem específica para esta aplicação</p>
                                    </div>
                                </div>
                                <button type="button" onclick="addEmbroideryApp{{ $itemId }}{{ $persId }}()" class="mt-3 w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm">
                                    ➕ Adicionar Bordado
                                </button>
                            </div>
                        </div>

                        @elseif($persName === 'SERIGRAFIA')
                        <!-- Interface Serigrafia -->
                        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                            <h4 class="font-semibold mb-2">🎨 Aplicações de Serigrafia</h4>
                            <p class="text-sm text-gray-600 mb-4">Adicione quantas serigrafias precisar</p>
                            <p class="text-xs text-orange-600 font-medium mb-4">💡 A partir da 3ª aplicação: 50% de desconto!</p>
                            
                            <input type="hidden" name="total_shirts" value="{{ $item->quantity }}">
                            <input type="hidden" name="sublimations" value="[]">
                            
                            <!-- Lista de Aplicações Serigrafia -->
                            <div id="serigraphy-list-{{ $itemId }}-{{ $persId }}" class="space-y-3 mb-4">
                                <!-- Aplicações serão adicionadas aqui -->
                            </div>
                            
                            <!-- Formulário de Nova Aplicação -->
                            <div class="border-t pt-4">
                                <h5 class="font-medium mb-3">Adicionar Nova Serigrafia</h5>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Tamanho</label>
                                        <select id="serigraphy-size-{{ $itemId }}-{{ $persId }}" onchange="loadSerigraphyPrice{{ $itemId }}{{ $persId }}()" class="w-full rounded-md border-gray-300 shadow-sm px-3 py-2 border text-sm">
                                            <option value="">Selecione</option>
                                            @if(isset($personalizationData['SERIGRAFIA']['sizes']))
                                                @foreach($personalizationData['SERIGRAFIA']['sizes'] as $size)
                                                    <option value="{{ $size->size_name }}" data-dimensions="{{ $size->size_dimensions }}" data-price="0">
                                                        {{ $size->size_name }}@if($size->size_dimensions) ({{ $size->size_dimensions }})@endif
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Quantidade de Cores * (máx. 6)</label>
                                        <div class="grid grid-cols-3 gap-2">
                                            <button type="button" onclick="selectSerigraphyColors{{ $itemId }}{{ $persId }}(1)" 
                                                    class="serigraphy-color-btn-{{ $itemId }}-{{ $persId }} p-2 border-2 rounded hover:border-orange-500 text-sm" data-colors="1">
                                                1 Cor<br><span class="text-xs text-gray-500">+R$ 5,00</span>
                                            </button>
                                            <button type="button" onclick="selectSerigraphyColors{{ $itemId }}{{ $persId }}(2)" 
                                                    class="serigraphy-color-btn-{{ $itemId }}-{{ $persId }} p-2 border-2 rounded hover:border-orange-500 text-sm" data-colors="2">
                                                2 Cores<br><span class="text-xs text-gray-500">+R$ 10,00</span>
                                            </button>
                                            <button type="button" onclick="selectSerigraphyColors{{ $itemId }}{{ $persId }}(3)" 
                                                    class="serigraphy-color-btn-{{ $itemId }}-{{ $persId }} p-2 border-2 rounded hover:border-orange-500 text-sm" data-colors="3">
                                                3 Cores<br><span class="text-xs text-gray-500">+R$ 15,00</span>
                                            </button>
                                            <button type="button" onclick="selectSerigraphyColors{{ $itemId }}{{ $persId }}(4)" 
                                                    class="serigraphy-color-btn-{{ $itemId }}-{{ $persId }} p-2 border-2 rounded hover:border-orange-500 text-sm" data-colors="4">
                                                4 Cores<br><span class="text-xs text-gray-500">+R$ 20,00</span>
                                            </button>
                                            <button type="button" onclick="selectSerigraphyColors{{ $itemId }}{{ $persId }}(5)" 
                                                    class="serigraphy-color-btn-{{ $itemId }}-{{ $persId }} p-2 border-2 rounded hover:border-orange-500 text-sm" data-colors="5">
                                                5 Cores<br><span class="text-xs text-gray-500">+R$ 25,00</span>
                                            </button>
                                            <button type="button" onclick="selectSerigraphyColors{{ $itemId }}{{ $persId }}(6)" 
                                                    class="serigraphy-color-btn-{{ $itemId }}-{{ $persId }} p-2 border-2 rounded hover:border-orange-500 text-sm" data-colors="6">
                                                6 Cores<br><span class="text-xs text-gray-500">+R$ 30,00</span>
                                            </button>
                                        </div>
                                        <input type="hidden" id="serigraphy-color-count-{{ $itemId }}-{{ $persId }}" value="1">
                                    </div>
                                    <div>
                                        <label class="flex items-center">
                                            <input type="checkbox" id="serigraphy-neon-{{ $itemId }}-{{ $persId }}" 
                                                   onchange="updateSerigraphyPreview{{ $itemId }}{{ $persId }}()"
                                                   class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                                            <span class="ml-2 text-sm font-medium text-gray-700">Cor Neon (+50% no valor base)</span>
                                        </label>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Localização</label>
                                        <select id="serigraphy-location-{{ $itemId }}-{{ $persId }}" class="w-full rounded-md border-gray-300 shadow-sm px-3 py-2 border text-sm">
                                            <option value="">Selecione</option>
                                            <option value="FRENTE">Frente</option>
                                            <option value="COSTAS">Costas</option>
                                            <option value="MANGA">Manga</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">🖼️ Imagem desta Aplicação (opcional)</label>
                                        <input type="file" id="serigraphy-image-{{ $itemId }}-{{ $persId }}" accept="image/*"
                                               class="w-full rounded-md border-gray-300 shadow-sm px-3 py-2 border text-sm">
                                        <p class="text-xs text-gray-500 mt-1">Envie uma imagem específica para esta aplicação</p>
                                    </div>
                                    <div class="bg-white border-2 border-orange-300 rounded p-3">
                                        <div class="text-sm font-medium mb-1">Preview do Preço:</div>
                                        <div id="serigraphy-preview-{{ $itemId }}-{{ $persId }}" class="text-xs space-y-1">
                                            <div>Base: <span id="preview-base-{{ $itemId }}-{{ $persId }}">R$ 0,00</span></div>
                                            <div>Cores: <span id="preview-colors-{{ $itemId }}-{{ $persId }}">R$ 0,00</span></div>
                                            <div>Neon: <span id="preview-neon-{{ $itemId }}-{{ $persId }}">R$ 0,00</span></div>
                                            <div class="font-bold text-orange-600 pt-1 border-t">Total: <span id="preview-total-{{ $itemId }}-{{ $persId }}">R$ 0,00</span></div>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" onclick="addSerigraphyApp{{ $itemId }}{{ $persId }}()" class="mt-3 w-full bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-md text-sm">
                                    ➕ Adicionar Serigrafia
                                </button>
                            </div>
                        </div>
                        @endif

                        <!-- Resumo de Valores -->
                        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                            <h4 class="font-semibold mb-3">💰 Resumo de Valores - Item {{ $item->item_number }}</h4>
                            <div id="price-summary-{{ $itemId }}-{{ $persId }}" class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span>Valor da Peça:</span>
                                    <span class="font-medium">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Quantidade:</span>
                                    <span class="font-medium">{{ $item->quantity }} peças</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Subtotal Peças:</span>
                                    <span class="font-medium">R$ {{ number_format($item->total_price, 2, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Total de Aplicações:</span>
                                    <span class="font-medium" id="app-count-{{ $itemId }}-{{ $persId }}">0</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Valor das Aplicações:</span>
                                    <span class="font-medium" id="app-value-{{ $itemId }}-{{ $persId }}">R$ 0,00</span>
                                </div>
                                <div class="flex justify-between font-bold text-lg text-indigo-600 pt-2 border-t">
                                    <span>Total Item {{ $item->item_number }}:</span>
                                    <span id="total-item-{{ $itemId }}-{{ $persId }}">R$ {{ number_format($item->total_price, 2, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Observações -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Observações</label>
                            <textarea name="notes" rows="3"
                                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-2 border"
                                      placeholder="Informações adicionais sobre esta personalização..."></textarea>
                        </div>

                        <div class="flex justify-between items-center pt-4 border-t mt-4">
                            <p class="text-sm text-gray-600">
                                💡 <strong>Dica:</strong> Salve cada personalização antes de mudar de aba
                            </p>
                            <button type="submit" 
                                    class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 font-semibold">
                                ✓ Salvar esta Personalização
                            </button>
                        </div>
                    </form>
                    
                    <script>
                        // Debug: Verificar arquivos antes do envio
                        document.getElementById('form-{{ $itemId }}-{{ $persId }}').addEventListener('submit', function(e) {
                            const formData = new FormData(this);
                            console.log('📤 Enviando formulário para item {{ $itemId }}-{{ $persId }}');
                            
                            let imageCount = 0;
                            for (let pair of formData.entries()) {
                                if (pair[0].startsWith('application_image_')) {
                                    console.log('  ✅ Arquivo encontrado:', pair[0], pair[1].name);
                                    imageCount++;
                                }
                            }
                            console.log(`  Total de imagens de aplicação: ${imageCount}`);
                        });
                    </script>

                    <script>
                        // Definir variáveis globais para este item
                        window.item{{ $itemId }}{{ $persId }}Data = {
                            itemId: '{{ $itemId }}',
                            persId: '{{ $persId }}',
                            persName: '{{ $persName }}',
                            itemQuantity: {{ $item->quantity }},
                            itemPrice: {{ $item->total_price }},
                            applications: []
                        };

                        (function() {
                            const data = window.item{{ $itemId }}{{ $persId }}Data;
                            const {itemId, persId, persName, itemQuantity, itemPrice} = data;
                            
                            // Aguardar DOM estar pronto
                            setTimeout(function() {
                                const dropZone = document.getElementById(`drop-zone-${itemId}-${persId}`);
                                const input = document.getElementById(`cover-input-${itemId}-${persId}`);
                                const dropText = document.getElementById(`drop-text-${itemId}-${persId}`);
                                const dropPreview = document.getElementById(`drop-preview-${itemId}-${persId}`);
                                const previewImg = document.getElementById(`drop-preview-img-${itemId}-${persId}`);

                                if (!dropZone || !input) {
                                    console.warn('Elementos de upload não encontrados para item', itemId, persId);
                                    return;
                                }

                                // Prevenir comportamento padrão para drag & drop
                                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                                    dropZone.addEventListener(eventName, preventDefaults, false);
                                });

                            function preventDefaults(e) {
                                e.preventDefault();
                                e.stopPropagation();
                            }

                            // Highlight da zona de drop
                            ['dragenter', 'dragover'].forEach(eventName => {
                                dropZone.addEventListener(eventName, () => {
                                    dropZone.classList.add('border-indigo-600', 'bg-indigo-100');
                                }, false);
                            });

                            ['dragleave', 'drop'].forEach(eventName => {
                                dropZone.addEventListener(eventName, () => {
                                    dropZone.classList.remove('border-indigo-600', 'bg-indigo-100');
                                }, false);
                            });

                            // Handle drop
                            dropZone.addEventListener('drop', function(e) {
                                const dt = e.dataTransfer;
                                const files = dt.files;
                                
                                if (files.length > 0) {
                                    const file = files[0];
                                    if (file.type.startsWith('image/')) {
                                        handleFile(file);
                                    } else {
                                        alert('Por favor, envie apenas imagens.');
                                    }
                                }
                            }, false);

                            // Definir função de manipulação de arquivo no escopo local
                            function handleFile(file) {
                                // Validar tamanho (10MB)
                                if (file.size > 10 * 1024 * 1024) {
                                    alert('A imagem deve ter no máximo 10MB');
                                    return;
                                }

                                const reader = new FileReader();
                                reader.onload = function(e) {
                                    previewImg.src = e.target.result;
                                    dropText.classList.add('hidden');
                                    dropPreview.classList.remove('hidden');
                                };
                                reader.readAsDataURL(file);

                                // Criar um DataTransfer para adicionar o arquivo ao input
                                const dataTransfer = new DataTransfer();
                                dataTransfer.items.add(file);
                                input.files = dataTransfer.files;
                            }

                            // Handle paste (Ctrl+V)
                            document.addEventListener('paste', function(e) {
                                // Verificar se esta aba está ativa
                                const tabContent = document.getElementById(`tab-item-${itemId}-pers-${persId}`);
                                if (tabContent && !tabContent.classList.contains('hidden')) {
                                    const items = e.clipboardData.items;
                                    
                                    for (let i = 0; i < items.length; i++) {
                                        if (items[i].type.indexOf('image') !== -1) {
                                            e.preventDefault();
                                            const blob = items[i].getAsFile();
                                            handleFile(blob);
                                            break;
                                        }
                                    }
                                }
                            });

                            // Funções globais para este item
                            window['handleCoverImage{{ $itemId }}{{ $persId }}'] = function(event) {
                                const file = event.target.files[0];
                                if (file) {
                                    handleFile(file);
                                }
                            };

                            window['clearCoverImage{{ $itemId }}{{ $persId }}'] = function() {
                                input.value = '';
                                dropText.classList.remove('hidden');
                                dropPreview.classList.add('hidden');
                                previewImg.src = '';
                            };
                            }, 100); // Aguardar 100ms para garantir que DOM está pronto
                        })();

                        // Funções globais para este item
                        window['displayFileList{{ $itemId }}{{ $persId }}'] = function(event) {
                            const input = event.target;
                            const fileList = document.getElementById('file-list-{{ $itemId }}-{{ $persId }}');
                            fileList.innerHTML = '';
                            
                            Array.from(input.files).forEach(file => {
                                const div = document.createElement('div');
                                div.className = 'text-sm text-gray-600 flex items-center';
                                div.innerHTML = `<span class="mr-2">📎</span> ${file.name} (${(file.size / 1024).toFixed(1)} KB)`;
                                fileList.appendChild(div);
                            });
                        };

                        // Funções para gerenciar aplicações
                        window['updateSummary{{ $itemId }}{{ $persId }}'] = function() {
                            const data = window.item{{ $itemId }}{{ $persId }}Data;
                            const {itemId, persId, persName, itemQuantity, itemPrice, applications} = data;
                            const totalApps = applications.length;
                            
                            // Preparar dados para envio ao backend
                            const applicationsForBackend = applications.map((app, index) => {
                                // Calcular preço com desconto se for SERIGRAFIA
                                let finalPrice = app.price;
                                let discountPercent = 0;
                                
                                if (persName === 'SERIGRAFIA' && index >= 2) {
                                    finalPrice = app.price * 0.5;
                                    discountPercent = 50;
                                }
                                
                                return {
                                    ...app,
                                    quantity: itemQuantity,
                                    final_price: finalPrice,
                                    discount_percent: discountPercent
                                };
                            });
                            
                            // Calcular valor total para exibição
                            let totalValue = 0;
                            applicationsForBackend.forEach(app => {
                                totalValue += app.final_price * app.quantity;
                            });
                            
                            const grandTotal = itemPrice + totalValue;

                            document.getElementById(`app-count-${itemId}-${persId}`).textContent = totalApps;
                            document.getElementById(`app-value-${itemId}-${persId}`).textContent = 
                                'R$ ' + totalValue.toFixed(2).replace('.', ',');
                            document.getElementById(`total-item-${itemId}-${persId}`).textContent = 
                                'R$ ' + grandTotal.toFixed(2).replace('.', ',');
                            
                            // Salvar dados com todos os campos necessários para o backend
                            document.getElementById(`applications-data-${itemId}-${persId}`).value = JSON.stringify(applicationsForBackend);
                        };

                        window['removeApp{{ $itemId }}{{ $persId }}'] = function(index) {
                            const data = window.item{{ $itemId }}{{ $persId }}Data;
                            data.applications.splice(index, 1);
                            window['renderApplicationsList{{ $itemId }}{{ $persId }}']();
                            window['updateSummary{{ $itemId }}{{ $persId }}']();
                        };

                        window['renderApplicationsList{{ $itemId }}{{ $persId }}'] = function() {
                            const data = window.item{{ $itemId }}{{ $persId }}Data;
                            const {itemId, persId, persName, itemQuantity, applications} = data;
                            
                            const listId = persName.toLowerCase().includes('sublim') ? 'sublimation' :
                                          persName === 'DTF' ? 'dtf' :
                                          persName === 'BORDADO' ? 'embroidery' : 'serigraphy';
                            const list = document.getElementById(`${listId}-list-${itemId}-${persId}`);
                            
                            if (applications.length === 0) {
                                list.innerHTML = '<p class="text-sm text-gray-500 italic">Nenhuma aplicação adicionada ainda</p>';
                                return;
                            }

                            list.innerHTML = applications.map((app, index) => {
                                // Desconto de 50% APENAS para SERIGRAFIA a partir da 3ª aplicação
                                const hasDiscount = (persName === 'SERIGRAFIA' && index >= 2);
                                const finalPrice = hasDiscount ? app.price * 0.5 : app.price;
                                const totalPrice = finalPrice * itemQuantity;
                                
                                let colorInfo = '';
                                if (app.colorCount) {
                                    colorInfo = `<div><strong>Cores:</strong> ${app.colorCount} ${app.colorCount === 1 ? 'cor' : 'cores'} 
                                                ${app.hasNeon ? '<span class="text-orange-600">(+ NEON)</span>' : ''}</div>`;
                                } else if (app.colors) {
                                    colorInfo = `<div><strong>Cores:</strong> ${app.colors}</div>`;
                                }
                                
                                let imageInfo = '';
                                if (app.hasImage) {
                                    imageInfo = `<div class="mt-1"><span class="text-green-600">🖼️ Imagem: ${app.imageName || 'anexada'}</span></div>`;
                                }
                                
                                return `
                                <div class="bg-white border-2 ${hasDiscount ? 'border-green-400' : 'border-gray-300'} rounded-lg p-3 ${hasDiscount ? 'bg-green-50' : ''}">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="font-semibold text-sm mb-1">
                                                Aplicação ${index + 1}
                                                ${hasDiscount ? '<span class="ml-2 text-xs bg-green-600 text-white px-2 py-1 rounded">50% OFF</span>' : ''}
                                            </div>
                                            <div class="text-xs space-y-1">
                                                <div><strong>Tamanho:</strong> ${app.size}</div>
                                                <div><strong>Localização:</strong> ${app.location}</div>
                                                ${colorInfo}
                                                ${imageInfo}
                                                ${hasDiscount ? `
                                                <div class="text-gray-500 line-through">
                                                    De: R$ ${app.price.toFixed(2).replace('.', ',')} × ${itemQuantity} = 
                                                    R$ ${(app.price * itemQuantity).toFixed(2).replace('.', ',')}
                                                </div>
                                                <div class="text-green-600 font-bold">
                                                    Por: R$ ${finalPrice.toFixed(2).replace('.', ',')} × ${itemQuantity} peças = 
                                                    R$ ${totalPrice.toFixed(2).replace('.', ',')}
                                                </div>
                                                ` : `
                                                <div class="text-indigo-600 font-bold mt-2">
                                                    R$ ${app.price.toFixed(2).replace('.', ',')} × ${itemQuantity} peças = 
                                                    R$ ${totalPrice.toFixed(2).replace('.', ',')}
                                                </div>
                                                `}
                                            </div>
                                        </div>
                                        <button type="button" onclick="removeApp{{ $itemId }}{{ $persId }}(${index})" 
                                                class="ml-3 text-red-600 hover:text-red-800 font-bold">
                                            ✕
                                        </button>
                                    </div>
                                </div>
                            `}).join('');
                        };

                        // Funções para carregar preços via API
                        window['loadSublimationPrice{{ $itemId }}{{ $persId }}'] = async function() {
                            const data = window.item{{ $itemId }}{{ $persId }}Data;
                            const {itemId, persId, itemQuantity} = data;
                            const sizeSelect = document.getElementById(`sublimation-size-${itemId}-${persId}`);
                            const priceInfo = document.getElementById(`sublimation-price-info-${itemId}-${persId}`);
                            const priceDisplay = document.getElementById(`sublimation-price-display-${itemId}-${persId}`);
                            
                            if (!sizeSelect.value) {
                                priceInfo.classList.add('hidden');
                                return;
                            }
                            
                            try {
                                const response = await fetch(`/api/personalization-prices/price?type=SUBLIMACAO&size=${encodeURIComponent(sizeSelect.value)}&quantity=${itemQuantity}`);
                                const result = await response.json();
                                
                                if (result.success) {
                                    sizeSelect.options[sizeSelect.selectedIndex].dataset.price = result.price;
                                    priceDisplay.textContent = `R$ ${parseFloat(result.price).toFixed(2).replace('.', ',')}`;
                                    priceInfo.classList.remove('hidden');
                                    
                                    // Atualizar texto da opção
                                    const dimensions = result.size_dimensions ? ` (${result.size_dimensions})` : '';
                                    sizeSelect.options[sizeSelect.selectedIndex].text = `${result.size_name}${dimensions} - R$ ${parseFloat(result.price).toFixed(2).replace('.', ',')}`;
                                } else {
                                    priceDisplay.textContent = 'Preço não encontrado';
                                    priceInfo.classList.remove('hidden');
                                }
                            } catch (error) {
                                console.error('Erro ao carregar preço:', error);
                                priceDisplay.textContent = 'Erro ao carregar';
                                priceInfo.classList.remove('hidden');
                            }
                        };

                        window['loadDTFPrice{{ $itemId }}{{ $persId }}'] = async function() {
                            const data = window.item{{ $itemId }}{{ $persId }}Data;
                            const {itemId, persId, itemQuantity} = data;
                            const sizeSelect = document.getElementById(`dtf-size-${itemId}-${persId}`);
                            const priceInfo = document.getElementById(`dtf-price-info-${itemId}-${persId}`);
                            const priceDisplay = document.getElementById(`dtf-price-display-${itemId}-${persId}`);
                            
                            if (!sizeSelect.value) {
                                priceInfo.classList.add('hidden');
                                return;
                            }
                            
                            try {
                                const response = await fetch(`/api/personalization-prices/price?type=DTF&size=${encodeURIComponent(sizeSelect.value)}&quantity=${itemQuantity}`);
                                const result = await response.json();
                                
                                if (result.success) {
                                    sizeSelect.options[sizeSelect.selectedIndex].dataset.price = result.price;
                                    priceDisplay.textContent = `R$ ${parseFloat(result.price).toFixed(2).replace('.', ',')}`;
                                    priceInfo.classList.remove('hidden');
                                    
                                    const dimensions = result.size_dimensions ? ` (${result.size_dimensions})` : '';
                                    sizeSelect.options[sizeSelect.selectedIndex].text = `${result.size_name}${dimensions} - R$ ${parseFloat(result.price).toFixed(2).replace('.', ',')}`;
                                }
                            } catch (error) {
                                console.error('Erro ao carregar preço:', error);
                            }
                        };

                        window['loadEmbroideryPrice{{ $itemId }}{{ $persId }}'] = async function() {
                            const data = window.item{{ $itemId }}{{ $persId }}Data;
                            const {itemId, persId, itemQuantity} = data;
                            const sizeSelect = document.getElementById(`embroidery-size-${itemId}-${persId}`);
                            const priceInfo = document.getElementById(`embroidery-price-info-${itemId}-${persId}`);
                            const priceDisplay = document.getElementById(`embroidery-price-display-${itemId}-${persId}`);
                            
                            if (!sizeSelect.value) {
                                priceInfo.classList.add('hidden');
                                return;
                            }
                            
                            try {
                                const response = await fetch(`/api/personalization-prices/price?type=BORDADO&size=${encodeURIComponent(sizeSelect.value)}&quantity=${itemQuantity}`);
                                const result = await response.json();
                                
                                if (result.success) {
                                    sizeSelect.options[sizeSelect.selectedIndex].dataset.price = result.price;
                                    priceDisplay.textContent = `R$ ${parseFloat(result.price).toFixed(2).replace('.', ',')}`;
                                    priceInfo.classList.remove('hidden');
                                    
                                    const dimensions = result.size_dimensions ? ` (${result.size_dimensions})` : '';
                                    sizeSelect.options[sizeSelect.selectedIndex].text = `${result.size_name}${dimensions} - R$ ${parseFloat(result.price).toFixed(2).replace('.', ',')}`;
                                }
                            } catch (error) {
                                console.error('Erro ao carregar preço:', error);
                            }
                        };

                        window['loadSerigraphyPrice{{ $itemId }}{{ $persId }}'] = async function() {
                            const data = window.item{{ $itemId }}{{ $persId }}Data;
                            const {itemId, persId, itemQuantity} = data;
                            const sizeSelect = document.getElementById(`serigraphy-size-${itemId}-${persId}`);
                            
                            if (!sizeSelect.value) {
                                document.getElementById(`preview-base-${itemId}-${persId}`).textContent = 'R$ 0,00';
                                document.getElementById(`preview-colors-${itemId}-${persId}`).textContent = 'R$ 0,00';
                                document.getElementById(`preview-neon-${itemId}-${persId}`).textContent = 'R$ 0,00';
                                document.getElementById(`preview-total-${itemId}-${persId}`).textContent = 'R$ 0,00';
                                return;
                            }
                            
                            try {
                                const response = await fetch(`/api/personalization-prices/price?type=SERIGRAFIA&size=${encodeURIComponent(sizeSelect.value)}&quantity=${itemQuantity}`);
                                const result = await response.json();
                                
                                if (result.success) {
                                    sizeSelect.options[sizeSelect.selectedIndex].dataset.price = result.price;
                                    const dimensions = result.size_dimensions ? ` (${result.size_dimensions})` : '';
                                    sizeSelect.options[sizeSelect.selectedIndex].text = `${result.size_name}${dimensions} (Base: R$ ${parseFloat(result.price).toFixed(2).replace('.', ',')})`;
                                    
                                    // Atualizar preview
                                    window['updateSerigraphyPreview{{ $itemId }}{{ $persId }}']();
                                }
                            } catch (error) {
                                console.error('Erro ao carregar preço:', error);
                            }
                        };

                        // Funções de adicionar aplicações
                        window['addSublimationApp{{ $itemId }}{{ $persId }}'] = function() {
                            const data = window.item{{ $itemId }}{{ $persId }}Data;
                            const {itemId, persId} = data;
                            
                            const sizeSelect = document.getElementById(`sublimation-size-${itemId}-${persId}`);
                            const locationSelect = document.getElementById(`sublimation-location-${itemId}-${persId}`);
                            const imageInput = document.getElementById(`sublimation-image-${itemId}-${persId}`);
                            
                            if (!sizeSelect.value || !locationSelect.value) {
                                alert('Preencha tamanho e localização');
                                return;
                            }

                            const price = parseFloat(sizeSelect.options[sizeSelect.selectedIndex].dataset.price);
                            if (!price || price === 0) {
                                alert('Carregue o preço primeiro selecionando o tamanho');
                                return;
                            }

                            // Criar input file hidden para esta aplicação usando DataTransfer
                            const appIndex = data.applications.length;
                            let hasImage = false;
                            
                            if (imageInput.files && imageInput.files[0]) {
                                hasImage = true;
                                const form = document.getElementById(`form-${itemId}-${persId}`);
                                const hiddenFileInput = document.createElement('input');
                                hiddenFileInput.type = 'file';
                                hiddenFileInput.name = `application_image_${appIndex}`;
                                hiddenFileInput.style.display = 'none';
                                
                                // Usar DataTransfer para copiar o arquivo
                                const dataTransfer = new DataTransfer();
                                dataTransfer.items.add(imageInput.files[0]);
                                hiddenFileInput.files = dataTransfer.files;
                                
                                form.appendChild(hiddenFileInput);
                                console.log(`✅ Imagem adicionada para aplicação ${appIndex}:`, imageInput.files[0].name);
                            }

                            data.applications.push({
                                type: 'sublimation',
                                size: sizeSelect.value,
                                location: locationSelect.options[locationSelect.selectedIndex].text,
                                price: price,
                                hasImage: hasImage,
                                imageName: hasImage ? imageInput.files[0].name : null
                            });

                            sizeSelect.value = '';
                            locationSelect.value = '';
                            imageInput.value = '';
                            document.getElementById(`sublimation-price-info-${itemId}-${persId}`).classList.add('hidden');
                            window['renderApplicationsList{{ $itemId }}{{ $persId }}']();
                            window['updateSummary{{ $itemId }}{{ $persId }}']();
                        };

                        window['addDTFApp{{ $itemId }}{{ $persId }}'] = function() {
                            const data = window.item{{ $itemId }}{{ $persId }}Data;
                            const {itemId, persId} = data;
                            
                            const sizeSelect = document.getElementById(`dtf-size-${itemId}-${persId}`);
                            const locationSelect = document.getElementById(`dtf-location-${itemId}-${persId}`);
                            const imageInput = document.getElementById(`dtf-image-${itemId}-${persId}`);
                            
                            if (!sizeSelect.value || !locationSelect.value) {
                                alert('Preencha tamanho e localização');
                                return;
                            }

                            const price = parseFloat(sizeSelect.options[sizeSelect.selectedIndex].dataset.price);
                            if (!price || price === 0) {
                                alert('Carregue o preço primeiro selecionando o tamanho');
                                return;
                            }

                            // Criar input file hidden para esta aplicação usando DataTransfer
                            const appIndex = data.applications.length;
                            let hasImage = false;
                            
                            if (imageInput.files && imageInput.files[0]) {
                                hasImage = true;
                                const form = document.getElementById(`form-${itemId}-${persId}`);
                                const hiddenFileInput = document.createElement('input');
                                hiddenFileInput.type = 'file';
                                hiddenFileInput.name = `application_image_${appIndex}`;
                                hiddenFileInput.style.display = 'none';
                                
                                // Usar DataTransfer para copiar o arquivo
                                const dataTransfer = new DataTransfer();
                                dataTransfer.items.add(imageInput.files[0]);
                                hiddenFileInput.files = dataTransfer.files;
                                
                                form.appendChild(hiddenFileInput);
                            }

                            data.applications.push({
                                type: 'dtf',
                                size: sizeSelect.value,
                                location: locationSelect.options[locationSelect.selectedIndex].text,
                                price: price,
                                hasImage: hasImage,
                                imageName: hasImage ? imageInput.files[0].name : null
                            });

                            sizeSelect.value = '';
                            locationSelect.value = '';
                            imageInput.value = '';
                            document.getElementById(`dtf-price-info-${itemId}-${persId}`).classList.add('hidden');
                            window['renderApplicationsList{{ $itemId }}{{ $persId }}']();
                            window['updateSummary{{ $itemId }}{{ $persId }}']();
                        };

                        window['addEmbroideryApp{{ $itemId }}{{ $persId }}'] = function() {
                            const data = window.item{{ $itemId }}{{ $persId }}Data;
                            const {itemId, persId} = data;
                            
                            const sizeSelect = document.getElementById(`embroidery-size-${itemId}-${persId}`);
                            const colorsInput = document.getElementById(`embroidery-colors-${itemId}-${persId}`);
                            const locationSelect = document.getElementById(`embroidery-location-${itemId}-${persId}`);
                            const imageInput = document.getElementById(`embroidery-image-${itemId}-${persId}`);
                            
                            if (!sizeSelect.value || !colorsInput.value || !locationSelect.value) {
                                alert('Preencha todos os campos');
                                return;
                            }

                            const price = parseFloat(sizeSelect.options[sizeSelect.selectedIndex].dataset.price);
                            if (!price || price === 0) {
                                alert('Carregue o preço primeiro selecionando o tamanho');
                                return;
                            }

                            // Criar input file hidden para esta aplicação usando DataTransfer
                            const appIndex = data.applications.length;
                            let hasImage = false;
                            
                            if (imageInput.files && imageInput.files[0]) {
                                hasImage = true;
                                const form = document.getElementById(`form-${itemId}-${persId}`);
                                const hiddenFileInput = document.createElement('input');
                                hiddenFileInput.type = 'file';
                                hiddenFileInput.name = `application_image_${appIndex}`;
                                hiddenFileInput.style.display = 'none';
                                
                                // Usar DataTransfer para copiar o arquivo
                                const dataTransfer = new DataTransfer();
                                dataTransfer.items.add(imageInput.files[0]);
                                hiddenFileInput.files = dataTransfer.files;
                                
                                form.appendChild(hiddenFileInput);
                            }

                            data.applications.push({
                                type: 'embroidery',
                                size: sizeSelect.value,
                                colors: colorsInput.value,
                                location: locationSelect.options[locationSelect.selectedIndex].text,
                                price: price,
                                hasImage: hasImage,
                                imageName: hasImage ? imageInput.files[0].name : null
                            });

                            sizeSelect.value = '';
                            colorsInput.value = '';
                            locationSelect.value = '';
                            imageInput.value = '';
                            document.getElementById(`embroidery-price-info-${itemId}-${persId}`).classList.add('hidden');
                            window['renderApplicationsList{{ $itemId }}{{ $persId }}']();
                            window['updateSummary{{ $itemId }}{{ $persId }}']();
                        };

                        // Sistema de cores para Serigrafia
                        window['selectSerigraphyColors{{ $itemId }}{{ $persId }}'] = function(count) {
                            const data = window.item{{ $itemId }}{{ $persId }}Data;
                            const {itemId, persId} = data;
                            
                            document.getElementById(`serigraphy-color-count-${itemId}-${persId}`).value = count;
                            
                            // Atualizar estilos dos botões
                            document.querySelectorAll(`.serigraphy-color-btn-${itemId}-${persId}`).forEach(btn => {
                                btn.classList.remove('border-orange-600', 'bg-orange-100');
                            });
                            document.querySelector(`.serigraphy-color-btn-${itemId}-${persId}[data-colors="${count}"]`)
                                .classList.add('border-orange-600', 'bg-orange-100');
                            
                            window['updateSerigraphyPreview{{ $itemId }}{{ $persId }}']();
                        };

                        window['updateSerigraphyPreview{{ $itemId }}{{ $persId }}'] = function() {
                            const data = window.item{{ $itemId }}{{ $persId }}Data;
                            const {itemId, persId} = data;
                            
                            const sizeSelect = document.getElementById(`serigraphy-size-${itemId}-${persId}`);
                            const colorCount = parseInt(document.getElementById(`serigraphy-color-count-${itemId}-${persId}`).value) || 1;
                            const hasNeon = document.getElementById(`serigraphy-neon-${itemId}-${persId}`).checked;
                            
                            if (!sizeSelect.value) {
                                document.getElementById(`preview-base-${itemId}-${persId}`).textContent = 'R$ 0,00';
                                document.getElementById(`preview-colors-${itemId}-${persId}`).textContent = 'R$ 0,00';
                                document.getElementById(`preview-neon-${itemId}-${persId}`).textContent = 'R$ 0,00';
                                document.getElementById(`preview-total-${itemId}-${persId}`).textContent = 'R$ 0,00';
                                return;
                            }
                            
                            const basePrice = parseFloat(sizeSelect.options[sizeSelect.selectedIndex].dataset.price) || 0;
                            const colorPrice = colorCount * 5; // R$ 5 por cor
                            const neonPrice = hasNeon ? (basePrice * 0.5) : 0;
                            const totalPrice = basePrice + colorPrice + neonPrice;
                            
                            document.getElementById(`preview-base-${itemId}-${persId}`).textContent = 
                                'R$ ' + basePrice.toFixed(2).replace('.', ',');
                            document.getElementById(`preview-colors-${itemId}-${persId}`).textContent = 
                                'R$ ' + colorPrice.toFixed(2).replace('.', ',') + ` (${colorCount}x R$ 5,00)`;
                            document.getElementById(`preview-neon-${itemId}-${persId}`).textContent = 
                                'R$ ' + neonPrice.toFixed(2).replace('.', ',');
                            document.getElementById(`preview-total-${itemId}-${persId}`).textContent = 
                                'R$ ' + totalPrice.toFixed(2).replace('.', ',');
                        };

                        window['addSerigraphyApp{{ $itemId }}{{ $persId }}'] = function() {
                            const data = window.item{{ $itemId }}{{ $persId }}Data;
                            const {itemId, persId} = data;
                            
                            const sizeSelect = document.getElementById(`serigraphy-size-${itemId}-${persId}`);
                            const colorCount = parseInt(document.getElementById(`serigraphy-color-count-${itemId}-${persId}`).value) || 1;
                            const hasNeon = document.getElementById(`serigraphy-neon-${itemId}-${persId}`).checked;
                            const locationSelect = document.getElementById(`serigraphy-location-${itemId}-${persId}`);
                            const imageInput = document.getElementById(`serigraphy-image-${itemId}-${persId}`);
                            
                            if (!sizeSelect.value || !locationSelect.value) {
                                alert('Preencha tamanho e localização');
                                return;
                            }

                            const basePrice = parseFloat(sizeSelect.options[sizeSelect.selectedIndex].dataset.price);
                            const colorPrice = colorCount * 5;
                            const neonPrice = hasNeon ? (basePrice * 0.5) : 0;
                            const totalPrice = basePrice + colorPrice + neonPrice;

                            // Criar input file hidden para esta aplicação usando DataTransfer
                            const appIndex = data.applications.length;
                            let hasImage = false;
                            
                            if (imageInput.files && imageInput.files[0]) {
                                hasImage = true;
                                const form = document.getElementById(`form-${itemId}-${persId}`);
                                const hiddenFileInput = document.createElement('input');
                                hiddenFileInput.type = 'file';
                                hiddenFileInput.name = `application_image_${appIndex}`;
                                hiddenFileInput.style.display = 'none';
                                
                                // Usar DataTransfer para copiar o arquivo
                                const dataTransfer = new DataTransfer();
                                dataTransfer.items.add(imageInput.files[0]);
                                hiddenFileInput.files = dataTransfer.files;
                                
                                form.appendChild(hiddenFileInput);
                            }

                            data.applications.push({
                                type: 'serigraphy',
                                size: sizeSelect.options[sizeSelect.selectedIndex].text.split('(')[0].trim(),
                                colorCount: colorCount,
                                hasNeon: hasNeon,
                                location: locationSelect.options[locationSelect.selectedIndex].text,
                                basePrice: basePrice,
                                colorPrice: colorPrice,
                                neonPrice: neonPrice,
                                price: totalPrice,
                                hasImage: hasImage,
                                imageName: hasImage ? imageInput.files[0].name : null
                            });

                            // Reset
                            sizeSelect.value = '';
                            locationSelect.value = '';
                            imageInput.value = '';
                            document.getElementById(`serigraphy-color-count-${itemId}-${persId}`).value = '1';
                            document.getElementById(`serigraphy-neon-${itemId}-${persId}`).checked = false;
                            document.querySelectorAll(`.serigraphy-color-btn-${itemId}-${persId}`).forEach(btn => {
                                btn.classList.remove('border-orange-600', 'bg-orange-100');
                            });
                            window['updateSerigraphyPreview{{ $itemId }}{{ $persId }}']();
                            
                            window['renderApplicationsList{{ $itemId }}{{ $persId }}']();
                            window['updateSummary{{ $itemId }}{{ $persId }}']();
                        };

                        // Carregar preços iniciais para todos os tamanhos
                        window['loadInitialPrices{{ $itemId }}{{ $persId }}'] = async function() {
                            const data = window.item{{ $itemId }}{{ $persId }}Data;
                            const {itemId, persId, persName, itemQuantity} = data;
                            
                            // Determinar o tipo de personalização
                            let type = '';
                            if (persName.toLowerCase().includes('sublim')) {
                                type = 'SUBLIMACAO';
                            } else if (persName === 'DTF') {
                                type = 'DTF';
                            } else if (persName === 'BORDADO') {
                                type = 'BORDADO';
                            } else if (persName === 'SERIGRAFIA') {
                                type = 'SERIGRAFIA';
                            }
                            
                            if (!type) return;
                            
                            // Buscar todos os tamanhos e seus preços
                            const sizeSelect = document.getElementById(`${type.toLowerCase()}-size-${itemId}-${persId}`) ||
                                             document.getElementById(`sublimation-size-${itemId}-${persId}`) ||
                                             document.getElementById(`dtf-size-${itemId}-${persId}`) ||
                                             document.getElementById(`embroidery-size-${itemId}-${persId}`) ||
                                             document.getElementById(`serigraphy-size-${itemId}-${persId}`);
                            
                            if (!sizeSelect) return;
                            
                            // Carregar preço para cada opção
                            const options = Array.from(sizeSelect.options).filter(opt => opt.value);
                            
                            for (const option of options) {
                                try {
                                    const response = await fetch(`/api/personalization-prices/price?type=${type}&size=${encodeURIComponent(option.value)}&quantity=${itemQuantity}`);
                                    const result = await response.json();
                                    
                                    if (result.success) {
                                        option.dataset.price = result.price;
                                        const dimensions = result.size_dimensions ? ` (${result.size_dimensions})` : '';
                                        option.text = `${result.size_name}${dimensions} - R$ ${parseFloat(result.price).toFixed(2).replace('.', ',')}`;
                                    } else {
                                        option.text = `${option.value} - Preço não encontrado`;
                                    }
                                } catch (error) {
                                    console.error('Erro ao carregar preço:', error);
                                    option.text = `${option.value} - Erro`;
                                }
                            }
                        };
                        
                        // Inicializar
                        window['renderApplicationsList{{ $itemId }}{{ $persId }}']();
                        window['updateSummary{{ $itemId }}{{ $persId }}']();
                        
                        // Carregar preços iniciais
                        setTimeout(() => {
                            window['loadInitialPrices{{ $itemId }}{{ $persId }}']();
                        }, 100);
                    </script>
                </div>
                @endforeach
            @endforeach

            <!-- Resumo Geral do Pedido -->
            <div class="bg-gradient-to-r from-green-50 to-green-100 border-2 border-green-300 rounded-lg p-6 mt-8">
                <h3 class="font-bold text-xl mb-4 text-green-800">💰 Resumo Geral do Pedido</h3>
                <div class="space-y-3">
                    <div class="flex justify-between text-lg">
                        <span>Total de Itens:</span>
                        <span class="font-bold">{{ $order->items->count() }}</span>
                    </div>
                    <div class="flex justify-between text-lg">
                        <span>Total de Peças:</span>
                        <span class="font-bold">{{ $order->items->sum('quantity') }} peças</span>
                    </div>
                    <div class="flex justify-between text-lg border-t pt-3">
                        <span>Subtotal (Peças):</span>
                        <span class="font-bold">R$ {{ number_format($order->subtotal, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-lg">
                        <span>Total de Aplicações:</span>
                        <span class="font-bold" id="grand-total-apps">0</span>
                    </div>
                    <div class="flex justify-between text-lg">
                        <span>Valor das Aplicações:</span>
                        <span class="font-bold" id="grand-total-apps-value">R$ 0,00</span>
                    </div>
                    <div class="flex justify-between text-2xl font-bold text-green-700 border-t-2 pt-4">
                        <span>TOTAL DO PEDIDO:</span>
                        <span id="grand-total-order">R$ {{ number_format($order->subtotal, 2, ',', '.') }}</span>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mt-4 italic">
                    * Este é o valor estimado. Acréscimos, descontos e frete serão calculados na próxima etapa.
                </p>
            </div>

            <!-- Instruções e Botão de Avançar -->
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mt-6 rounded-r-lg">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm text-blue-700">
                            <strong>Como prosseguir:</strong> Preencha todas as abas de personalização acima, salvando cada uma individualmente. 
                            Depois que todas estiverem completas (com ✓ verde), clique no botão abaixo para ir para a etapa de Pagamento.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex justify-between items-center mt-6 pt-4 border-t">
                <a href="{{ route('orders.wizard.sewing') }}" class="px-4 py-2 text-gray-600 hover:text-gray-900">
                    ← Voltar para Costura
                </a>
                <form method="GET" action="{{ route('orders.wizard.payment') }}" class="inline">
                    <button type="submit" 
                            class="px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 font-bold text-lg shadow-lg hover:shadow-xl transition-all">
                        Avançar para Pagamento (Etapa 4) →
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Atualizar resumo geral do pedido
        function updateGrandTotal() {
            // Este será chamado quando qualquer aplicação for adicionada/removida
            // Por enquanto mostra apenas o subtotal das peças
            // Será atualizado dinamicamente quando implementarmos a soma total
        }
    </script>

    <script>
        // Função para mostrar/ocultar abas
        function showTab(tabId) {
            // Ocultar todos os conteúdos
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Remover active de todos os botões
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('border-indigo-500', 'text-indigo-600');
                button.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
            });
            
            // Mostrar o conteúdo selecionado
            const selectedContent = document.getElementById('tab-' + tabId);
            if (selectedContent) {
                selectedContent.classList.remove('hidden');
            }
            
            // Ativar o botão selecionado
            const selectedButton = document.querySelector(`[data-tab="${tabId}"]`);
            if (selectedButton) {
                selectedButton.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
                selectedButton.classList.add('border-indigo-500', 'text-indigo-600');
            }
        }

        // Mostrar a primeira aba ao carregar
        document.addEventListener('DOMContentLoaded', function() {
            const firstButton = document.querySelector('.tab-button');
            if (firstButton) {
                const firstTabId = firstButton.getAttribute('data-tab');
                showTab(firstTabId);
            }
        });
    </script>
</body>
</html>

