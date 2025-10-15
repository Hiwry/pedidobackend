<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Confirmação do Pedido</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <x-app-header />

    <div class="max-w-7xl mx-auto p-6">
        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-green-600">✓ Etapa 5 de 5</span>
                <span class="text-sm text-gray-500">Confirmação</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-green-600 h-2 rounded-full" style="width: 100%"></div>
            </div>
        </div>

        <!-- Título -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Confirmação do Pedido</h1>
            <p class="text-gray-600">Pedido #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }} - Aguardando Confirmação</p>
        </div>

        <!-- Resumo Completo -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Coluna Principal -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- ETAPA 1: Dados do Cliente -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="bg-indigo-600 text-white px-6 py-3">
                        <h2 class="text-lg font-semibold">📋 Etapa 1: Dados do Cliente</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-600">Nome Completo:</span>
                                <p class="font-medium">{{ $order->client->name }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Telefone:</span>
                                <p class="font-medium">{{ $order->client->phone_primary }}</p>
                            </div>
                            @if($order->client->email)
                            <div>
                                <span class="text-sm text-gray-600">E-mail:</span>
                                <p class="font-medium">{{ $order->client->email }}</p>
                            </div>
                            @endif
                            @if($order->client->cpf_cnpj)
                            <div>
                                <span class="text-sm text-gray-600">CPF/CNPJ:</span>
                                <p class="font-medium">{{ $order->client->cpf_cnpj }}</p>
                            </div>
                            @endif
                            @if($order->client->address)
                            <div class="md:col-span-2">
                                <span class="text-sm text-gray-600">Endereço:</span>
                                <p class="font-medium">{{ $order->client->address }}</p>
                            </div>
                            @endif
                            @if($order->client->category)
                            <div>
                                <span class="text-sm text-gray-600">Categoria:</span>
                                <p class="font-medium">{{ $order->client->category }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- ETAPA 2: Costura -->
                @foreach($order->items as $index => $item)
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="bg-purple-600 text-white px-6 py-3">
                        <h2 class="text-lg font-semibold">✂️ Etapa 2: Costura - Item {{ $index + 1 }}</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <span class="text-sm text-gray-600">Personalização:</span>
                                <p class="font-medium">{{ $item->print_type }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Tecido:</span>
                                <p class="font-medium">{{ $item->fabric }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Cor:</span>
                                <p class="font-medium">{{ $item->color }}</p>
                            </div>
                            @if($item->collar)
                            <div>
                                <span class="text-sm text-gray-600">Gola:</span>
                                <p class="font-medium">{{ $item->collar }}</p>
                            </div>
                            @endif
                            @if($item->detail)
                            <div>
                                <span class="text-sm text-gray-600">Detalhe:</span>
                                <p class="font-medium">{{ $item->detail }}</p>
                            </div>
                            @endif
                            @if($item->model)
                            <div>
                                <span class="text-sm text-gray-600">Tipo de Corte:</span>
                                <p class="font-medium">{{ $item->model }}</p>
                            </div>
                            @endif
                        </div>

                        <!-- Tamanhos -->
                        <div class="border-t pt-4">
                            <span class="text-sm text-gray-600 block mb-2">Tamanhos:</span>
                            <div class="grid grid-cols-4 md:grid-cols-10 gap-2">
                                @foreach($item->sizes as $size => $qty)
                                    @if($qty > 0)
                                    <div class="bg-gray-100 rounded px-2 py-1 text-center">
                                        <span class="text-xs text-gray-600">{{ $size }}</span>
                                        <p class="font-bold text-sm">{{ $qty }}</p>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                            <p class="mt-2 text-sm font-medium">Total de Peças: <span class="text-indigo-600">{{ $item->quantity }}</span></p>
                        </div>
                    </div>
                </div>

                <!-- ETAPA 3: Personalização -->
                @if($item->sublimations->count() > 0)
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="bg-pink-600 text-white px-6 py-3">
                        <h2 class="text-lg font-semibold">🎨 Etapa 3: Personalização - Item {{ $index + 1 }}</h2>
                    </div>
                    <div class="p-6">
                        @if($item->art_name)
                        <div class="mb-4">
                            <span class="text-sm text-gray-600">Nome da Arte:</span>
                            <p class="font-medium">{{ $item->art_name }}</p>
                        </div>
                        @endif

                        @if($item->cover_image)
                        <div class="mb-4">
                            <span class="text-sm text-gray-600 block mb-2">Imagem de Capa:</span>
                            <img src="{{ asset('storage/' . $item->cover_image) }}" alt="Capa" class="max-w-xs rounded-lg border">
                        </div>
                        @endif

                        <!-- Aplicações -->
                        <div class="mb-4">
                            <span class="text-sm text-gray-600 block mb-2">Aplicações:</span>
                            <div class="space-y-2">
                                @foreach($item->sublimations as $sub)
                                <div class="flex items-center justify-between bg-gray-50 rounded-lg p-3">
                                    <div class="flex-1">
                                        @php
                                            // Usar nome do relacionamento se existir, senão usar campo de texto
                                            $sizeName = $sub->size ? $sub->size->name : $sub->size_name;
                                            $sizeDimensions = $sub->size ? $sub->size->dimensions : '';
                                            $locationName = $sub->location ? $sub->location->name : $sub->location_name;
                                            $appType = $sub->application_type ? strtoupper($sub->application_type) : 'APLICAÇÃO';
                                        @endphp
                                        
                                        <p class="font-medium">
                                            @if($sub->art_name)
                                                🎨 {{ $sub->art_name }}
                                            @elseif($sizeName)
                                                {{ $sizeName }}@if($sizeDimensions) ({{ $sizeDimensions }})@endif
                                            @else
                                                {{ $appType }}
                                            @endif
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            @if($locationName){{ $locationName }} - @endif
                                            @if($sizeName){{ $sizeName }}@if($sizeDimensions) ({{ $sizeDimensions }})@endif - @endif
                                            Qtd: {{ $sub->quantity }}
                                        </p>
                                        @if($sub->color_count > 0)
                                        <p class="text-xs text-gray-500">{{ $sub->color_count }} {{ $sub->color_count == 1 ? 'Cor' : 'Cores' }}
                                            @if($sub->has_neon) + Neon @endif
                                        </p>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-600">R$ {{ number_format($sub->unit_price, 2, ',', '.') }} × {{ $sub->quantity }}</p>
                                        @if($sub->discount_percent > 0)
                                        <p class="text-xs text-green-600">-{{ $sub->discount_percent }}%</p>
                                        @endif
                                        <p class="font-bold">R$ {{ number_format($sub->final_price, 2, ',', '.') }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        @if($item->files->count() > 0)
                        <div>
                            <span class="text-sm text-gray-600 block mb-2">Arquivos da Arte:</span>
                            <div class="space-y-1">
                                @foreach($item->files as $file)
                                <div class="flex items-center text-sm text-gray-700">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                    {{ $file->file_name }}
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
                @endforeach

                <!-- ETAPA 4: Pagamento -->
                @if($payment)
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="bg-green-600 text-white px-6 py-3">
                        <h2 class="text-lg font-semibold">💰 Etapa 4: Pagamento</h2>
                    </div>
                    <div class="p-6">
                        <div class="mb-4">
                            <span class="text-sm text-gray-600">Data de Entrada:</span>
                            <p class="font-medium">{{ \Carbon\Carbon::parse($payment->entry_date)->format('d/m/Y') }}</p>
                        </div>

                        <div class="mb-4">
                            <span class="text-sm text-gray-600 block mb-2">Formas de Pagamento:</span>
                            <div class="space-y-2">
                                @foreach($payment->payment_methods as $method)
                                <div class="flex justify-between items-center bg-gray-50 rounded-lg p-3">
                                    <span class="font-medium capitalize">{{ $method['method'] }}</span>
                                    <span class="font-bold">R$ {{ number_format($method['amount'], 2, ',', '.') }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="border-t pt-4">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Total Pago:</span>
                                <span class="font-medium">R$ {{ number_format($payment->entry_amount, 2, ',', '.') }}</span>
                            </div>
                            @if($payment->remaining_amount > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Restante:</span>
                                <span class="font-medium text-orange-600">R$ {{ number_format($payment->remaining_amount, 2, ',', '.') }}</span>
                            </div>
                            @else
                            <div class="flex items-center text-green-600 text-sm">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Pago Integralmente
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

            </div>

            <!-- Coluna Lateral - Resumo Financeiro -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-6">
                    <h3 class="text-xl font-bold mb-4 text-gray-900">💵 Resumo Financeiro</h3>
                    
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal (Etapa 2 + 3):</span>
                            <span class="font-medium">R$ {{ number_format($order->subtotal, 2, ',', '.') }}</span>
                        </div>

                        @if(!empty($sizeSurcharges))
                        <div class="border-t pt-2">
                            <p class="text-gray-600 font-medium mb-2">Acréscimos por Tamanho:</p>
                            @foreach($sizeSurcharges as $size => $surcharge)
                            <div class="flex justify-between text-xs ml-2">
                                <span class="text-gray-500">{{ $size }}:</span>
                                <span class="text-orange-600">+R$ {{ number_format($surcharge, 2, ',', '.') }}</span>
                            </div>
                            @endforeach
                            <div class="flex justify-between mt-1">
                                <span class="text-gray-600">Total Acréscimos:</span>
                                <span class="font-medium text-orange-600">+R$ {{ number_format(array_sum($sizeSurcharges), 2, ',', '.') }}</span>
                            </div>
                        </div>
                        @endif

                        @if($order->delivery_fee > 0)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Taxa de Entrega:</span>
                            <span class="font-medium">+R$ {{ number_format($order->delivery_fee, 2, ',', '.') }}</span>
                        </div>
                        @endif

                        <div class="border-t pt-3">
                            <div class="flex justify-between text-lg font-bold">
                                <span>Total Final:</span>
                                <span class="text-indigo-600">R$ {{ number_format($order->total, 2, ',', '.') }}</span>
                            </div>
                        </div>

                        @if($payment)
                        <div class="border-t pt-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Valor Pago:</span>
                                <span class="font-medium text-green-600">R$ {{ number_format($payment->entry_amount, 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between mt-1">
                                <span class="text-gray-600">Saldo Restante:</span>
                                <span class="font-bold {{ $payment->remaining_amount > 0 ? 'text-orange-600' : 'text-green-600' }}">
                                    R$ {{ number_format($payment->remaining_amount, 2, ',', '.') }}
                                </span>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Status do Pedido -->
                    <div class="mt-6 p-4 bg-yellow-50 rounded-lg border border-yellow-300">
                        <p class="text-sm text-gray-600 mb-1">Status do Pedido:</p>
                        <p class="font-bold text-yellow-700">📝 RASCUNHO - Aguardando Confirmação</p>
                        <p class="text-xs text-gray-600 mt-2">
                            Este pedido ainda não está visível no kanban. Confirme abaixo para enviar para produção.
                        </p>
                    </div>

                    <!-- Ações -->
                    <div class="mt-6 space-y-3">
                        <form method="POST" action="{{ route('orders.wizard.finalize') }}" id="finalize-form" onsubmit="return handleFinalize(this)">
                            @csrf
                            <button type="submit" id="finalize-btn"
                                    class="block w-full px-4 py-3 bg-green-600 text-white text-center rounded-md hover:bg-green-700 font-medium shadow-lg transition-all">
                                <span id="finalize-text">✓ Confirmar Pedido e Enviar para Produção</span>
                                <span id="finalize-loading" class="hidden">⏳ Finalizando...</span>
                            </button>
                        </form>
                        <a href="{{ route('orders.wizard.payment') }}" 
                           class="block w-full px-4 py-3 bg-gray-600 text-white text-center rounded-md hover:bg-gray-700 font-medium">
                            ← Voltar para Pagamento
                        </a>
                        <button onclick="window.print()" 
                                class="block w-full px-4 py-3 bg-white border border-gray-300 text-gray-700 text-center rounded-md hover:bg-gray-50 font-medium">
                            🖨️ Imprimir Resumo
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            body {
                background: white;
            }
            .no-print {
                display: none;
            }
        }
    </style>

    <script>
        let formSubmitted = false;

        function handleFinalize(form) {
            if (formSubmitted) {
                return false;
            }

            if (!confirm('Confirmar pedido e enviar para produção?')) {
                return false;
            }

            formSubmitted = true;
            
            // Desabilitar botão e mostrar loading
            const btn = document.getElementById('finalize-btn');
            const text = document.getElementById('finalize-text');
            const loading = document.getElementById('finalize-loading');
            
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            text.classList.add('hidden');
            loading.classList.remove('hidden');

            // Enviar o formulário após um pequeno delay para mostrar o loading
            setTimeout(() => {
                form.submit();
            }, 500);

            return false; // Prevenir envio imediato
        }

        // Prevenir múltiplos envios
        document.getElementById('finalize-form').addEventListener('submit', function(e) {
            if (formSubmitted) {
                e.preventDefault();
                return false;
            }
        });
    </script>
</body>
</html>