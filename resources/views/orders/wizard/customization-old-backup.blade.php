<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Novo Pedido - Personalização</title>
    <!-- TODO: Em produção, substituir pelo Tailwind compilado via PostCSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <x-app-header />

    <div class="max-w-6xl mx-auto p-6">
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
            <h1 class="text-2xl font-semibold mb-6">Personalização - Sublimação Local</h1>
            
            @if(auth()->check() && !auth()->user()->isAdmin())
            <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <p class="text-sm text-yellow-800">
                        <strong>Modo Visualização:</strong> Apenas administradores podem editar valores e quantidades. 
                        Você pode visualizar e adicionar novas aplicações, mas não pode modificar valores existentes.
                    </p>
                </div>
            </div>
            @endif

            <form method="POST" action="{{ route('orders.wizard.customization') }}" id="customization-form" enctype="multipart/form-data">
                @csrf
                
                <input type="hidden" name="total_shirts" value="{{ session('total_shirts', 0) }}" id="total-shirts">
                <input type="hidden" name="sublimations" id="sublimations-data">

                <!-- Informações do Pedido e Seleção de Item -->
                <div class="bg-blue-50 rounded-lg p-4 mb-6 border border-blue-200">
                    <h3 class="font-semibold mb-2">Informações do Pedido</h3>
                    <p class="text-sm mb-3">Total de camisas: <strong id="display-total-shirts">{{ session('total_shirts', 0) }}</strong></p>
                    
                    @if($order->items->count() > 1)
                    <div class="mt-4 pt-3 border-t border-blue-300">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Selecione o Item para personalizar:
                        </label>
                        <select id="selected-item" onchange="updateItemInfo()" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                            @foreach($order->items as $item)
                            <option value="{{ $item->id }}" 
                                    data-personalizacao="{{ $item->print_type }}"
                                    data-tecido="{{ $item->fabric }}"
                                    data-cor="{{ $item->color }}"
                                    data-quantidade="{{ $item->quantity }}">
                                Item {{ $item->item_number }} - {{ $item->print_type }} ({{ $item->quantity }} peças)
                            </option>
                            @endforeach
                        </select>
                        
                        <div id="item-details" class="mt-3 p-3 bg-white rounded-lg border border-blue-200">
                            <!-- Será preenchido via JavaScript -->
                        </div>
                    </div>
                    @else
                    <div class="mt-3 p-3 bg-white rounded-lg border border-blue-200">
                        <p class="text-sm"><strong>Item 1:</strong> {{ $order->items->first()->print_type }}</p>
                        <p class="text-sm"><strong>Tecido:</strong> {{ $order->items->first()->fabric }}</p>
                        <p class="text-sm"><strong>Cor:</strong> {{ $order->items->first()->color }}</p>
                        <p class="text-sm"><strong>Quantidade:</strong> {{ $order->items->first()->quantity }} peças</p>
                    </div>
                    <input type="hidden" id="selected-item" value="{{ $order->items->first()->id }}">
                    @endif
                </div>

                <!-- Nome da Arte -->
                <div class="mb-6">
                    <label for="art_name" class="block text-sm font-medium text-gray-700 mb-2">Nome da Arte *</label>
                    <input type="text" id="art_name" name="art_name" required
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           placeholder="Ex: Logo Empresa XYZ">
                </div>

                <!-- Imagem de Capa -->
                <div class="mb-6">
                    <label for="cover_image" class="block text-sm font-medium text-gray-700 mb-2">Imagem de Capa do Pedido</label>
                    <input type="file" id="cover_image" name="cover_image" accept="image/*"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           onchange="previewCoverImage(event)">
                    <div id="cover-preview" class="mt-3 hidden">
                        <img id="cover-preview-img" src="" alt="Preview" class="max-w-xs rounded-lg border">
                    </div>
                </div>

                <!-- Arquivos da Arte -->
                <div class="mb-6">
                    <label for="art_files" class="block text-sm font-medium text-gray-700 mb-2">Arquivos da Arte (múltiplos)</label>
                    <input type="file" id="art_files" name="art_files[]" multiple
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           onchange="displayFileList()">
                    <p class="text-xs text-gray-500 mt-1">Você pode selecionar múltiplos arquivos (AI, PDF, PNG, JPG, etc)</p>
                    <div id="file-list" class="mt-3 space-y-2"></div>
                </div>

                <!-- Tamanhos de Aplicação -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-4">Selecione o Tamanho da Aplicação</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="size-buttons">
                        <!-- Será preenchido via JavaScript -->
                    </div>
                </div>

                <!-- Aplicações Adicionadas -->
                <div id="applications-container" class="mb-6">
                    <h3 class="text-lg font-semibold mb-4">Aplicações Adicionadas</h3>
                    <p class="text-gray-500 text-sm mb-3" id="no-applications">Nenhuma aplicação adicionada ainda.</p>
                    <div id="applications-list" class="space-y-3">
                    </div>
                </div>

                <!-- Resumo de Valores -->
                <div class="bg-green-50 rounded-lg p-4 border border-green-200 mb-6">
                    <h3 class="font-semibold mb-3">Resumo de Valores</h3>
                    <div id="price-breakdown" class="space-y-2 text-sm">
                        <!-- Será preenchido via JavaScript -->
                    </div>
                    <div class="flex justify-between font-bold text-lg pt-3 border-t mt-3">
                        <span>Total:</span>
                        <span id="total-price">R$ 0,00</span>
                    </div>
                </div>

                <div class="flex justify-between pt-4">
                    <a href="{{ route('orders.wizard.sewing') }}" class="px-4 py-2 text-gray-600 hover:text-gray-900">← Voltar</a>
                    <button type="submit" 
                            class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        Continuar →
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para adicionar aplicação -->
    <div id="application-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-xl font-semibold mb-4">Adicionar Aplicação - <span id="modal-size-name"></span></h3>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Local da Aplicação *</label>
                <select id="modal-location" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Selecione</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Quantidade de Aplicações *</label>
                <input type="number" id="modal-quantity" min="1" value="1" 
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            @if(auth()->check() && auth()->user()->isAdmin())
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Valor Unitário (R$) *</label>
                <input type="number" id="modal-unit-price-input" step="0.01" min="0" value="0" 
                       onchange="updateModalPriceFromInput()"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            @endif

            <div class="bg-gray-50 rounded p-3 mb-4">
                <div class="flex justify-between items-center mb-2">
                    <p class="text-sm text-gray-600">Valor unitário: <strong id="modal-unit-price">R$ 0,00</strong></p>
                    @if(auth()->check() && auth()->user()->isAdmin())
                    <button type="button" onclick="updatePriceByQuantity()" 
                            class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded hover:bg-blue-200">
                        🔄 Recalcular por Qtd
                    </button>
                    @endif
                </div>
                <p class="text-sm text-gray-600">Subtotal: <strong id="modal-subtotal">R$ 0,00</strong></p>
                <p class="text-xs text-gray-500 mt-1">Total de peças: <span id="modal-total-pieces">{{ session('total_shirts', 0) }}</span></p>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeModal()" 
                        class="px-4 py-2 text-gray-600 hover:text-gray-900">
                    Cancelar
                </button>
                <button type="button" onclick="addApplication()" 
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
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
        let currentSize = null;
        let isAdmin = {{ auth()->check() && auth()->user()->isAdmin() ? 'true' : 'false' }};

        document.addEventListener('DOMContentLoaded', function() {
            loadData();
            updateItemInfo();
        });

        // Função para atualizar detalhes do item selecionado
        function updateItemInfo() {
            const select = document.getElementById('selected-item');
            if (!select) return;
            
            const option = select.options ? select.options[select.selectedIndex] : null;
            const details = document.getElementById('item-details');
            
            if (details && option && option.dataset) {
                details.innerHTML = `
                    <p class="text-sm"><strong>Personalização:</strong> ${option.dataset.personalizacao}</p>
                    <p class="text-sm"><strong>Tecido:</strong> ${option.dataset.tecido}</p>
                    <p class="text-sm"><strong>Cor:</strong> ${option.dataset.cor}</p>
                    <p class="text-sm"><strong>Quantidade:</strong> ${option.dataset.quantidade} peças</p>
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
                        class="p-4 border-2 border-gray-300 rounded-lg hover:border-indigo-500 hover:bg-indigo-50 transition text-center">
                    <div class="font-bold text-lg">${size.name}</div>
                    <div class="text-sm text-gray-600">${size.dimensions}</div>
                    <div class="text-xs text-indigo-600 mt-2" id="price-${size.id}">Carregando...</div>
                </button>
            `).join('');

            // Buscar preços para a quantidade atual
            sizes.forEach(size => {
                fetch(`/api/sublimation-price/${size.id}/${totalShirts}`)
                    .then(r => r.json())
                    .then(data => {
                        document.getElementById(`price-${size.id}`).textContent = 
                            `R$ ${parseFloat(data.price).toFixed(2).replace('.', ',')}`;
                    });
            });
        }

        function renderLocationOptions() {
            const select = document.getElementById('modal-location');
            select.innerHTML = '<option value="">Selecione</option>' + 
                locations.map(loc => `<option value="${loc.id}">${loc.name}</option>`).join('');
        }

        function openModal(sizeId) {
            console.log('Opening modal for size:', sizeId);
            currentSize = sizes.find(s => s.id === sizeId);
            console.log('Current size:', currentSize);
            
            document.getElementById('modal-size-name').textContent = `${currentSize.name} (${currentSize.dimensions})`;
            document.getElementById('modal-quantity').value = 1;
            
            // Buscar preço baseado na quantidade total de camisas
            fetch(`/api/sublimation-price/${sizeId}/${totalShirts}`)
                .then(r => r.json())
                .then(data => {
                    console.log('Price data received:', data);
                    currentSize.price = parseFloat(data.price);
                    console.log('Current size price set to:', currentSize.price);
                    updateModalPrices();
                })
                .catch(error => {
                    console.error('Error fetching price:', error);
                });

            document.getElementById('application-modal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('application-modal').classList.add('hidden');
            document.getElementById('modal-location').value = '';
            document.getElementById('modal-quantity').value = 1;
            window.editingIndex = undefined;
        }

        function updateModalPrices() {
            const quantity = parseInt(document.getElementById('modal-quantity').value) || 1;
            const unitPrice = currentSize.price;
            const subtotal = unitPrice * quantity;

            document.getElementById('modal-unit-price').textContent = `R$ ${unitPrice.toFixed(2).replace('.', ',')}`;
            document.getElementById('modal-subtotal').textContent = `R$ ${subtotal.toFixed(2).replace('.', ',')}`;
            document.getElementById('modal-unit-price-input').value = unitPrice.toFixed(2);
        }

        function updatePriceByQuantity() {
            if (!isAdmin) {
                alert('Apenas administradores podem recalcular preços.');
                return;
            }
            const quantity = parseInt(document.getElementById('modal-quantity').value) || 1;
            const totalQuantity = quantity * totalShirts;
            
            // Atualizar exibição da quantidade total
            document.getElementById('modal-total-pieces').textContent = totalQuantity;
            
            if (currentSize && currentSize.id) {
                fetch(`/api/sublimation-price/${currentSize.id}/${totalQuantity}`)
                    .then(r => r.json())
                    .then(data => {
                        currentSize.price = parseFloat(data.price);
                        updateModalPrices();
                    })
                    .catch(error => {
                        console.error('Error fetching price by quantity:', error);
                    });
            }
        }

        function updateModalPriceFromInput() {
            if (!isAdmin) {
                alert('Apenas administradores podem editar valores.');
                return;
            }
            const inputPrice = parseFloat(document.getElementById('modal-unit-price-input').value) || 0;
            currentSize.price = inputPrice;
            updateModalPrices();
        }

        document.getElementById('modal-quantity').addEventListener('input', function() {
            updatePriceByQuantity();
        });

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
            const fileInput = document.getElementById('art_files');
            const fileList = document.getElementById('file-list');
            const files = Array.from(fileInput.files);

            if (files.length === 0) {
                fileList.innerHTML = '';
                return;
            }

            fileList.innerHTML = files.map((file, index) => `
                <div class="flex items-center justify-between p-2 bg-gray-50 rounded border">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        <div>
                            <div class="text-sm font-medium">${file.name}</div>
                            <div class="text-xs text-gray-500">${(file.size / 1024).toFixed(2)} KB</div>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function addApplication() {
            const locationId = parseInt(document.getElementById('modal-location').value);
            const quantity = parseInt(document.getElementById('modal-quantity').value);

            if (!locationId || !quantity) {
                alert('Por favor, preencha todos os campos.');
                return;
            }

            const location = locations.find(l => l.id === locationId);

            const newApp = {
                size_id: currentSize.id,
                size_name: currentSize.name,
                size_dimensions: currentSize.dimensions,
                location_id: locationId,
                location_name: location.name,
                quantity: quantity,
                unit_price: currentSize.price
            };

            console.log('Adding application:', newApp);
            
            // Se estamos editando, substituir o item existente
            if (window.editingIndex !== undefined) {
                applications[window.editingIndex] = newApp;
                window.editingIndex = undefined;
            } else {
                applications.push(newApp);
            }
            
            console.log('Total applications:', applications.length);

            closeModal();
            renderApplications();
            calculatePrices();
        }

        function removeApplication(index) {
            if (!isAdmin) {
                alert('Apenas administradores podem remover aplicações.');
                return;
            }
            applications.splice(index, 1);
            renderApplications();
            calculatePrices();
        }

        function updateApplicationPrice(index, newPrice) {
            if (!isAdmin) {
                alert('Apenas administradores podem editar valores.');
                return;
            }
            if (applications[index]) {
                applications[index].unit_price = newPrice;
                calculatePrices();
            }
        }

        function updateApplicationQuantity(index, newQuantity) {
            if (!isAdmin) {
                alert('Apenas administradores podem editar quantidades.');
                return;
            }
            if (applications[index] && newQuantity > 0) {
                applications[index].quantity = newQuantity;
                calculatePrices();
            }
        }

        function editApplication(index) {
            if (!isAdmin) {
                alert('Apenas administradores podem editar aplicações.');
                return;
            }
            if (applications[index]) {
                const app = applications[index];
                currentSize = {
                    id: app.size_id,
                    name: app.size_name,
                    dimensions: app.size_dimensions,
                    price: app.unit_price
                };
                
                document.getElementById('modal-size-name').textContent = `${app.size_name} (${app.size_dimensions})`;
                document.getElementById('modal-location').value = app.location_id;
                document.getElementById('modal-quantity').value = app.quantity;
                
                updateModalPrices();
                document.getElementById('application-modal').classList.remove('hidden');
                
                // Marcar que estamos editando
                window.editingIndex = index;
            }
        }

        function renderApplications() {
            const container = document.getElementById('applications-list');
            const noApps = document.getElementById('no-applications');

            if (!container || !noApps) {
                console.error('Container or noApps element not found');
                return;
            }

            if (applications.length === 0) {
                noApps.style.display = 'block';
                container.innerHTML = '';
                return;
            }

            noApps.style.display = 'none';
            
            const html = applications.map((app, index) => `
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border">
                    <div class="flex-1">
                        <div class="font-medium">${app.location_name}: ${app.quantity}x ${app.size_name} (${app.size_dimensions})</div>
                        <div class="flex items-center space-x-2 mt-1">
                            <span class="text-sm text-gray-600">R$</span>
                            ${isAdmin ? `
                                <input type="number" 
                                       step="0.01" 
                                       min="0" 
                                       value="${app.unit_price.toFixed(2)}"
                                       onchange="updateApplicationPrice(${index}, parseFloat(this.value))"
                                       class="w-20 px-2 py-1 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            ` : `
                                <span class="text-sm font-medium">${app.unit_price.toFixed(2).replace('.', ',')}</span>
                            `}
                            <span class="text-sm text-gray-600">cada</span>
                            <span class="text-sm text-gray-600">|</span>
                            <span class="text-sm text-gray-600">Qtd:</span>
                            ${isAdmin ? `
                                <input type="number" 
                                       min="1" 
                                       value="${app.quantity}"
                                       onchange="updateApplicationQuantity(${index}, parseInt(this.value))"
                                       class="w-16 px-2 py-1 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            ` : `
                                <span class="text-sm font-medium">${app.quantity}</span>
                            `}
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        ${isAdmin ? `
                            <button type="button" onclick="editApplication(${index})" 
                                    class="text-blue-600 hover:text-blue-800 px-2 py-1 text-sm">
                                ✏️ Editar
                            </button>
                            <button type="button" onclick="removeApplication(${index})" 
                                    class="text-red-600 hover:text-red-800 px-2 py-1 text-sm">
                                🗑️ Remover
                            </button>
                        ` : `
                            <span class="text-xs text-gray-500">Apenas admin pode editar</span>
                        `}
                    </div>
                </div>
            `).join('');
            
            container.innerHTML = html;
            
            console.log('Applications rendered:', applications.length);
        }

        function calculatePrices() {
            console.log('Calculating prices for', applications.length, 'applications');
            
            if (applications.length === 0) {
                document.getElementById('price-breakdown').innerHTML = '<p class="text-gray-500 text-sm">Nenhuma aplicação adicionada.</p>';
                document.getElementById('total-price').textContent = 'R$ 0,00';
                document.getElementById('sublimations-data').value = '';
                return;
            }

            // Calcular subtotais
            const items = applications.map(app => {
                const subtotal = app.unit_price * app.quantity;
                console.log(`${app.size_name}: ${app.unit_price} x ${app.quantity} = ${subtotal}`);
                return {
                    ...app,
                    subtotal: subtotal
                };
            });

            // Ordenar por subtotal (maior primeiro)
            items.sort((a, b) => b.subtotal - a.subtotal);

            // Aplicar desconto: primeira integral, demais 50% off
            let total = 0;
            const breakdown = items.map((item, index) => {
                const discount = index === 0 ? 0 : 50;
                const finalPrice = item.subtotal * (1 - discount / 100);
                total += finalPrice;

                item.discount_percent = discount;
                item.final_price = finalPrice;

                console.log(`${item.location_name} - ${item.size_name}: R$ ${item.subtotal.toFixed(2)} -> R$ ${finalPrice.toFixed(2)} (${discount}% off)`);

                return `
                    <div class="flex justify-between">
                        <span>${item.location_name} - ${item.quantity}x ${item.size_name}${discount > 0 ? ` (-${discount}%)` : ''}</span>
                        <span class="${discount > 0 ? 'text-green-600' : ''}">R$ ${finalPrice.toFixed(2).replace('.', ',')}</span>
                    </div>
                `;
            }).join('');

            console.log('Total:', total);

            document.getElementById('price-breakdown').innerHTML = breakdown;
            document.getElementById('total-price').textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;

            // Atualizar campo hidden com dados das aplicações
            document.getElementById('sublimations-data').value = JSON.stringify(items);
        }
    </script>
</body>
</html>