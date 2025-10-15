<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edição de Pedido - Pagamento</title>
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

        @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
        @endif

        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-semibold mb-6">Pagamento e Valores - Edição de Pedido</h1>
            
            <form method="POST" action="{{ route('orders.edit-wizard.payment') }}">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <label for="delivery_date" class="block text-sm font-medium text-gray-700 mb-2">Data de Entrega *</label>
                        <input type="date" id="delivery_date" name="delivery_date" required
                               value="{{ $editData['delivery_date'] ?? ($order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('Y-m-d') : '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    
                    <div>
                        <label for="subtotal" class="block text-sm font-medium text-gray-700 mb-2">Subtotal (R$) *</label>
                        <input type="number" id="subtotal" name="subtotal" step="0.01" min="0" required
                               value="{{ $editData['subtotal'] ?? $order->subtotal }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                               onchange="calculateTotal()">
                    </div>
                    
                    <div>
                        <label for="discount" class="block text-sm font-medium text-gray-700 mb-2">Desconto (R$)</label>
                        <input type="number" id="discount" name="discount" step="0.01" min="0"
                               value="{{ $editData['discount'] ?? $order->discount }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                               onchange="calculateTotal()">
                    </div>
                    
                    <div>
                        <label for="delivery_fee" class="block text-sm font-medium text-gray-700 mb-2">Taxa de Entrega (R$)</label>
                        <input type="number" id="delivery_fee" name="delivery_fee" step="0.01" min="0"
                               value="{{ $editData['delivery_fee'] ?? $order->delivery_fee }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                               onchange="calculateTotal()">
                    </div>
                    
                    <div>
                        <label for="total" class="block text-sm font-medium text-gray-700 mb-2">Total (R$) *</label>
                        <input type="number" id="total" name="total" step="0.01" min="0" required
                               value="{{ $editData['total'] ?? $order->total }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-gray-100"
                               readonly>
                    </div>
                </div>

                <div class="mt-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Observações</label>
                    <textarea id="notes" name="notes" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                              placeholder="Observações sobre o pedido...">{{ $editData['notes'] ?? $order->notes }}</textarea>
                </div>

                <div class="flex justify-between mt-8">
                    <a href="{{ route('orders.edit-wizard.customization') }}" 
                       class="px-6 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                        ← Voltar
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Próximo →
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function calculateTotal() {
            const subtotal = parseFloat(document.getElementById('subtotal').value) || 0;
            const discount = parseFloat(document.getElementById('discount').value) || 0;
            const deliveryFee = parseFloat(document.getElementById('delivery_fee').value) || 0;
            
            const total = subtotal - discount + deliveryFee;
            document.getElementById('total').value = total.toFixed(2);
        }

        // Calcular total inicial
        document.addEventListener('DOMContentLoaded', function() {
            calculateTotal();
        });
    </script>
</body>
</html>
