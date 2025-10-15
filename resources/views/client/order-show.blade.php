<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Meu Pedido #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media (max-width: 640px) {
            .mobile-padding { padding: 1rem; }
            .mobile-text-sm { font-size: 0.875rem; }
            .mobile-text-xs { font-size: 0.75rem; }
        }
        
        .cover-image {
            max-width: 100%;
            height: auto;
            max-height: 200px;
            object-fit: contain;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        @media (max-width: 640px) {
            .cover-image {
                max-height: 150px;
            }
        }
        
        @media (max-width: 480px) {
            .cover-image {
                max-height: 120px;
            }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-indigo-600 text-white py-4 px-4 mobile-padding">
        <div class="max-w-md mx-auto">
            <h1 class="text-xl font-bold text-center">Meu Pedido</h1>
            <p class="text-center text-indigo-200 mobile-text-sm">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
        </div>
    </div>

    <div class="max-w-md mx-auto mobile-padding">
        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 mobile-text-sm">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                {{ session('success') }}
            </div>
        </div>
        @endif

        <!-- Status do Pedido -->
        <div class="bg-white rounded-lg shadow p-4 mb-4">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-lg font-semibold">Status do Pedido</h2>
                <span class="px-3 py-1 text-sm font-medium rounded-full text-white" 
                      style="background-color: {{ $order->status->color }}">
                    {{ $order->status->name }}
                </span>
            </div>
            
            @if($order->client_confirmed)
            <div class="bg-green-50 border border-green-200 rounded p-3 mb-3">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-green-800 font-medium mobile-text-sm">Pedido Confirmado</span>
                </div>
                <p class="text-green-600 mobile-text-xs mt-1">
                    Confirmado em {{ $order->client_confirmed_at->format('d/m/Y H:i') }}
                </p>
            </div>
            @endif

            <div class="grid grid-cols-2 gap-4 mobile-text-sm">
                <div>
                    <p class="text-gray-600 mobile-text-xs">Data do Pedido</p>
                    <p class="font-medium">{{ $order->created_at->format('d/m/Y') }}</p>
                </div>
                @if($order->delivery_date)
                <div>
                    <p class="text-gray-600 mobile-text-xs">Previsão de Entrega</p>
                    <p class="font-medium">{{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Dados do Cliente -->
        <div class="bg-white rounded-lg shadow p-4 mb-4">
            <h2 class="text-lg font-semibold mb-3">Seus Dados</h2>
            <div class="space-y-2 mobile-text-sm">
                <div>
                    <p class="text-gray-600 mobile-text-xs">Nome</p>
                    <p class="font-medium">{{ $order->client->name }}</p>
                </div>
                <div>
                    <p class="text-gray-600 mobile-text-xs">Telefone</p>
                    <p class="font-medium">{{ $order->client->phone_primary }}</p>
                </div>
                @if($order->client->email)
                <div>
                    <p class="text-gray-600 mobile-text-xs">Email</p>
                    <p class="font-medium">{{ $order->client->email }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Itens do Pedido -->
        @foreach($order->items as $item)
        <div class="bg-white rounded-lg shadow p-4 mb-4">
            <h2 class="text-lg font-semibold mb-3">Item {{ $loop->iteration }} - {{ $item->print_type }}</h2>
            
            @if($item->art_name)
            <div class="mb-3">
                <p class="text-gray-600 mobile-text-xs">Nome da Arte</p>
                <p class="font-medium mobile-text-sm">{{ $item->art_name }}</p>
            </div>
            @endif

            <!-- Imagem de Capa -->
            @if($item->cover_image)
            <div class="mb-3">
                <p class="text-gray-600 mobile-text-xs mb-2">Imagem de Referência</p>
                <div class="text-center">
                    @php
                        $imagePath = 'storage/' . $item->cover_image;
                        $fullPath = public_path($imagePath);
                        $imageExists = file_exists($fullPath);
                    @endphp
                    
                    @if($imageExists)
                        <img src="{{ asset($imagePath) }}" 
                             alt="Imagem de Referência" 
                             class="cover-image mx-auto"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    @else
                        <div class="text-gray-500 text-sm py-4">
                            <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p>Arquivo de imagem não encontrado</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $item->cover_image }}</p>
                        </div>
                    @endif
                    
                    <div class="text-gray-500 text-sm py-4" style="display: none;">
                        <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p>Erro ao carregar imagem</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Especificações -->
            <div class="mb-3">
                <p class="text-gray-600 mobile-text-xs mb-2">Especificações</p>
                <div class="grid grid-cols-2 gap-2 mobile-text-sm">
                    <div>
                        <span class="text-gray-600">Tecido:</span>
                        <span class="font-medium">{{ $item->fabric }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Cor:</span>
                        <span class="font-medium">{{ $item->color }}</span>
                    </div>
                </div>
            </div>

            <!-- Aplicações -->
            @if($item->sublimations && $item->sublimations->count() > 0)
            <div class="mb-3">
                <p class="text-gray-600 mobile-text-xs mb-2">Personalizações</p>
                @foreach($item->sublimations as $index => $sub)
                @php
                    $sizeName = $sub->size ? $sub->size->name : $sub->size_name;
                    $locationName = $sub->location ? $sub->location->name : $sub->location_name;
                    $appType = $sub->application_type ? strtoupper($sub->application_type) : 'APLICAÇÃO';
                @endphp
                <div class="bg-gray-50 p-3 rounded mb-2 mobile-text-sm">
                    <div class="font-medium">
                        {{ $index + 1 }}. 
                        @if($sizeName)
                            {{ $sizeName }}
                        @else
                            {{ $appType }}
                        @endif
                    </div>
                    <div class="text-gray-600 mobile-text-xs">
                        @if($locationName){{ $locationName }} | @endif
                        Qtd: {{ $sub->quantity }}
                        @if($sub->color_count > 0) | {{ $sub->color_count }} cores @endif
                        @if($sub->has_neon) | Neon @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <!-- Tamanhos -->
            <div class="mb-3">
                <p class="text-gray-600 mobile-text-xs mb-2">Tamanhos</p>
                <div class="grid grid-cols-5 gap-1 mobile-text-xs">
                    @foreach(['PP', 'P', 'M', 'G', 'GG'] as $size)
                    <div class="text-center p-1 bg-gray-100 rounded">
                        <div class="font-medium">{{ $size }}</div>
                        <div>{{ $item->sizes[$size] ?? 0 }}</div>
                    </div>
                    @endforeach
                </div>
                @if(isset($item->sizes['EXG']) || isset($item->sizes['G1']) || isset($item->sizes['G2']) || isset($item->sizes['G3']) || isset($item->sizes['ESPECIAL']))
                <div class="grid grid-cols-5 gap-1 mobile-text-xs mt-1">
                    @foreach(['EXG', 'G1', 'G2', 'G3', 'ESPECIAL'] as $size)
                    @if(isset($item->sizes[$size]) && $item->sizes[$size] > 0)
                    <div class="text-center p-1 bg-gray-100 rounded">
                        <div class="font-medium">{{ $size }}</div>
                        <div>{{ $item->sizes[$size] }}</div>
                    </div>
                    @endif
                    @endforeach
                </div>
                @endif
            </div>
        </div>
        @endforeach

        <!-- Resumo Financeiro -->
        <div class="bg-white rounded-lg shadow p-4 mb-4">
            <h2 class="text-lg font-semibold mb-3">Resumo Financeiro</h2>
            <div class="space-y-2 mobile-text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Total do Pedido:</span>
                    <span class="font-bold text-lg">R$ {{ number_format($order->total, 2, ',', '.') }}</span>
                </div>
                @if($payment)
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Pago:</span>
                    <span class="font-medium text-green-600">R$ {{ number_format($payment->entry_amount, 2, ',', '.') }}</span>
                </div>
                <div class="flex justify-between border-t pt-2">
                    <span class="text-gray-600">Restante:</span>
                    <span class="font-medium {{ $payment->remaining_amount > 0 ? 'text-orange-600' : 'text-green-600' }}">
                        R$ {{ number_format($payment->remaining_amount, 2, ',', '.') }}
                    </span>
                </div>
                @endif
            </div>
        </div>

        <!-- Confirmação do Pedido -->
        @if(!$order->client_confirmed)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
            <h2 class="text-lg font-semibold mb-3 text-yellow-800">Confirme Seu Pedido</h2>
            <p class="text-yellow-700 mobile-text-sm mb-4">
                Por favor, confirme se os dados do seu pedido estão corretos. Esta confirmação é importante para prosseguirmos com a produção.
            </p>
            
            <form method="POST" action="{{ route('client.order.confirm', $order->client_token) }}">
                @csrf
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome Completo</label>
                        <input type="text" 
                               name="client_name" 
                               value="{{ $order->client->name }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 mobile-text-sm"
                               required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                        <input type="tel" 
                               name="client_phone" 
                               value="{{ $order->client->phone_primary }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 mobile-text-sm"
                               required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Observações (opcional)</label>
                        <textarea name="confirmation_notes" 
                                  rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 mobile-text-sm"
                                  placeholder="Alguma observação sobre o pedido..."></textarea>
                    </div>
                </div>
                
                <button type="submit" 
                        class="w-full mt-4 bg-indigo-600 text-white py-3 px-4 rounded-md hover:bg-indigo-700 font-medium mobile-text-sm">
                    ✓ Confirmar Pedido
                </button>
            </form>
        </div>
        @else
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
            <div class="flex items-center mb-2">
                <svg class="w-6 h-6 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <h2 class="text-lg font-semibold text-green-800">Pedido Confirmado</h2>
            </div>
            <p class="text-green-700 mobile-text-sm">
                Obrigado pela confirmação! Seu pedido está em produção. Você pode acompanhar o status aqui mesmo.
            </p>
            @if($order->client_confirmation_notes)
            <div class="mt-3 p-3 bg-white rounded border">
                <p class="text-gray-600 mobile-text-xs">Suas observações:</p>
                <p class="mobile-text-sm">{{ $order->client_confirmation_notes }}</p>
            </div>
            @endif
        </div>
        @endif

        <!-- Observações do Pedido -->
        @if($order->notes)
        <div class="bg-white rounded-lg shadow p-4 mb-4">
            <h2 class="text-lg font-semibold mb-3">Observações</h2>
            <p class="mobile-text-sm text-gray-700">{{ $order->notes }}</p>
        </div>
        @endif

        <!-- Footer -->
        <div class="text-center py-4 mobile-text-xs text-gray-500">
            <p>Para dúvidas, entre em contato conosco</p>
            <p>Tel: (11) 99999-9999 | Email: contato@empresa.com</p>
        </div>
    </div>

    <script>
        // Auto-refresh da página a cada 30 segundos para atualizar o status
        setTimeout(function() {
            location.reload();
        }, 30000);

        // Debug: Verificar se as imagens estão carregando
        document.addEventListener('DOMContentLoaded', function() {
            const images = document.querySelectorAll('.cover-image');
            console.log('Imagens encontradas:', images.length);
            
            images.forEach(function(img, index) {
                console.log('Imagem ' + (index + 1) + ':', img.src);
                
                img.addEventListener('load', function() {
                    console.log('Imagem ' + (index + 1) + ' carregada com sucesso');
                });
                
                img.addEventListener('error', function() {
                    console.log('Erro ao carregar imagem ' + (index + 1) + ':', img.src);
                });
            });
        });
    </script>
</body>
</html>
