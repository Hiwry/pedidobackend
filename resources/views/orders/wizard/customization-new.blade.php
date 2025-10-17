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

        <!-- Main Content -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-indigo-100 rounded-md flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg font-semibold text-gray-900">Personalização dos Itens</h1>
                        <p class="text-sm text-gray-600">Configure as aplicações para cada item</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <form method="POST" action="{{ route('orders.wizard.customization') }}" id="customization-form" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    <input type="hidden" name="total_shirts" value="{{ session('total_shirts', 0) }}" id="total-shirts">
                    <input type="hidden" name="sublimations" id="sublimations-data">

                    <!-- Informações do Pedido -->
                    @if($order->items->count() > 1)
                    <div class="space-y-3">
                        <div class="flex items-center space-x-2 mb-3">
                            <div class="w-5 h-5 bg-indigo-100 rounded-md flex items-center justify-center">
                                <svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <h2 class="text-sm font-medium text-gray-900">Selecione o Item</h2>
                        </div>

                        <div class="bg-gray-50 rounded-md p-4">
                            <select id="selected-item" onchange="updateItemInfo()" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                                @foreach($order->items as $item)
                                <option value="{{ $item->id }}" 
                                        data-personalizacao="{{ $item->print_type }}"
                                        data-quantidade="{{ $item->quantity }}">
                                    Item {{ $item->item_number }} - {{ $item->print_type }} ({{ $item->quantity }} peças)
                                </option>
                                @endforeach
                            </select>
                            
                            <div id="item-details" class="mt-3 p-3 bg-white rounded border border-gray-200 text-sm">
                                <!-- Será preenchido via JavaScript -->
                            </div>
                        </div>
                    </div>
                    @else
                    <input type="hidden" id="selected-item" value="{{ $order->items->first()->id }}">
                    @endif

                    <!-- Nome da Arte -->
                    <div class="space-y-3">
                        <div class="flex items-center space-x-2 mb-3">
                            <div class="w-5 h-5 bg-indigo-100 rounded-md flex items-center justify-center">
                                <svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <h2 class="text-sm font-medium text-gray-900">Nome da Arte *</h2>
                        </div>

                        <div class="bg-gray-50 rounded-md p-4">
                            <input type="text" id="art_name" name="art_name" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                                   placeholder="Ex: Logo Empresa - DTF">
                        </div>
                    </div>


                    <!-- Arquivos da Arte -->
                    <div class="space-y-3">
                        <div class="flex items-center space-x-2 mb-3">
                            <div class="w-5 h-5 bg-indigo-100 rounded-md flex items-center justify-center">
                                <svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <h2 class="text-sm font-medium text-gray-900">Arquivos da Arte *</h2>
                        </div>

                        <div class="bg-gray-50 rounded-md p-4">
                            <input type="file" id="art_files" name="art_files[]" multiple
                                   required class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100"
                                   onchange="displayFileList()">
                            <p class="text-xs text-gray-500 mt-2">Múltiplos arquivos: AI, PDF, PNG, JPG, CDR - Obrigatório pelo menos 1 arquivo</p>
                            <div id="file-list" class="mt-3 space-y-2"></div>
                        </div>
                    </div>

                    <!-- Tamanhos de Aplicação -->
                    <div class="space-y-3">
                        <div class="flex items-center space-x-2 mb-3">
                            <div class="w-5 h-5 bg-indigo-100 rounded-md flex items-center justify-center">
                                <svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            <h2 class="text-sm font-medium text-gray-900">Adicionar Aplicações</h2>
                        </div>

                        <div class="bg-gray-50 rounded-md p-4">
                            <p class="text-xs text-gray-600 mb-3">Selecione o tamanho da aplicação</p>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3" id="size-buttons">
                                <!-- Será preenchido via JavaScript -->
                            </div>
                        </div>
                    </div>

                    <!-- Aplicações Adicionadas -->
                    <div class="space-y-3">
                        <div class="flex items-center space-x-2 mb-3">
                            <div class="w-5 h-5 bg-indigo-100 rounded-md flex items-center justify-center">
                                <svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                            </div>
                            <h2 class="text-sm font-medium text-gray-900">Aplicações Adicionadas</h2>
                        </div>

                        <div class="bg-gray-50 rounded-md p-4">
                            <p class="text-sm text-gray-500 mb-3" id="no-applications">Nenhuma aplicação adicionada</p>
                            <div id="applications-list" class="space-y-2">
                                <!-- Será preenchido via JavaScript -->
                            </div>
                        </div>
                    </div>

                    <!-- Resumo -->
                    <div class="bg-indigo-50 rounded-md p-4 border border-indigo-200">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-medium text-gray-900">Resumo</h3>
                            <div class="text-xs text-gray-600">
                                Total de peças: <span class="font-medium text-gray-900">{{ session('total_shirts', 0) }}</span>
                            </div>
                        </div>
                        <div id="price-breakdown" class="space-y-1 text-sm text-gray-600 mb-3">
                            <!-- Será preenchido via JavaScript -->
                        </div>
                        <div class="flex justify-between items-center pt-3 border-t border-indigo-200">
                            <span class="text-sm font-medium text-gray-900">Total:</span>
                            <span id="total-price" class="text-lg font-bold text-indigo-600">R$ 0,00</span>
                        </div>
                    </div>

                    <!-- Botões de Navegação -->
                    <div class="flex justify-between pt-6 border-t border-gray-200">
                        <a href="{{ route('orders.wizard.sewing') }}" 
                           class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Voltar
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 flex items-center">
                            Continuar
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Minimalista -->
    <div id="application-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg max-w-md w-full shadow-xl">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <span id="modal-size-name"></span>
                </h3>
            </div>

            <!-- Modal Body -->
            <div class="px-6 py-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Local da Aplicação *</label>
                    <select id="modal-location" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        <option value="">Selecione</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantidade *</label>
                    <input type="number" id="modal-quantity" min="1" value="1" 
                           onchange="updateModalPrices()"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                </div>

                @if(auth()->check() && auth()->user()->isAdmin())
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Valor Unitário (R$) *</label>
                    <input type="number" id="modal-unit-price-input" step="0.01" min="0" value="0" 
                           onchange="updateModalPriceFromInput()"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                </div>
                @endif

                <div class="bg-gray-50 rounded-md p-3 space-y-1 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Valor unitário:</span>
                        <span class="font-medium text-gray-900" id="modal-unit-price">R$ 0,00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-medium text-gray-900" id="modal-subtotal">R$ 0,00</span>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" onclick="closeModal()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900">
                    Cancelar
                </button>
                <button type="button" onclick="addApplication()" 
                        class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                    Adicionar
                </button>
            </div>
        </div>
    </div>

    <script>
        let sizes = [];
        let locations = [];
        let applications = [];
        let totalShirts = {{ session('total_shirts', 0) }};
        let currentSize = '';
        let isAdmin = {{ auth()->check() && auth()->user()->isAdmin() ? 'true' : 'false' }};

        document.addEventListener('DOMContentLoaded', function() {
            loadData();
            @if($order->items->count() > 1)
            updateItemInfo();
            @endif
        });

        function updateItemInfo() {
            const select = document.getElementById('selected-item');
            if (!select) return;
            
            const option = select.options[select.selectedIndex];
            const details = document.getElementById('item-details');
            
            if (details && option && option.dataset) {
                details.innerHTML = `
                    <p><strong>Personalização:</strong> ${option.dataset.personalizacao}</p>
                    <p><strong>Quantidade:</strong> ${option.dataset.quantidade} peças</p>
                `;
            }
        }

        function loadData() {
            Promise.all([
                fetch('/api/sublimation-sizes').then(r => r.json()),
                fetch('/api/sublimation-locations').then(r => r.json())
            ]).then(([sizesData, locationsData]) => {
                sizes = sizesData;
                locations = locationsData;
                renderSizeButtons();
                renderLocationOptions();
            });
        }

        function renderSizeButtons() {
            const container = document.getElementById('size-buttons');
            container.innerHTML = sizes.map(size => `
                <button type="button" onclick="openModal(${size.id})" 
                        class="p-3 border border-gray-300 rounded-md hover:border-indigo-500 hover:bg-indigo-50 transition text-center">
                    <div class="font-medium text-sm">${size.name}</div>
                    <div class="text-xs text-gray-500 mt-1">${size.dimensions || ''}</div>
                </button>
            `).join('');
        }

        function renderLocationOptions() {
            const select = document.getElementById('modal-location');
            select.innerHTML = '<option value="">Selecione</option>' + 
                locations.map(loc => `<option value="${loc.id}">${loc.name}</option>`).join('');
        }

        function openModal(sizeId) {
            currentSize = sizes.find(s => s.id === sizeId);
            const dimensions = currentSize.dimensions || '';
            document.getElementById('modal-size-name').textContent = dimensions ? `${currentSize.name} (${dimensions})` : currentSize.name;
            document.getElementById('modal-quantity').value = 1;
            
            fetch(`/api/sublimation-price/${sizeId}/${totalShirts}`)
                .then(r => r.json())
                .then(data => {
                    currentSize.price = parseFloat(data.price);
                    updateModalPrices();
                });

            document.getElementById('application-modal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('application-modal').classList.add('hidden');
            document.getElementById('modal-location').value = '';
            document.getElementById('modal-quantity').value = 1;
        }

        function updateModalPrices() {
            const quantity = parseInt(document.getElementById('modal-quantity').value) || 1;
            const unitPrice = currentSize.price;
            const subtotal = unitPrice * quantity;

            document.getElementById('modal-unit-price').textContent = `R$ ${unitPrice.toFixed(2).replace('.', ',')}`;
            document.getElementById('modal-subtotal').textContent = `R$ ${subtotal.toFixed(2).replace('.', ',')}`;
            if (document.getElementById('modal-unit-price-input')) {
                document.getElementById('modal-unit-price-input').value = unitPrice.toFixed(2);
            }
        }

        function updateModalPriceFromInput() {
            if (!isAdmin) return;
            const inputPrice = parseFloat(document.getElementById('modal-unit-price-input').value) || 0;
            currentSize.price = inputPrice;
            updateModalPrices();
        }

        function addApplication() {
            const locationId = document.getElementById('modal-location').value;
            const quantity = parseInt(document.getElementById('modal-quantity').value);

            if (!locationId || !quantity) {
                alert('Preencha todos os campos obrigatórios');
                return;
            }

            const location = locations.find(l => l.id == locationId);
            const unitPrice = currentSize.price;
            const subtotal = unitPrice * quantity;

            applications.push({
                size_id: currentSize.id,
                size_name: currentSize.name,
                size_dimensions: currentSize.dimensions,
                location_id: locationId,
                location_name: location.name,
                quantity: quantity,
                unit_price: unitPrice,
                subtotal: subtotal
            });

            renderApplications();
            updatePriceBreakdown();
            closeModal();
        }

        function renderApplications() {
            const container = document.getElementById('applications-list');
            const noApps = document.getElementById('no-applications');
            
            if (applications.length === 0) {
                noApps.classList.remove('hidden');
                container.innerHTML = '';
                return;
            }

            noApps.classList.add('hidden');
            container.innerHTML = applications.map((app, index) => `
                <div class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-md">
                    <div class="flex-1">
                        <div class="text-sm font-medium text-gray-900">${app.size_name} - ${app.location_name}</div>
                        <div class="text-xs text-gray-500">${app.quantity}x R$ ${app.unit_price.toFixed(2).replace('.', ',')}</div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="text-sm font-medium text-gray-900">R$ ${app.subtotal.toFixed(2).replace('.', ',')}</span>
                        <button type="button" onclick="removeApplication(${index})" 
                                class="text-red-600 hover:text-red-800">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            `).join('');
        }

        function removeApplication(index) {
            applications.splice(index, 1);
            renderApplications();
            updatePriceBreakdown();
        }

        function updatePriceBreakdown() {
            const total = applications.reduce((sum, app) => sum + app.subtotal, 0);
            
            document.getElementById('total-price').textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
            
            const breakdown = document.getElementById('price-breakdown');
            if (applications.length === 0) {
                breakdown.innerHTML = '<p class="text-sm text-gray-500">Nenhuma aplicação adicionada</p>';
            } else {
                breakdown.innerHTML = applications.map(app => 
                    `<div class="flex justify-between">
                        <span>${app.size_name} - ${app.location_name} (${app.quantity}x)</span>
                        <span>R$ ${app.subtotal.toFixed(2).replace('.', ',')}</span>
                    </div>`
                ).join('');
            }
        }

        function previewCoverImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('cover-preview-img').src = e.target.result;
                    document.getElementById('cover-preview').classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        }

        function displayFileList() {
            const files = document.getElementById('art_files').files;
            const container = document.getElementById('file-list');
            
            if (files.length === 0) {
                container.innerHTML = '';
                return;
            }

            container.innerHTML = Array.from(files).map(file => `
                <div class="flex items-center p-2 bg-white border border-gray-200 rounded text-sm">
                    <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="text-gray-700">${file.name}</span>
                    <span class="ml-auto text-xs text-gray-500">${(file.size / 1024).toFixed(1)} KB</span>
                </div>
            `).join('');
        }

        // Submeter formulário
        document.getElementById('customization-form').addEventListener('submit', function(e) {
            document.getElementById('sublimations-data').value = JSON.stringify(applications);
        });
    </script>
</body>
</html>

