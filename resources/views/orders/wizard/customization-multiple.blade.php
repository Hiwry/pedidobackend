<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Novo Pedido - Personalização</title>
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
                    <div class="w-6 h-6 bg-indigo-600 text-white rounded-full flex items-center justify-center text-xs font-medium">3</div>
                    <div>
                        <span class="text-base font-medium text-indigo-600">Personalização</span>
                        <p class="text-xs text-gray-500">Etapa 3 de 5</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-xs text-gray-500">Progresso</div>
                    <div class="text-sm font-medium text-indigo-600">60%</div>
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-1.5">
                <div class="bg-indigo-600 h-1.5 rounded-full transition-all duration-500 ease-out" style="width: 60%"></div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-indigo-100 rounded-md flex items-center justify-center">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-lg font-semibold text-gray-900">Personalizações</h1>
                            <p class="text-sm text-gray-600">Configure as personalizações de cada item</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6">
                
                @if(session('success'))
                    <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-800 rounded-md text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-800 rounded-md text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Resumo -->
                <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Total de Itens</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $order->items->count() }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Total de Peças</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $order->items->sum('quantity') }}</p>
                        </div>
                        @php
                            $totalApplications = 0;
                            $totalApplicationsCount = 0;
                            foreach($order->items as $item) {
                                $itemApplications = \App\Models\OrderSublimation::where('order_item_id', $item->id)->get();
                                $totalApplications += $itemApplications->sum('final_price');
                                $totalApplicationsCount += $itemApplications->count();
                            }
                            $avgPerPiece = $order->items->sum('quantity') > 0 ? $totalApplications / $order->items->sum('quantity') : 0;
                        @endphp
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Total de Aplicações</p>
                            <p class="text-lg font-semibold text-indigo-600">R$ {{ number_format($totalApplications, 2, ',', '.') }}</p>
                            <p class="text-xs text-gray-400">{{ $totalApplicationsCount }} {{ $totalApplicationsCount == 1 ? 'aplicação' : 'aplicações' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Custo por Peça</p>
                            <p class="text-lg font-semibold text-indigo-600">R$ {{ number_format($avgPerPiece, 2, ',', '.') }}</p>
                            <p class="text-xs text-gray-400">Média das aplicações</p>
                        </div>
                    </div>
                </div>

                <!-- Lista de Itens -->
                <div class="space-y-6">
                    @foreach($itemPersonalizations as $itemData)
                        @php
                            $item = $itemData['item'];
                            $persIds = $itemData['personalization_ids'];
                            $persNames = $itemData['personalization_names'];
                        @endphp
                        
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            
                            <!-- Item Header -->
                            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <h3 class="font-medium text-gray-900">Item {{ $item->item_number }}</h3>
                                        <p class="text-sm text-gray-600 mt-0.5">{{ $item->quantity }} peças • {{ $item->fabric }} • {{ $item->color }}</p>
                                    </div>
                                    <span class="text-xs px-2 py-1 bg-indigo-100 text-indigo-700 rounded">
                                        {{ count($persIds) }} {{ count($persIds) == 1 ? 'personalização' : 'personalizações' }}
                                    </span>
                                </div>
                                @php
                                    $itemTotalApplications = \App\Models\OrderSublimation::where('order_item_id', $item->id)->sum('final_price');
                                    $itemApplicationsCount = \App\Models\OrderSublimation::where('order_item_id', $item->id)->count();
                                    $itemAvgPerPiece = $item->quantity > 0 ? $itemTotalApplications / $item->quantity : 0;
                                @endphp
                                @if($itemApplicationsCount > 0)
                                    <div class="grid grid-cols-3 gap-3 text-xs bg-white rounded p-2">
                                        <div>
                                            <span class="text-gray-500">Aplicações:</span>
                                            <span class="font-semibold text-gray-900 ml-1">{{ $itemApplicationsCount }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Total:</span>
                                            <span class="font-semibold text-indigo-600 ml-1">R$ {{ number_format($itemTotalApplications, 2, ',', '.') }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Por peça:</span>
                                            <span class="font-semibold text-indigo-600 ml-1">R$ {{ number_format($itemAvgPerPiece, 2, ',', '.') }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Personalizações do Item -->
                            <div class="p-4 space-y-4">
                                @foreach($persIds as $persId)
                                    @php
                                        $persName = $persNames[$persId] ?? 'Personalização';
                                        $existingPersonalizations = \App\Models\OrderSublimation::where('order_item_id', $item->id)
                                            ->where('application_type', strtolower($persName))
                                            ->with('files')
                                            ->get();
                                    @endphp
                                    
                                    <div class="bg-white border border-gray-200 rounded-md p-4">
                                        
                                        <!-- Tipo de Personalização -->
                                        <div class="flex items-center justify-between mb-4">
                                            <div class="flex items-center space-x-2">
                                                <div class="w-6 h-6 bg-indigo-100 rounded flex items-center justify-center">
                                                    <svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                                <h4 class="font-medium text-gray-900">{{ $persName }}</h4>
                                            </div>
                                            <button 
                                                type="button"
                                                onclick="openPersonalizationModal({{ $item->id }}, '{{ $persName }}', {{ $persId }})"
                                                class="text-sm px-3 py-1.5 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors">
                                                + Adicionar
                                            </button>
                                        </div>
                                        
                                        <!-- Lista de Personalizações Adicionadas -->
                                        <div id="personalizations-list-{{ $item->id }}-{{ $persId }}" class="space-y-2">
                                            @if($existingPersonalizations->count() > 0)
                                                @foreach($existingPersonalizations as $pers)
                                                    <div class="p-3 bg-gray-50 rounded border border-gray-200">
                                                        <div class="flex items-center justify-between">
                                                            <div class="flex-1">
                                                                @if($pers->art_name)
                                                                    <div class="text-sm font-semibold text-indigo-700 mb-1">
                                                                        🎨 {{ $pers->art_name }}
                                                                    </div>
                                                                @endif
                                                                <div class="flex items-center space-x-4 text-sm">
                                                                    <span class="text-gray-700"><strong>Local:</strong> {{ $pers->location_name }}</span>
                                                                    <span class="text-gray-700"><strong>Tamanho:</strong> {{ $pers->size_name }}</span>
                                                                    <span class="text-gray-700"><strong>Qtd:</strong> {{ $pers->quantity }}</span>
                                                                    @if($pers->color_count)
                                                                        <span class="text-gray-700"><strong>Cores:</strong> {{ $pers->color_count }}</span>
                                                                    @endif
                                                                    @if($pers->final_price > 0)
                                                                        <span class="text-indigo-600 font-semibold">R$ {{ number_format($pers->final_price, 2, ',', '.') }}</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <button 
                                                                type="button"
                                                                onclick="removePersonalization({{ $pers->id }})"
                                                                class="text-red-600 hover:text-red-700 ml-4">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                        
                                                        @if($pers->files && $pers->files->count() > 0)
                                                            <div class="mt-2 pt-2 border-t border-gray-200">
                                                                <div class="text-xs font-medium text-gray-600 mb-1">📁 Arquivos da Arte:</div>
                                                                <div class="flex flex-wrap gap-2">
                                                                    @foreach($pers->files as $file)
                                                                        <a href="{{ asset('storage/' . $file->file_path) }}" 
                                                                           download="{{ $file->file_name }}"
                                                                           class="inline-flex items-center px-2 py-1 bg-white border border-gray-300 rounded text-xs hover:bg-gray-100 transition-colors"
                                                                           title="{{ $file->file_name }} ({{ $file->formatted_size }})">
                                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                                                                            </svg>
                                                                            <span class="truncate max-w-xs">{{ $file->file_name }}</span>
                                                                        </a>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            @else
                                                <p class="text-sm text-gray-500 text-center py-4">Nenhuma personalização adicionada</p>
                                            @endif
                                        </div>

                                    </div>
                                @endforeach
                            </div>

                        </div>
                    @endforeach
                </div>

                <!-- Navegação -->
                <div class="mt-8 flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('orders.wizard.sewing') }}" 
                       class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        ← Voltar
                    </a>
                    <a href="{{ route('orders.wizard.payment') }}" 
                       class="px-6 py-2 text-sm bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Continuar →
                    </a>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal de Adicionar Personalização -->
    <div id="personalizationModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white">
                <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">Adicionar Personalização</h3>
                <button type="button" onclick="closePersonalizationModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Modal Body -->
            <form id="personalizationForm" class="p-6 space-y-4">
                <input type="hidden" id="modal_item_id" name="item_id">
                <input type="hidden" id="modal_personalization_type" name="personalization_type">
                <input type="hidden" id="modal_personalization_id" name="personalization_id">

                <!-- Nome da Arte -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome da Arte</label>
                    <input type="text" id="art_name" name="art_name" placeholder="Ex: Logo Empresa, Arte Costas, etc." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="mt-1 text-xs text-gray-500">Este nome aparecerá no kanban de produção</p>
                </div>

                <!-- Localização -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Localização</label>
                    <select id="location" name="location" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Selecione...</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Tamanho -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tamanho</label>
                    <select id="size" name="size" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Selecione...</option>
                    </select>
                </div>

                <!-- Quantidade -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantidade</label>
                    <input type="number" id="quantity" name="quantity" min="1" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Cores (para Serigrafia) -->
                <div id="colorCountField" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Número de Cores</label>
                    <input type="number" id="color_count" name="color_count" min="1" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Preço Calculado -->
                <div id="priceDisplay" class="hidden">
                    <div class="bg-indigo-50 border border-indigo-200 rounded-md p-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Preço por Aplicação:</span>
                            <span class="text-lg font-bold text-indigo-600" id="unitPrice">R$ 0,00</span>
                        </div>
                        <div class="flex items-center justify-between mt-1">
                            <span class="text-xs text-gray-600">Total desta Aplicação:</span>
                            <span class="text-sm font-semibold text-gray-900" id="totalPrice">R$ 0,00</span>
                        </div>
                        <div class="text-xs text-gray-500 mt-2 text-center" id="priceFormula">
                            <!-- Será preenchido via JavaScript -->
                        </div>
                    </div>
                </div>
                <input type="hidden" id="unit_price" name="unit_price" value="0">
                <input type="hidden" id="final_price" name="final_price" value="0">

                <!-- Upload de Imagem -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Imagem da Arte (opcional)</label>
                    <input type="file" id="application_image" name="application_image" accept="image/*"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="mt-1 text-xs text-gray-500">Apenas para visualização rápida</p>
                </div>

                <!-- Upload de Arquivos (CorelDRAW, PDF, Excel, etc.) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Arquivos da Arte (CorelDRAW, PDF, Excel, etc.)</label>
                    <input type="file" id="art_files" name="art_files[]" multiple
                           accept=".cdr,.pdf,.ai,.eps,.svg,.xlsx,.xls,.doc,.docx,.zip,.rar,.png,.jpg,.jpeg"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="mt-1 text-xs text-gray-500">Você pode selecionar múltiplos arquivos. Aceita: CDR, PDF, AI, EPS, SVG, Excel, Word, ZIP, imagens</p>
                    <div id="selected_files_list" class="mt-2 space-y-1"></div>
                </div>

                <!-- Botões -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                    <button type="button" onclick="closePersonalizationModal()" 
                            class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-6 py-2 text-sm bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Adicionar
                    </button>
                </div>
            </form>
            
        </div>
    </div>

    <script>
        let currentItemId = null;
        let currentPersonalizationType = null;
        let currentPersonalizationId = null;

        // Dados de tamanhos por tipo
        const personalizationSizes = @json($personalizationData);

        function openPersonalizationModal(itemId, persType, persId) {
            currentItemId = itemId;
            currentPersonalizationType = persType;
            currentPersonalizationId = persId;
            
            document.getElementById('modal_item_id').value = itemId;
            document.getElementById('modal_personalization_type').value = persType;
            document.getElementById('modal_personalization_id').value = persId;
            document.getElementById('modalTitle').textContent = `Adicionar ${persType}`;
            
            // Mostrar/ocultar campo de cores
            if (persType === 'SERIGRAFIA') {
                document.getElementById('colorCountField').classList.remove('hidden');
            } else {
                document.getElementById('colorCountField').classList.add('hidden');
            }
            
            // Carregar tamanhos
            loadSizes(persType);
            
            // Limpar formulário
            document.getElementById('personalizationForm').reset();
            document.getElementById('modal_item_id').value = itemId;
            document.getElementById('modal_personalization_type').value = persType;
            document.getElementById('modal_personalization_id').value = persId;
            
            // Mostrar modal
            document.getElementById('personalizationModal').classList.remove('hidden');
        }

        function closePersonalizationModal() {
            document.getElementById('personalizationModal').classList.add('hidden');
            currentItemId = null;
            currentPersonalizationType = null;
            currentPersonalizationId = null;
        }

        function loadSizes(persType) {
            const sizeSelect = document.getElementById('size');
            sizeSelect.innerHTML = '<option value="">Selecione...</option>';
            
            // Mapear tipo para chave correta
            let typeKey = persType;
            if (persType === 'SUB. LOCAL' || persType === 'SUB. TOTAL') {
                typeKey = 'SUBLIMACAO';
            }
            
            if (personalizationSizes[typeKey] && personalizationSizes[typeKey].sizes) {
                personalizationSizes[typeKey].sizes.forEach(size => {
                    const option = document.createElement('option');
                    option.value = size.size_name;
                    option.textContent = `${size.size_name} (${size.size_dimensions})`;
                    sizeSelect.appendChild(option);
                });
            }
        }

        // Calcular preço
        async function calculatePrice() {
            const persType = document.getElementById('modal_personalization_type').value;
            const size = document.getElementById('size').value;
            const quantity = document.getElementById('quantity').value;
            
            if (!persType || !size || !quantity) {
                document.getElementById('priceDisplay').classList.add('hidden');
                return;
            }
            
            // Mapear tipo para API
            let apiType = persType;
            if (persType === 'SUB. LOCAL') apiType = 'SUBLIMACAO';
            if (persType === 'SUB. TOTAL') apiType = 'SUBLIMACAO_TOTAL';
            
            try {
                console.log('Buscando preço:', { apiType, size, quantity });
                const response = await fetch(`/api/personalization-prices/price?type=${apiType}&size=${encodeURIComponent(size)}&quantity=${quantity}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                console.log('Resposta da API:', response.status, response.statusText);
                const data = await response.json();
                console.log('Dados recebidos:', data);
                
                if (data.success && data.price) {
                    let unitPrice = parseFloat(data.price);
                    const qty = parseInt(quantity);
                    const colorCount = parseInt(document.getElementById('color_count')?.value || 1);
                    
                    // Para SERIGRAFIA, adicionar preço das cores adicionais e aplicar desconto
                    if (apiType === 'SERIGRAFIA') {
                        console.log('Número de cores:', colorCount);
                        
                        if (colorCount > 1) {
                            // Buscar preço da cor adicional
                            try {
                                console.log('Buscando preços de cores para quantidade:', qty);
                                const colorsResponse = await fetch('/api/serigraphy-colors', {
                                    headers: {
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                    }
                                });
                                const colorsData = await colorsResponse.json();
                                console.log('Dados de cores recebidos:', colorsData);
                                
                                // Encontrar o preço da cor para esta quantidade
                                let colorPrice = 0;
                                for (const color of colorsData) {
                                    // Verificar se o nome contém a faixa de quantidade
                                    const match = color.name.match(/\((\d+)-(\d+)\)/);
                                    if (match) {
                                        const from = parseInt(match[1]);
                                        const to = parseInt(match[2]);
                                        console.log(`Verificando cor: ${color.name}, faixa: ${from}-${to}, quantidade: ${qty}`);
                                        if (qty >= from && qty <= to) {
                                            colorPrice = parseFloat(color.price);
                                            console.log(`Preço da cor encontrado: R$ ${colorPrice} para quantidade ${qty}`);
                                            break;
                                        }
                                    }
                                }
                                
                                // Adicionar preço das cores adicionais (cor 1 já está incluída no preço base)
                                const extraColors = colorCount - 1;
                                const colorCost = colorPrice * extraColors;
                                console.log(`Cores extras: ${extraColors}, custo por cor: R$ ${colorPrice}, custo total cores: R$ ${colorCost}`);
                                unitPrice += colorCost;
                                console.log(`Preço unitário após cores: R$ ${unitPrice}`);
                            } catch (error) {
                                console.error('Erro ao buscar preços de cores:', error);
                            }
                        }
                        
                        // REGRA DE DESCONTO: 50% de desconto para aplicações a partir da terceira
                        if (colorCount >= 3) {
                            // Aplicar 50% de desconto nas aplicações a partir da terceira
                            const applicationsWithDiscount = colorCount - 2; // A partir da 3ª aplicação
                            const discountPerApplication = colorPrice * 0.5; // 50% de desconto no preço da cor
                            const totalDiscount = discountPerApplication * applicationsWithDiscount;
                            
                            // Subtrair o desconto do preço unitário
                            unitPrice -= totalDiscount;
                            
                            console.log(`Desconto aplicado: ${applicationsWithDiscount} aplicações com 50% de desconto = R$ ${totalDiscount.toFixed(2)}`);
                        }
                    }
                    
                    const total = unitPrice * qty;
                    
                    // Preparar texto da fórmula
                    let formulaText = `R$ ${unitPrice.toFixed(2).replace('.', ',')} × ${qty} ${qty === 1 ? 'peça' : 'peças'}`;
                    
                    // Se for serigrafia com desconto, mostrar na fórmula
                    if (apiType === 'SERIGRAFIA' && colorCount >= 3) {
                        const applicationsWithDiscount = colorCount - 2;
                        formulaText += ` (${applicationsWithDiscount} aplicações com 50% desconto)`;
                    }
                    
                    console.log('Exibindo preços:', { unitPrice, total, formulaText });
                    document.getElementById('unitPrice').textContent = `R$ ${unitPrice.toFixed(2).replace('.', ',')}`;
                    document.getElementById('totalPrice').textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
                    document.getElementById('priceFormula').textContent = formulaText;
                    document.getElementById('unit_price').value = unitPrice;
                    document.getElementById('final_price').value = total;
                    document.getElementById('priceDisplay').classList.remove('hidden');
                    console.log('Preços exibidos com sucesso!');
                } else {
                    document.getElementById('priceDisplay').classList.add('hidden');
                }
            } catch (error) {
                console.error('Erro ao calcular preço:', error);
                document.getElementById('priceDisplay').classList.add('hidden');
            }
        }

        // Mostrar arquivos selecionados
        function displaySelectedFiles() {
            const fileInput = document.getElementById('art_files');
            const filesList = document.getElementById('selected_files_list');
            
            if (fileInput.files.length > 0) {
                let html = '<div class="text-xs font-medium text-gray-700 mb-1">Arquivos selecionados:</div>';
                for (let i = 0; i < fileInput.files.length; i++) {
                    const file = fileInput.files[i];
                    const sizeMB = (file.size / 1024 / 1024).toFixed(2);
                    html += `
                        <div class="flex items-center justify-between text-xs bg-gray-50 px-2 py-1 rounded border border-gray-200">
                            <span class="truncate flex-1">📄 ${file.name}</span>
                            <span class="text-gray-500 ml-2">${sizeMB} MB</span>
                        </div>
                    `;
                }
                filesList.innerHTML = html;
            } else {
                filesList.innerHTML = '';
            }
        }

        // Adicionar listeners para recalcular preço
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('size').addEventListener('change', calculatePrice);
            document.getElementById('quantity').addEventListener('input', calculatePrice);
            document.getElementById('color_count')?.addEventListener('input', calculatePrice);
            document.getElementById('art_files').addEventListener('change', displaySelectedFiles);
        });

        // Submit do formulário
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('personalizationForm').addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                try {
                    const response = await fetch('{{ route("orders.wizard.customization") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (response.ok) {
                        window.location.reload();
                    } else {
                        alert(data.message || 'Erro ao adicionar personalização');
                    }
                } catch (error) {
                    console.error('Erro:', error);
                    alert('Erro ao adicionar personalização');
                }
            });

            // Fechar modal ao clicar fora
            document.getElementById('personalizationModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closePersonalizationModal();
                }
            });
        });

        // Remover personalização
        async function removePersonalization(id) {
            if (!confirm('Deseja remover esta personalização?')) {
                return;
            }
            
            try {
                const response = await fetch(`/api/personalizations/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                });
                
                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Erro ao remover personalização');
                }
            } catch (error) {
                console.error('Erro:', error);
                alert('Erro ao remover personalização');
            }
        }
    </script>
</body>
</html>
