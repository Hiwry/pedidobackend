<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edição de Pedido - Confirmação</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <x-app-header />

    <div class="max-w-6xl mx-auto p-6">
        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-indigo-600">Etapa 5 de 5</span>
                <span class="text-sm text-gray-500">Confirmação</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-indigo-600 h-2 rounded-full" style="width: 100%"></div>
            </div>
        </div>

        @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
        @endif

        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-semibold mb-6">Confirmação da Edição</h1>
            
            <div class="mb-8">
                <h2 class="text-lg font-semibold mb-4">Resumo das Alterações</h2>
                
                <!-- Dados do Cliente -->
                <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                    <h3 class="font-semibold text-blue-800 mb-3">Dados do Cliente</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div><strong>Nome:</strong> {{ $editData['client']['name'] ?? $order->client->name }}</div>
                        <div><strong>Telefone:</strong> {{ $editData['client']['phone_primary'] ?? $order->client->phone_primary }}</div>
                        <div><strong>Email:</strong> {{ $editData['client']['email'] ?? $order->client->email ?: 'Não informado' }}</div>
                        <div><strong>CPF/CNPJ:</strong> {{ $editData['client']['cpf_cnpj'] ?? $order->client->cpf_cnpj ?: 'Não informado' }}</div>
                        <div class="md:col-span-2"><strong>Endereço:</strong> {{ $editData['client']['address'] ?? $order->client->address ?: 'Não informado' }}</div>
                    </div>
                </div>

                <!-- Itens do Pedido -->
                <div class="mb-6 p-4 bg-green-50 rounded-lg">
                    <h3 class="font-semibold text-green-800 mb-3">Itens do Pedido</h3>
                    @if(isset($editData['items']) && count($editData['items']) > 0)
                        @foreach($editData['items'] as $index => $item)
                        <div class="mb-3 p-3 bg-white rounded border">
                            <div class="font-medium">Item {{ $index + 1 }}</div>
                            <div class="text-sm text-gray-600 mt-1">
                                <div><strong>Tipo:</strong> {{ $item['print_type'] ?? 'Não definido' }}</div>
                                <div><strong>Arte:</strong> {{ $item['art_name'] ?? 'Não definido' }}</div>
                                <div><strong>Quantidade:</strong> {{ $item['quantity'] ?? 0 }}</div>
                                <div><strong>Tecido:</strong> {{ $item['fabric'] ?? 'Não definido' }}</div>
                                <div><strong>Cor:</strong> {{ $item['color'] ?? 'Não definido' }}</div>
                                <div><strong>Preço Unitário:</strong> R$ {{ number_format($item['unit_price'] ?? 0, 2, ',', '.') }}</div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <p class="text-gray-600">Nenhum item modificado</p>
                    @endif
                </div>

                <!-- Personalização -->
                <div class="mb-6 p-4 bg-yellow-50 rounded-lg">
                    <h3 class="font-semibold text-yellow-800 mb-3">Personalização</h3>
                    <div class="text-sm">
                        <div><strong>Tipo de Contrato:</strong> 
                            @if(($editData['contract_type'] ?? $order->contract_type) == 'costura')
                                Costura
                            @elseif(($editData['contract_type'] ?? $order->contract_type) == 'personalizacao')
                                Personalização
                            @elseif(($editData['contract_type'] ?? $order->contract_type) == 'ambos')
                                Ambos
                            @else
                                Não definido
                            @endif
                        </div>
                        <div><strong>Vendedor:</strong> {{ $editData['seller'] ?? $order->seller ?: 'Não informado' }}</div>
                    </div>
                </div>

                <!-- Pagamento -->
                <div class="mb-6 p-4 bg-purple-50 rounded-lg">
                    <h3 class="font-semibold text-purple-800 mb-3">Pagamento e Valores</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div><strong>Data de Entrega:</strong> {{ $editData['delivery_date'] ?? ($order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') : 'Não definida') }}</div>
                        <div><strong>Subtotal:</strong> R$ {{ number_format($editData['subtotal'] ?? $order->subtotal, 2, ',', '.') }}</div>
                        <div><strong>Desconto:</strong> R$ {{ number_format($editData['discount'] ?? $order->discount, 2, ',', '.') }}</div>
                        <div><strong>Taxa de Entrega:</strong> R$ {{ number_format($editData['delivery_fee'] ?? $order->delivery_fee, 2, ',', '.') }}</div>
                        <div class="md:col-span-2"><strong>Total:</strong> R$ {{ number_format($editData['total'] ?? $order->total, 2, ',', '.') }}</div>
                    </div>
                </div>

                <!-- Observações -->
                @if($editData['notes'] ?? $order->notes)
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <h3 class="font-semibold text-gray-800 mb-3">Observações</h3>
                    <p class="text-sm">{{ $editData['notes'] ?? $order->notes }}</p>
                </div>
                @endif
            </div>

            <form method="POST" action="{{ route('orders.edit-wizard.finalize') }}">
                @csrf
                
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <h3 class="font-semibold text-red-800 mb-3">Motivo da Edição *</h3>
                    <textarea name="edit_reason" rows="3" required
                              class="w-full px-3 py-2 border border-red-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                              placeholder="Explique o motivo das alterações..."></textarea>
                </div>

                <div class="flex justify-between">
                    <a href="{{ route('orders.edit-wizard.payment') }}" 
                       class="px-6 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                        ← Voltar
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        ✓ Finalizar Edição
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
