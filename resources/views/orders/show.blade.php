<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pedido #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }} - Sistema de Pedidos</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <x-app-header />

    <div class="max-w-7xl mx-auto p-6">
        <!-- Cabeçalho -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-semibold">Pedido #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</h1>
                <p class="text-sm text-gray-600 mt-1">Criado em {{ $order->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('orders.index') }}" 
                   class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                    ← Voltar
                </a>
                @if(!$order->is_cancelled && !$order->has_pending_edit)
                <a href="{{ route('orders.edit-wizard.start', $order->id) }}" 
                   class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <span>Editar Pedido</span>
                </a>
                @elseif($order->has_pending_edit)
                <span class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded-md flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Edição Pendente</span>
                </span>
                @elseif($order->is_cancelled)
                <span class="px-4 py-2 bg-red-100 text-red-800 rounded-md flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <span>Pedido Cancelado</span>
                </span>
                @endif
                <a href="{{ route('kanban.index') }}" 
                   class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Ver no Kanban
                </a>
                <form method="POST" action="{{ route('orders.generate-share-link', $order->id) }}" class="inline">
                    @csrf
                    <button type="submit" 
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                        </svg>
                        <span>Compartilhar</span>
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Coluna Principal -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Status e Datas -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold mb-4">Informações do Pedido</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Status</p>
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full mt-1" 
                                  style="background-color: {{ $order->status->color }}20; color: {{ $order->status->color }}">
                                {{ $order->status->name }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Data de Entrega</p>
                            <p class="text-sm font-medium text-gray-900 mt-1">
                                @if($order->delivery_date)
                                    {{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') }}
                                @else
                                    Não definida
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <!-- Status de Confirmação do Cliente -->
                    <div class="mt-4 pt-4 border-t">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Confirmação do Cliente</p>
                                @if($order->client_confirmed)
                                <div class="flex items-center mt-1">
                                    <svg class="w-4 h-4 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-green-600">Confirmado</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $order->client_confirmed_at->format('d/m/Y H:i') }}
                                </p>
                                @else
                                <div class="flex items-center mt-1">
                                    <svg class="w-4 h-4 text-orange-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-orange-600">Pendente</span>
                                </div>
                                @endif
                            </div>
                            
                            @if($order->client_token)
                            <div class="text-right">
                                <p class="text-xs text-gray-500">Link de Compartilhamento</p>
                                <div class="flex items-center space-x-2 mt-1">
                                    <input type="text" 
                                           value="{{ route('client.order.show', $order->client_token) }}" 
                                           readonly 
                                           class="text-xs px-2 py-1 border border-gray-300 rounded bg-gray-50 w-48">
                                    <button onclick="copyToClipboard('{{ route('client.order.show', $order->client_token) }}', this)" 
                                            class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                                        Copiar
                                    </button>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Cliente -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold mb-4">👤 Cliente</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Nome</p>
                            <p class="text-sm font-medium text-gray-900">{{ $order->client->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Telefone</p>
                            <p class="text-sm font-medium text-gray-900">{{ $order->client->phone_primary }}</p>
                        </div>
                        @if($order->client->email)
                        <div>
                            <p class="text-sm text-gray-600">Email</p>
                            <p class="text-sm font-medium text-gray-900">{{ $order->client->email }}</p>
                        </div>
                        @endif
                        @if($order->client->cpf_cnpj)
                        <div>
                            <p class="text-sm text-gray-600">CPF/CNPJ</p>
                            <p class="text-sm font-medium text-gray-900">{{ $order->client->cpf_cnpj }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Itens do Pedido -->
                @foreach($order->items as $item)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold mb-4">✂️ Item {{ $loop->iteration }}</h2>
                    
                    <!-- Imagem de Capa -->
                    @if($item->cover_image)
                    <div class="mb-4">
                        <img src="{{ asset('storage/' . $item->cover_image) }}" 
                             alt="Capa" 
                             class="max-w-md rounded-lg border">
                    </div>
                    @endif

                    <!-- Detalhes da Costura -->
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-4">
                        <div>
                            <p class="text-xs text-gray-600">Tecido</p>
                            <p class="text-sm font-medium">{{ $item->fabric }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600">Cor</p>
                            <p class="text-sm font-medium">{{ $item->color }}</p>
                        </div>
                        @if($item->collar)
                        <div>
                            <p class="text-xs text-gray-600">Gola</p>
                            <p class="text-sm font-medium">{{ $item->collar }}</p>
                        </div>
                        @endif
                        @if($item->detail)
                        <div>
                            <p class="text-xs text-gray-600">Detalhe</p>
                            <p class="text-sm font-medium">{{ $item->detail }}</p>
                        </div>
                        @endif
                        @if($item->model)
                        <div>
                            <p class="text-xs text-gray-600">Tipo de Corte</p>
                            <p class="text-sm font-medium">{{ $item->model }}</p>
                        </div>
                        @endif
                        <div>
                            <p class="text-xs text-gray-600">Personalização</p>
                            <p class="text-sm font-medium">{{ $item->print_type }}</p>
                        </div>
                    </div>

                    <!-- Tamanhos -->
                    <div class="mb-4">
                        <p class="text-sm font-semibold mb-2">Tamanhos:</p>
                        <div class="grid grid-cols-5 md:grid-cols-10 gap-2">
                            @foreach($item->sizes as $size => $qty)
                                @if($qty > 0)
                                <div class="bg-gray-100 rounded px-2 py-1 text-center">
                                    <span class="text-xs text-gray-600">{{ $size }}</span>
                                    <p class="font-bold text-sm">{{ $qty }}</p>
                                </div>
                                @endif
                            @endforeach
                        </div>
                        <p class="text-sm mt-2"><strong>Total:</strong> {{ $item->quantity }} peças</p>
                    </div>

                    <!-- Personalizações -->
                    @if($item->sublimations && $item->sublimations->count() > 0)
                    <div class="border-t pt-4">
                        <p class="text-sm font-semibold mb-2">🎨 Personalização:</p>
                        @if($item->art_name)
                            <p class="text-sm mb-2"><strong>Nome da Arte:</strong> {{ $item->art_name }}</p>
                        @endif
                        <div class="space-y-2">
                            @foreach($item->sublimations as $sub)
                            <div class="flex justify-between items-center bg-gray-50 rounded p-3 text-sm">
                                <div>
                                    @php
                                        $sizeName = $sub->size ? $sub->size->name : $sub->size_name;
                                        $sizeDimensions = $sub->size ? $sub->size->dimensions : '';
                                        $locationName = $sub->location ? $sub->location->name : $sub->location_name;
                                        $appType = $sub->application_type ? strtoupper($sub->application_type) : 'APLICAÇÃO';
                                    @endphp
                                    
                                    <strong>
                                        @if($sizeName)
                                            {{ $sizeName }}@if($sizeDimensions) ({{ $sizeDimensions }})@endif
                                        @else
                                            {{ $appType }}
                                        @endif
                                    </strong>
                                    @if($locationName) - {{ $locationName }}@endif
                                    <span class="text-gray-600">x{{ $sub->quantity }}</span>
                                    @if($sub->color_count > 0)
                                        <br><span class="text-xs text-gray-500">{{ $sub->color_count }} {{ $sub->color_count == 1 ? 'Cor' : 'Cores' }}{{ $sub->has_neon ? ' + Neon' : '' }}</span>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <div class="text-gray-600">R$ {{ number_format($sub->unit_price, 2, ',', '.') }} × {{ $sub->quantity }}</div>
                                    @if($sub->discount_percent > 0)
                                        <div class="text-xs text-green-600">-{{ $sub->discount_percent }}%</div>
                                    @endif
                                    <div class="font-bold">R$ {{ number_format($sub->final_price, 2, ',', '.') }}</div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Arquivos -->
                    @if($item->files && $item->files->count() > 0)
                    <div class="border-t pt-4 mt-4">
                        <p class="text-sm font-semibold mb-2">📎 Arquivos:</p>
                        <div class="space-y-1">
                            @foreach($item->files as $file)
                            <div class="text-sm text-indigo-600">
                                • {{ $file->file_name }}
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>

            <!-- Coluna Lateral -->
            <div class="space-y-6">
                <!-- Gerenciamento de Pagamentos -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold">💰 Pagamentos</h2>
                        <button onclick="togglePaymentForm()" 
                                class="px-3 py-1 text-sm bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Adicionar Pagamento
                        </button>
                    </div>

                    <!-- Resumo Financeiro -->
                    @php
                        // Calcular total pago baseado nas transações de caixa confirmadas
                        $totalPaid = $cashTransactions->where('status', 'confirmado')->sum('amount');
                        $remaining = $order->total - $totalPaid;
                    @endphp
                    <div class="space-y-3 mb-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Total do Pedido:</span>
                            <span class="font-bold">R$ {{ number_format($order->total, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Total Pago:</span>
                            <span class="font-bold text-green-600">
                                R$ {{ number_format($totalPaid, 2, ',', '.') }}
                            </span>
                        </div>
                        <div class="flex justify-between text-sm border-t pt-2">
                            <span class="text-gray-600">Restante:</span>
                            <span class="font-bold {{ $remaining > 0 ? 'text-orange-600' : 'text-green-600' }}">
                                R$ {{ number_format($remaining, 2, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <!-- Lista de Pagamentos -->
                    @if($order->payments->count() > 0)
                    <div class="mb-4">
                        <h3 class="text-md font-medium mb-3">Histórico de Pagamentos</h3>
                        <div class="space-y-2">
                            @foreach($order->payments as $payment)
                                @if($payment->payment_methods && is_array($payment->payment_methods))
                                    @foreach($payment->payment_methods as $method)
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <div class="flex items-center space-x-4">
                                                    <div>
                                                        <p class="font-medium">R$ {{ number_format($method['amount'], 2, ',', '.') }}</p>
                                                        <p class="text-sm text-gray-600">{{ ucfirst($method['method']) }}</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($payment->created_at)->format('d/m/Y') }}</p>
                                                        @if($payment->notes)
                                                        <p class="text-xs text-gray-500">{{ $payment->notes }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex space-x-1">
                                                <button onclick="editPayment({{ $payment->id }}, '{{ $method['id'] }}')" 
                                                        class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                                                    Editar
                                                </button>
                                                <form method="POST" action="{{ route('orders.payment.delete', $order->id) }}" class="inline" 
                                                      onsubmit="return confirm('Tem certeza que deseja remover este pagamento?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="payment_id" value="{{ $payment->id }}">
                                                    <input type="hidden" name="method_id" value="{{ $method['id'] }}">
                                                    <button type="submit" 
                                                            class="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700">
                                                        Remover
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                <!-- Fallback para pagamentos antigos sem payment_methods -->
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-4">
                                                <div>
                                                    <p class="font-medium">R$ {{ number_format($payment->entry_amount, 2, ',', '.') }}</p>
                                                    <p class="text-sm text-gray-600">{{ ucfirst($payment->method) }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($payment->created_at)->format('d/m/Y') }}</p>
                                                    @if($payment->notes)
                                                    <p class="text-xs text-gray-500">{{ $payment->notes }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex space-x-1">
                                            <button onclick="editPayment({{ $payment->id }})" 
                                                    class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                                                Editar
                                            </button>
                                            <form method="POST" action="{{ route('orders.payment.delete', $order->id) }}" class="inline" 
                                                  onsubmit="return confirm('Tem certeza que deseja remover este pagamento?')">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="payment_id" value="{{ $payment->id }}">
                                                <button type="submit" 
                                                        class="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700">
                                                    Remover
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Formulário de Pagamento (Oculto por padrão) -->
                    <div id="paymentForm" class="hidden border-t pt-4">
                        <form method="POST" action="{{ route('orders.payment.add', $order->id) }}">
                            @csrf
                            
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Método de Pagamento</label>
                                    <select name="method" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                                        <option value="">Selecione...</option>
                                        <option value="pix">PIX</option>
                                        <option value="dinheiro">Dinheiro</option>
                                        <option value="cartao">Cartão</option>
                                        <option value="boleto">Boleto</option>
                                        <option value="transferencia">Transferência</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Valor</label>
                                    <input type="number" 
                                           name="amount" 
                                           step="0.01" 
                                           min="0.01" 
                                           max="{{ $remaining }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                                           required>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                                <textarea name="notes" 
                                          rows="2" 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                          placeholder="Observações sobre o pagamento..."></textarea>
                            </div>

                            <div class="flex justify-end space-x-3">
                                <button type="button" 
                                        onclick="togglePaymentForm()" 
                                        class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                                    Cancelar
                                </button>
                                <button type="submit" 
                                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                    Adicionar Pagamento
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Formulário de Edição de Pagamento (Oculto por padrão) -->
                    <div id="editPaymentForm" class="hidden border-t pt-4">
                        <form method="POST" action="{{ route('orders.payment.update', $order->id) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="payment_id" id="edit_payment_id">
                            
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Método de Pagamento</label>
                                    <select name="method" id="edit_method" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                                        <option value="">Selecione...</option>
                                        <option value="pix">PIX</option>
                                        <option value="dinheiro">Dinheiro</option>
                                        <option value="cartao">Cartão</option>
                                        <option value="boleto">Boleto</option>
                                        <option value="transferencia">Transferência</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Valor</label>
                                    <input type="number" 
                                           name="amount" 
                                           id="edit_amount"
                                           step="0.01" 
                                           min="0.01" 
                                           max="{{ $order->total }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                                           required>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                                <textarea name="notes" 
                                          id="edit_notes"
                                          rows="2" 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                          placeholder="Observações sobre o pagamento..."></textarea>
                            </div>

                            <div class="flex justify-end space-x-3">
                                <button type="button" 
                                        onclick="toggleEditPaymentForm()" 
                                        class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                                    Cancelar
                                </button>
                                <button type="submit" 
                                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                    Atualizar Pagamento
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Histórico de Transações -->
                    @if($cashTransactions->count() > 0)
                    <div class="border-t pt-4 mt-4">
                        <h3 class="text-sm font-medium text-gray-700 mb-3">Histórico de Transações</h3>
                        <div class="space-y-2">
                            @foreach($cashTransactions as $transaction)
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-md">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm font-medium">{{ $transaction->description }}</span>
                                        <span class="px-2 py-1 text-xs rounded-full {{ $transaction->status === 'confirmado' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($transaction->status) }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ ucfirst($transaction->payment_method) }} • {{ $transaction->transaction_date->format('d/m/Y H:i') }}
                                        @if($transaction->notes)
                                        <br>{{ $transaction->notes }}
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-green-600">
                                        +R$ {{ number_format($transaction->amount, 2, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Ações de Pagamento -->
                    @if($order->payments->count() > 0)
                    <div class="border-t pt-4 mt-4">
                        @php
                            $totalPaymentMethods = 0;
                            foreach($order->payments as $payment) {
                                if($payment->payment_methods && is_array($payment->payment_methods)) {
                                    $totalPaymentMethods += count($payment->payment_methods);
                                } else {
                                    $totalPaymentMethods += 1;
                                }
                            }
                        @endphp
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">
                                {{ $totalPaymentMethods }} {{ $totalPaymentMethods == 1 ? 'pagamento' : 'pagamentos' }} registrado{{ $totalPaymentMethods == 1 ? '' : 's' }}
                            </span>
                            
                            <div class="text-sm text-gray-500">
                                Status: <span class="font-medium {{ $remaining <= 0 ? 'text-green-600' : 'text-orange-600' }}">
                                    {{ $remaining <= 0 ? 'Pago' : 'Pendente' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Edição de Pedido -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold">✏️ Edição de Pedido</h2>
                        @if($order->is_editing)
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-yellow-100 text-yellow-800">
                            Em Edição
                        </span>
                        @else
                        <button onclick="requestEdit()" 
                                class="px-3 py-1 text-sm bg-orange-600 text-white rounded-md hover:bg-orange-700">
                            Solicitar Edição
                        </button>
                        @endif
                    </div>

                    @if($order->edit_status !== 'none')
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-yellow-800 font-medium">
                                    @switch($order->edit_status)
                                        @case('requested')
                                            Pedido aguardando aprovação para edição
                                            @break
                                        @case('approved')
                                            Edição aprovada - Pronto para modificar
                                            @break
                                        @case('rejected')
                                            Edição rejeitada
                                            @break
                                        @case('completed')
                                            Pedido modificado
                                            @break
                                    @endswitch
                                </span>
                            </div>
                            <div class="flex space-x-2">
                                @if($order->edit_status === 'requested')
                                <button onclick="approveEdit({{ $order->id }})" 
                                        class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                                    Aprovar
                                </button>
                                <button onclick="rejectEdit({{ $order->id }})" 
                                        class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700">
                                    Rejeitar
                                </button>
                                @endif
                            </div>
                        </div>
                        
                        <div class="mt-3 text-sm text-yellow-700">
                            <p><strong>Solicitado em:</strong> {{ $order->edit_requested_at->format('d/m/Y H:i') }}</p>
                            @if($order->edit_notes)
                            <p><strong>Motivo:</strong> {{ $order->edit_notes }}</p>
                            @endif
                            @if($order->edit_approved_at)
                            <p><strong>Aprovado em:</strong> {{ $order->edit_approved_at->format('d/m/Y H:i') }} por {{ $order->editApprovedBy->name ?? 'Sistema' }}</p>
                            @endif
                            @if($order->edit_rejected_at)
                            <p><strong>Rejeitado em:</strong> {{ $order->edit_rejected_at->format('d/m/Y H:i') }}</p>
                            <p><strong>Motivo da rejeição:</strong> {{ $order->edit_rejection_reason }}</p>
                            @endif
                            @if($order->is_modified)
                            <p><strong>Última modificação:</strong> {{ $order->last_modified_at->format('d/m/Y H:i') }}</p>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if($order->editHistory && $order->editHistory->count() > 0)
                    <div class="mb-4">
                        <h3 class="text-md font-medium mb-3">📝 Histórico de Edições</h3>
                        <div class="space-y-3">
                            @foreach($order->editHistory->sortByDesc('created_at') as $edit)
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2">
                                            @switch($edit->action)
                                                @case('edit_requested')
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        Solicitado
                                                    </span>
                                                    @break
                                                @case('edit_approved')
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        Aprovado
                                                    </span>
                                                    @break
                                                @case('edit_completed')
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        Concluído
                                                    </span>
                                                    @break
                                                @case('edit_rejected')
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                        Rejeitado
                                                    </span>
                                                    @break
                                                @default
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        {{ ucfirst($edit->action) }}
                                                    </span>
                                            @endswitch
                                            <span class="text-sm font-medium text-gray-900">{{ $edit->description }}</span>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">
                                            <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $edit->created_at->format('d/m/Y H:i') }} - {{ $edit->user_name }}
                                        </p>
                                    </div>
                                </div>
                                
                                @if($edit->changes)
                                <div class="mt-3">
                                    <button onclick="toggleChanges({{ $edit->id }})" 
                                            class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                        <span id="toggle-text-{{ $edit->id }}">Ver alterações</span>
                                        <svg id="toggle-icon-{{ $edit->id }}" class="w-3 h-3 inline ml-1 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div id="changes-{{ $edit->id }}" class="hidden mt-2 bg-white p-3 rounded border text-xs">
                                        @foreach($edit->changes as $section => $sectionChanges)
                                            <div class="mb-3">
                                                <h4 class="font-semibold text-gray-800 mb-2 capitalize">
                                                    @switch($section)
                                                        @case('client')
                                                            👤 Dados do Cliente
                                                            @break
                                                        @case('items')
                                                            📦 Itens do Pedido
                                                            @break
                                                        @case('order')
                                                            📋 Dados do Pedido
                                                            @break
                                                        @case('personalization')
                                                            🎨 Personalização
                                                            @break
                                                        @case('payment')
                                                            💰 Pagamento
                                                            @break
                                                        @case('notes')
                                                            📝 Observações
                                                            @break
                                                        @default
                                                            {{ ucfirst($section) }}
                                                    @endswitch
                                                </h4>
                                                
                                                @if($section === 'items')
                                                    @foreach($sectionChanges as $itemKey => $itemChanges)
                                                        <div class="ml-4 mb-2 p-2 bg-gray-50 rounded">
                                                            @if(isset($itemChanges['action']))
                                                                @if($itemChanges['action'] === 'created')
                                                                    <span class="text-green-600 font-medium">➕ Novo item adicionado</span>
                                                                @elseif($itemChanges['action'] === 'deleted')
                                                                    <span class="text-red-600 font-medium">🗑️ Item removido</span>
                                                                @endif
                                                            @else
                                                                <span class="text-blue-600 font-medium">✏️ Item modificado</span>
                                                                @foreach($itemChanges as $field => $change)
                                                                    <div class="ml-2 mt-1">
                                                                        <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $field)) }}:</span>
                                                                        <span class="text-red-600 line-through">{{ $change['old'] ?? 'N/A' }}</span>
                                                                        <span class="text-gray-500">→</span>
                                                                        <span class="text-green-600">{{ $change['new'] ?? 'N/A' }}</span>
                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                @else
                                                    @foreach($sectionChanges as $field => $change)
                                                        <div class="ml-4 mb-1">
                                                            <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $field)) }}:</span>
                                                            <span class="text-red-600 line-through">{{ $change['old'] ?? 'N/A' }}</span>
                                                            <span class="text-gray-500">→</span>
                                                            <span class="text-green-600">{{ $change['new'] ?? 'N/A' }}</span>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Downloads -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold mb-4">📥 Downloads</h2>
                    <div class="space-y-2">
                        <!-- Nota do Cliente -->
                        <a href="{{ route('orders.client-receipt', $order->id) }}" 
                           target="_blank"
                           class="block w-full px-4 py-2 bg-blue-600 text-white text-center rounded-md hover:bg-blue-700 text-sm flex items-center justify-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span>Nota do Cliente (PDF)</span>
                        </a>
                        
                        <div class="border-t my-3"></div>
                        
                        <!-- Downloads Internos -->
                        <a href="{{ url('/kanban/download-costura/' . $order->id) }}" 
                           target="_blank"
                           class="block w-full px-4 py-2 bg-purple-600 text-white text-center rounded-md hover:bg-purple-700 text-sm">
                            Folha Costura (A4)
                        </a>
                        <a href="{{ url('/kanban/download-personalizacao/' . $order->id) }}" 
                           target="_blank"
                           class="block w-full px-4 py-2 bg-pink-600 text-white text-center rounded-md hover:bg-pink-700 text-sm">
                            Folha Personalização (A4)
                        </a>
                        @if($order->items->first() && $order->items->first()->files->count() > 0)
                        <a href="{{ url('/kanban/download-files/' . $order->id) }}" 
                           target="_blank"
                           class="block w-full px-4 py-2 bg-green-600 text-white text-center rounded-md hover:bg-green-700 text-sm">
                            Arquivos da Arte
                        </a>
                        @endif
                    </div>
                </div>

                <!-- Solicitações de Antecipação -->
                @if($order->deliveryRequests && $order->deliveryRequests->count() > 0)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold mb-4">📅 Solicitações</h2>
                    @foreach($order->deliveryRequests as $request)
                    <div class="mb-3 p-3 rounded {{ $request->status === 'pendente' ? 'bg-yellow-50' : ($request->status === 'aprovado' ? 'bg-green-50' : 'bg-red-50') }}">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-xs font-semibold {{ $request->status === 'pendente' ? 'text-yellow-800' : ($request->status === 'aprovado' ? 'text-green-800' : 'text-red-800') }}">
                                {{ ucfirst($request->status) }}
                            </span>
                            <span class="text-xs text-gray-500">{{ $request->created_at->format('d/m/Y') }}</span>
                        </div>
                        <p class="text-xs text-gray-700">
                            <strong>Nova data:</strong> {{ $request->requested_delivery_date->format('d/m/Y') }}
                        </p>
                        <p class="text-xs text-gray-600 mt-1">{{ $request->reason }}</p>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function togglePaymentForm() {
            const form = document.getElementById('paymentForm');
            const editForm = document.getElementById('editPaymentForm');
            form.classList.toggle('hidden');
            editForm.classList.add('hidden');
        }

        function toggleEditPaymentForm() {
            const form = document.getElementById('editPaymentForm');
            const addForm = document.getElementById('paymentForm');
            form.classList.toggle('hidden');
            addForm.classList.add('hidden');
        }

        function editPayment(paymentId) {
            // Buscar dados do pagamento via AJAX
            fetch(`/pedidos/{{ $order->id }}/pagamento/${paymentId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('edit_payment_id').value = data.id;
                    document.getElementById('edit_method').value = data.method;
                    document.getElementById('edit_amount').value = data.entry_amount;
                    document.getElementById('edit_notes').value = data.notes || '';
                    
                    toggleEditPaymentForm();
                })
                .catch(error => {
                    console.error('Erro ao carregar dados do pagamento:', error);
                    alert('Erro ao carregar dados do pagamento');
                });
        }

        function requestEdit() {
            const reason = prompt('Motivo da edição:');
            if (reason) {
                fetch(`/pedidos/{{ $order->id }}/solicitar-edicao`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ reason: reason })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Erro ao solicitar edição: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao solicitar edição');
                });
            }
        }

        function approveEdit(orderId) {
            const notes = prompt('Observações sobre a aprovação (opcional):');
            fetch(`/pedidos/${orderId}/aprovar-edicao`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ notes: notes || '' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Erro ao aprovar edição: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao aprovar edição');
            });
        }

        function rejectEdit(orderId) {
            const reason = prompt('Motivo da rejeição:');
            if (reason) {
                fetch(`/pedidos/${orderId}/rejeitar-edicao`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ reason: reason })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Erro ao rejeitar edição: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao rejeitar edição');
                });
            }
        }

        function copyToClipboard(text, buttonElement) {
            // Função para mostrar feedback visual
            function showSuccess(button) {
                if (button) {
                    const originalText = button.textContent;
                    button.textContent = 'Copiado!';
                    button.classList.add('bg-green-600');
                    button.classList.remove('bg-blue-600');
                    
                    setTimeout(function() {
                        button.textContent = originalText;
                        button.classList.remove('bg-green-600');
                        button.classList.add('bg-blue-600');
                    }, 2000);
                }
            }

            // Verificar se a API de clipboard está disponível
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(function() {
                    showSuccess(buttonElement);
                }).catch(function(err) {
                    console.error('Erro ao copiar com clipboard API: ', err);
                    fallbackCopyTextToClipboard(text, buttonElement);
                });
            } else {
                // Fallback para navegadores mais antigos
                fallbackCopyTextToClipboard(text, buttonElement);
            }
        }

        // Função fallback para copiar texto
        function fallbackCopyTextToClipboard(text, buttonElement) {
            const textArea = document.createElement("textarea");
            textArea.value = text;
            
            // Evitar scroll para o elemento
            textArea.style.top = "0";
            textArea.style.left = "0";
            textArea.style.position = "fixed";
            textArea.style.opacity = "0";
            
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            try {
                const successful = document.execCommand('copy');
                if (successful) {
                    showSuccess(buttonElement);
                } else {
                    throw new Error('execCommand falhou');
                }
            } catch (err) {
                console.error('Fallback: Erro ao copiar texto', err);
                alert('Erro ao copiar o link. Tente selecionar e copiar manualmente.');
            }
            
            document.body.removeChild(textArea);
        }

        function showSuccess(buttonElement) {
            if (buttonElement) {
                const originalText = buttonElement.textContent;
                buttonElement.textContent = 'Copiado!';
                buttonElement.classList.add('bg-green-600');
                buttonElement.classList.remove('bg-blue-600');
                
                setTimeout(function() {
                    buttonElement.textContent = originalText;
                    buttonElement.classList.remove('bg-green-600');
                    buttonElement.classList.add('bg-blue-600');
                }, 2000);
            }
        }

        // Mostrar link de compartilhamento se foi gerado
        @if(session('share_url'))
        document.addEventListener('DOMContentLoaded', function() {
            const shareUrl = '{{ session('share_url') }}';
            const input = document.querySelector('input[readonly]');
            if (input) {
                input.value = shareUrl;
            }
        });
        @endif

        // Função para toggle das alterações no histórico
        function toggleChanges(editId) {
            const changesDiv = document.getElementById('changes-' + editId);
            const toggleText = document.getElementById('toggle-text-' + editId);
            const toggleIcon = document.getElementById('toggle-icon-' + editId);
            
            if (changesDiv.classList.contains('hidden')) {
                changesDiv.classList.remove('hidden');
                toggleText.textContent = 'Ocultar alterações';
                toggleIcon.style.transform = 'rotate(180deg)';
            } else {
                changesDiv.classList.add('hidden');
                toggleText.textContent = 'Ver alterações';
                toggleIcon.style.transform = 'rotate(0deg)';
            }
        }
    </script>
</body>
</html>
