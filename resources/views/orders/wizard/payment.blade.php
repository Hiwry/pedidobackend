<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Novo Pedido - Pagamento</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <x-app-header />

    <div class="max-w-6xl mx-auto p-6">
        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-indigo-600">Etapa 4 de 5</span>
                <span class="text-sm text-gray-500">Pagamento</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-indigo-600 h-2 rounded-full" style="width: 80%"></div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Resumo do Pedido -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-6">
                    <h2 class="text-xl font-semibold mb-4">Resumo do Pedido</h2>
                    
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Cliente:</span>
                            <span class="font-medium">{{ $order->client->name }}</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total de Peças:</span>
                            <span class="font-medium" id="total-pieces">{{ $order->items->sum('quantity') }}</span>
                        </div>
                        
                        <div class="border-t pt-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal (Etapa 2 + 3):</span>
                                <span class="font-medium" id="subtotal">R$ {{ number_format($order->subtotal, 2, ',', '.') }}</span>
                            </div>
                        </div>
                        
                        <div id="surcharges-breakdown" class="space-y-2">
                            <!-- Será preenchido via JavaScript -->
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-gray-600">Taxa de Entrega:</span>
                            <span class="font-medium" id="delivery-fee-display">R$ 0,00</span>
                        </div>
                        
                        <div class="border-t pt-3">
                            <div class="flex justify-between text-lg font-bold">
                                <span>Total Final:</span>
                                <span id="total-final" class="text-indigo-600">R$ {{ number_format($order->subtotal, 2, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulário de Pagamento -->
            <div class="lg:col-span-2">
                <form method="POST" action="{{ route('orders.wizard.payment') }}" id="payment-form" class="space-y-6" enctype="multipart/form-data">
                    @csrf
                    
                    <input type="hidden" name="payment_methods" id="payment-methods-data">
                    <input type="hidden" name="size_surcharges" id="size-surcharges-data">
                    <input type="hidden" name="order_data" value="{{ json_encode($order->items->first()->sizes ?? []) }}">


                    <!-- Data de Entrada -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold mb-4">Data de Entrada</h3>
                        <div>
                            <label for="entry_date" class="block text-sm font-medium text-gray-700 mb-2">Data da Entrada *</label>
                            <input type="date" id="entry_date" name="entry_date" required
                                   value="{{ date('Y-m-d') }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <!-- Taxa de Entrega -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold mb-4">Taxa de Entrega</h3>
                        <div>
                            <label for="delivery_fee" class="block text-sm font-medium text-gray-700 mb-2">Valor da Taxa (R$)</label>
                            <input type="number" id="delivery_fee" name="delivery_fee" step="0.01" min="0" value="0"
                                   onchange="calculateTotal()"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <!-- Formas de Pagamento -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Formas de Pagamento</h3>
                            <button type="button" onclick="addPaymentMethod()" 
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm">
                                + Adicionar Forma
                            </button>
                        </div>

                        <div id="payment-methods-list" class="space-y-4">
                            <!-- Será preenchido via JavaScript -->
                        </div>

                        <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <div class="flex justify-between text-sm">
                                <span>Total Pago:</span>
                                <span class="font-bold" id="total-paid">R$ 0,00</span>
                            </div>
                            <div class="flex justify-between text-sm mt-2">
                                <span>Restante:</span>
                                <span class="font-bold" id="remaining">R$ 0,00</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between pt-4">
                        <a href="{{ route('orders.wizard.customization') }}" class="px-4 py-2 text-gray-600 hover:text-gray-900">← Voltar</a>
                        <button type="submit" 
                                class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            Continuar →
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let paymentMethods = [];
        let subtotal = {{ $order->subtotal }};
        let deliveryFee = 0;
        let sizeSurcharges = {};
        let orderSizes = @json($order->items->first()->sizes ?? []);

        document.addEventListener('DOMContentLoaded', function() {
            calculateSizeSurcharges();
            addPaymentMethod(); // Adicionar primeira forma de pagamento
        });

        function calculateSizeSurcharges() {
            // Calcular acréscimos por tamanho (GG, EXG, G1, G2, G3)
            const largeSizes = ['GG', 'EXG', 'G1', 'G2', 'G3'];
            let totalSurcharge = 0;
            let surchargesHtml = '';

            largeSizes.forEach(size => {
                const quantity = parseInt(orderSizes[size] || 0);
                if (quantity > 0) {
                    // Buscar acréscimo na API
                    fetch(`/api/size-surcharge/${size}/${subtotal}`)
                        .then(r => r.json())
                        .then(data => {
                            if (data.surcharge) {
                                const surcharge = parseFloat(data.surcharge) * quantity;
                                sizeSurcharges[size] = surcharge;
                                totalSurcharge += surcharge;
                                
                                surchargesHtml += `
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Acréscimo ${size} (${quantity}x):</span>
                                        <span class="font-medium text-orange-600">+R$ ${surcharge.toFixed(2).replace('.', ',')}</span>
                                    </div>
                                `;
                                
                                document.getElementById('surcharges-breakdown').innerHTML = surchargesHtml;
                                document.getElementById('size-surcharges-data').value = JSON.stringify(sizeSurcharges);
                                calculateTotal();
                            }
                        });
                }
            });
        }

        function addPaymentMethod() {
            const id = Date.now();
            paymentMethods.push({
                id: id,
                method: 'pix',
                amount: 0
            });
            renderPaymentMethods();
        }

        function removePaymentMethod(id) {
            paymentMethods = paymentMethods.filter(pm => pm.id !== id);
            renderPaymentMethods();
            calculatePayments();
        }

        function renderPaymentMethods() {
            const container = document.getElementById('payment-methods-list');
            
            if (paymentMethods.length === 0) {
                container.innerHTML = '<p class="text-gray-500 text-sm">Nenhuma forma de pagamento adicionada.</p>';
                return;
            }

            container.innerHTML = paymentMethods.map((pm, index) => `
                <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg border">
                    <div class="flex-1">
                        <label class="block text-xs text-gray-600 mb-1">Forma de Pagamento</label>
                        <select onchange="updatePaymentMethod(${pm.id}, 'method', this.value)" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="pix" ${pm.method === 'pix' ? 'selected' : ''}>PIX</option>
                            <option value="dinheiro" ${pm.method === 'dinheiro' ? 'selected' : ''}>Dinheiro</option>
                            <option value="cartao" ${pm.method === 'cartao' ? 'selected' : ''}>Cartão</option>
                            <option value="boleto" ${pm.method === 'boleto' ? 'selected' : ''}>Boleto</option>
                            <option value="transferencia" ${pm.method === 'transferencia' ? 'selected' : ''}>Transferência</option>
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs text-gray-600 mb-1">Valor (R$)</label>
                        <input type="number" step="0.01" min="0" value="${pm.amount}"
                               onchange="updatePaymentMethod(${pm.id}, 'amount', parseFloat(this.value))"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    ${paymentMethods.length > 1 ? `
                        <button type="button" onclick="removePaymentMethod(${pm.id})" 
                                class="text-red-600 hover:text-red-800 mt-5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    ` : ''}
                </div>
            `).join('');
        }

        function updatePaymentMethod(id, field, value) {
            const pm = paymentMethods.find(p => p.id === id);
            if (pm) {
                pm[field] = value;
                calculatePayments();
            }
        }

        function calculatePayments() {
            const totalPaid = paymentMethods.reduce((sum, pm) => sum + (parseFloat(pm.amount) || 0), 0);
            const totalFinal = getTotalFinal();
            const remaining = totalFinal - totalPaid;

            document.getElementById('total-paid').textContent = `R$ ${totalPaid.toFixed(2).replace('.', ',')}`;
            document.getElementById('remaining').textContent = `R$ ${remaining.toFixed(2).replace('.', ',')}`;

            // Atualizar campo hidden
            document.getElementById('payment-methods-data').value = JSON.stringify(paymentMethods);
        }

        function calculateTotal() {
            deliveryFee = parseFloat(document.getElementById('delivery_fee').value) || 0;
            
            const totalSurcharges = Object.values(sizeSurcharges).reduce((sum, val) => sum + val, 0);
            const totalFinal = subtotal + totalSurcharges + deliveryFee;

            document.getElementById('delivery-fee-display').textContent = `R$ ${deliveryFee.toFixed(2).replace('.', ',')}`;
            document.getElementById('total-final').textContent = `R$ ${totalFinal.toFixed(2).replace('.', ',')}`;
            
            calculatePayments();
        }

        function getTotalFinal() {
            const totalSurcharges = Object.values(sizeSurcharges).reduce((sum, val) => sum + val, 0);
            return subtotal + totalSurcharges + deliveryFee;
        }

    </script>
</body>
</html>