<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edição de Pedido - Personalização</title>
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

        @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
        @endif

        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-semibold mb-6">Personalização - Edição de Pedido</h1>
            
            <form method="POST" action="{{ route('orders.edit-wizard.customization') }}">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="contract_type" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Contrato *</label>
                        <select id="contract_type" name="contract_type" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Selecione...</option>
                            <option value="costura" {{ ($editData['contract_type'] ?? $order->contract_type) == 'costura' ? 'selected' : '' }}>Costura</option>
                            <option value="personalizacao" {{ ($editData['contract_type'] ?? $order->contract_type) == 'personalizacao' ? 'selected' : '' }}>Personalização</option>
                            <option value="ambos" {{ ($editData['contract_type'] ?? $order->contract_type) == 'ambos' ? 'selected' : '' }}>Ambos</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="seller" class="block text-sm font-medium text-gray-700 mb-2">Vendedor</label>
                        <input type="text" id="seller" name="seller" 
                               value="{{ $editData['seller'] ?? $order->seller }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                               placeholder="Nome do vendedor">
                    </div>
                </div>

                <div class="flex justify-between mt-8">
                    <a href="{{ route('orders.edit-wizard.sewing') }}" 
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
</body>
</html>
