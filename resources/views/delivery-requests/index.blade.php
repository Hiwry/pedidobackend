<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Solicitações de Antecipação - Sistema de Pedidos</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <x-app-header />

    <div class="max-w-7xl mx-auto p-6">
        <h1 class="text-2xl font-semibold mb-6">Solicitações de Antecipação de Entrega</h1>

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
        @endif

        <!-- Tabs -->
        <div class="mb-6 border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
                <button onclick="showTab('pendente')" id="tab-pendente" class="tab-button border-b-2 border-indigo-500 py-4 px-1 text-sm font-medium text-indigo-600">
                    Pendentes ({{ $requests->where('status', 'pendente')->count() }})
                </button>
                <button onclick="showTab('aprovado')" id="tab-aprovado" class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Aprovadas ({{ $requests->where('status', 'aprovado')->count() }})
                </button>
                <button onclick="showTab('rejeitado')" id="tab-rejeitado" class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Rejeitadas ({{ $requests->where('status', 'rejeitado')->count() }})
                </button>
            </nav>
        </div>

        <!-- Solicitações Pendentes -->
        <div id="content-pendente" class="tab-content">
            @forelse($requests->where('status', 'pendente') as $request)
            <div class="bg-white rounded-lg shadow p-6 mb-4">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-3">
                            <h3 class="text-lg font-semibold">Pedido #{{ str_pad($request->order->id, 6, '0', STR_PAD_LEFT) }}</h3>
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">
                                Pendente
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <p class="text-sm text-gray-600"><strong>Cliente:</strong> {{ $request->order->client->name }}</p>
                                <p class="text-sm text-gray-600"><strong>Solicitado por:</strong> {{ $request->requested_by_name }}</p>
                                <p class="text-sm text-gray-600"><strong>Data da Solicitação:</strong> {{ $request->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600"><strong>Data Atual:</strong> <span class="text-orange-600 font-semibold">{{ \Carbon\Carbon::parse($request->current_delivery_date)->format('d/m/Y') }}</span></p>
                                <p class="text-sm text-gray-600"><strong>Data Solicitada:</strong> <span class="text-green-600 font-semibold">{{ \Carbon\Carbon::parse($request->requested_delivery_date)->format('d/m/Y') }}</span></p>
                                <p class="text-sm text-gray-600"><strong>Antecipação:</strong> {{ \Carbon\Carbon::parse($request->current_delivery_date)->diffInDays(\Carbon\Carbon::parse($request->requested_delivery_date)) }} dias</p>
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded p-3 mb-4">
                            <p class="text-sm font-semibold text-gray-700 mb-1">Motivo:</p>
                            <p class="text-sm text-gray-600">{{ $request->reason }}</p>
                        </div>

                        <div class="flex gap-3">
                            <button onclick="openApproveModal({{ $request->id }})" 
                                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">
                                ✓ Aprovar
                            </button>
                            <button onclick="openRejectModal({{ $request->id }})" 
                                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm">
                                ✗ Rejeitar
                            </button>
                            <a href="{{ route('kanban.index') }}" 
                               class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm">
                                Ver Pedido
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-lg shadow p-6 text-center text-gray-500">
                Nenhuma solicitação pendente.
            </div>
            @endforelse
        </div>

        <!-- Solicitações Aprovadas -->
        <div id="content-aprovado" class="tab-content hidden">
            @forelse($requests->where('status', 'aprovado') as $request)
            <div class="bg-white rounded-lg shadow p-6 mb-4">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-3">
                            <h3 class="text-lg font-semibold">Pedido #{{ str_pad($request->order->id, 6, '0', STR_PAD_LEFT) }}</h3>
                            <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                Aprovada
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <p class="text-sm text-gray-600"><strong>Cliente:</strong> {{ $request->order->client->name }}</p>
                                <p class="text-sm text-gray-600"><strong>Solicitado por:</strong> {{ $request->requested_by_name }}</p>
                                <p class="text-sm text-gray-600"><strong>Aprovado por:</strong> {{ $request->reviewed_by_name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600"><strong>Data Original:</strong> {{ \Carbon\Carbon::parse($request->current_delivery_date)->format('d/m/Y') }}</p>
                                <p class="text-sm text-gray-600"><strong>Nova Data:</strong> <span class="text-green-600 font-semibold">{{ \Carbon\Carbon::parse($request->requested_delivery_date)->format('d/m/Y') }}</span></p>
                                <p class="text-sm text-gray-600"><strong>Aprovado em:</strong> {{ $request->reviewed_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        @if($request->review_notes)
                        <div class="bg-green-50 rounded p-3">
                            <p class="text-sm font-semibold text-green-700 mb-1">Observações do Admin:</p>
                            <p class="text-sm text-green-600">{{ $request->review_notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-lg shadow p-6 text-center text-gray-500">
                Nenhuma solicitação aprovada.
            </div>
            @endforelse
        </div>

        <!-- Solicitações Rejeitadas -->
        <div id="content-rejeitado" class="tab-content hidden">
            @forelse($requests->where('status', 'rejeitado') as $request)
            <div class="bg-white rounded-lg shadow p-6 mb-4">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-3">
                            <h3 class="text-lg font-semibold">Pedido #{{ str_pad($request->order->id, 6, '0', STR_PAD_LEFT) }}</h3>
                            <span class="px-3 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">
                                Rejeitada
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <p class="text-sm text-gray-600"><strong>Cliente:</strong> {{ $request->order->client->name }}</p>
                                <p class="text-sm text-gray-600"><strong>Solicitado por:</strong> {{ $request->requested_by_name }}</p>
                                <p class="text-sm text-gray-600"><strong>Rejeitado por:</strong> {{ $request->reviewed_by_name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600"><strong>Data Solicitada:</strong> {{ \Carbon\Carbon::parse($request->requested_delivery_date)->format('d/m/Y') }}</p>
                                <p class="text-sm text-gray-600"><strong>Rejeitado em:</strong> {{ $request->reviewed_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        @if($request->review_notes)
                        <div class="bg-red-50 rounded p-3">
                            <p class="text-sm font-semibold text-red-700 mb-1">Motivo da Rejeição:</p>
                            <p class="text-sm text-red-600">{{ $request->review_notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-lg shadow p-6 text-center text-gray-500">
                Nenhuma solicitação rejeitada.
            </div>
            @endforelse
        </div>
    </div>

    <!-- Modal de Aprovação -->
    <div id="approve-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4 pb-4 border-b">
                <h3 class="text-xl font-bold text-gray-900">Aprovar Solicitação</h3>
                <button onclick="closeApproveModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="approve-form" method="POST">
                @csrf
                <div class="mb-6">
                    <label for="approve-notes" class="block text-sm font-medium text-gray-700 mb-2">Observações (opcional)</label>
                    <textarea id="approve-notes" 
                              name="review_notes" 
                              rows="3"
                              maxlength="500"
                              placeholder="Adicione observações sobre a aprovação..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" 
                            onclick="closeApproveModal()"
                            class="px-4 py-2 text-gray-600 hover:text-gray-900">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Aprovar Solicitação
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Rejeição -->
    <div id="reject-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4 pb-4 border-b">
                <h3 class="text-xl font-bold text-gray-900">Rejeitar Solicitação</h3>
                <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="reject-form" method="POST">
                @csrf
                <div class="mb-6">
                    <label for="reject-notes" class="block text-sm font-medium text-gray-700 mb-2">Motivo da Rejeição *</label>
                    <textarea id="reject-notes" 
                              name="review_notes" 
                              rows="3"
                              required
                              maxlength="500"
                              placeholder="Explique o motivo da rejeição..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" 
                            onclick="closeRejectModal()"
                            class="px-4 py-2 text-gray-600 hover:text-gray-900">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        Rejeitar Solicitação
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showTab(status) {
            // Esconder todos os conteúdos
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            
            // Remover estilo ativo de todos os botões
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('border-indigo-500', 'text-indigo-600');
                btn.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Mostrar conteúdo selecionado
            document.getElementById('content-' + status).classList.remove('hidden');
            
            // Ativar botão selecionado
            const activeBtn = document.getElementById('tab-' + status);
            activeBtn.classList.remove('border-transparent', 'text-gray-500');
            activeBtn.classList.add('border-indigo-500', 'text-indigo-600');
        }

        function openApproveModal(requestId) {
            document.getElementById('approve-form').action = `/delivery-requests/${requestId}/approve`;
            document.getElementById('approve-modal').classList.remove('hidden');
        }

        function closeApproveModal() {
            document.getElementById('approve-modal').classList.add('hidden');
            document.getElementById('approve-form').reset();
        }

        function openRejectModal(requestId) {
            document.getElementById('reject-form').action = `/delivery-requests/${requestId}/reject`;
            document.getElementById('reject-modal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('reject-modal').classList.add('hidden');
            document.getElementById('reject-form').reset();
        }

        // Fechar modais ao clicar fora
        document.getElementById('approve-modal').addEventListener('click', function(e) {
            if (e.target === this) closeApproveModal();
        });

        document.getElementById('reject-modal').addEventListener('click', function(e) {
            if (e.target === this) closeRejectModal();
        });
    </script>
</body>
</html>
