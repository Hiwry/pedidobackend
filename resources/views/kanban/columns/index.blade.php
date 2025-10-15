<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gerenciar Colunas do Kanban - Sistema de Pedidos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
</head>
<body class="bg-gray-100">
    <x-app-header />

    <div class="max-w-7xl mx-auto p-6">
        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
        @endif

        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-semibold">Gerenciar Colunas do Kanban</h1>
                <p class="text-gray-600 mt-1">Configure as colunas e sua ordem no Kanban de produção</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('kanban.index') }}" 
                   class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                    ← Voltar ao Kanban
                </a>
                <a href="{{ route('kanban.columns.create') }}" 
                   class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    + Nova Coluna
                </a>
            </div>
        </div>

        <!-- Lista de Colunas -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium">Colunas do Kanban</h2>
                <p class="text-sm text-gray-600">Arraste para reordenar as colunas</p>
            </div>

            @if($statuses->count() > 0)
            <div id="sortable-columns" class="divide-y divide-gray-200">
                @foreach($statuses as $status)
                <div class="px-6 py-4 hover:bg-gray-50 transition-colors" data-status-id="{{ $status->id }}">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <!-- Ícone de arrastar -->
                            <div class="cursor-move text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                                </svg>
                            </div>

                            <!-- Cor e Nome -->
                            <div class="flex items-center space-x-3">
                                <div class="w-6 h-6 rounded-full border-2 border-gray-300" 
                                     style="background-color: {{ $status->color }}"></div>
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ $status->name }}</h3>
                                    <p class="text-sm text-gray-500">Posição: {{ $status->position }}</p>
                                </div>
                            </div>

                            <!-- Contador de Pedidos -->
                            <div class="text-sm text-gray-600">
                                <span class="bg-gray-100 px-2 py-1 rounded-full">
                                    {{ $status->orders_count ?? 0 }} pedido(s)
                                </span>
                            </div>
                        </div>

                        <!-- Ações -->
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('kanban.columns.edit', $status) }}" 
                               class="px-3 py-1 text-sm bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200">
                                Editar
                            </a>
                            
                            @if($status->orders_count > 0)
                            <button onclick="openMoveModal({{ $status->id }}, '{{ $status->name }}', {{ $status->orders_count }})"
                                    class="px-3 py-1 text-sm bg-yellow-100 text-yellow-700 rounded-md hover:bg-yellow-200">
                                Mover Pedidos
                            </button>
                            @endif

                            <form method="POST" action="{{ route('kanban.columns.destroy', $status) }}" 
                                  class="inline" 
                                  onsubmit="return confirm('Tem certeza que deseja excluir esta coluna?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="px-3 py-1 text-sm bg-red-100 text-red-700 rounded-md hover:bg-red-200
                                               {{ $status->orders_count > 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                        {{ $status->orders_count > 0 ? 'disabled' : '' }}>
                                    Excluir
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="px-6 py-12 text-center">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhuma coluna encontrada</h3>
                <p class="text-gray-600 mb-4">Comece criando sua primeira coluna do Kanban.</p>
                <a href="{{ route('kanban.columns.create') }}" 
                   class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Criar Primeira Coluna
                </a>
            </div>
            @endif
        </div>

        <!-- Botão Salvar Ordem -->
        @if($statuses->count() > 1)
        <div class="mt-6 text-center">
            <button onclick="saveOrder()" 
                    class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                Salvar Ordem das Colunas
            </button>
        </div>
        @endif
    </div>

    <!-- Modal para Mover Pedidos -->
    <div id="moveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium">Mover Pedidos</h3>
                </div>
                <div class="px-6 py-4">
                    <p class="text-gray-600 mb-4">
                        Mover <span id="moveCount">0</span> pedido(s) da coluna 
                        <strong id="moveFromColumn">-</strong> para:
                    </p>
                    <form id="moveForm" method="POST">
                        @csrf
                        <select name="target_status_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Selecione a coluna de destino</option>
                            @foreach($statuses as $status)
                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
                <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                    <button onclick="closeMoveModal()" 
                            class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                        Cancelar
                    </button>
                    <button onclick="submitMove()" 
                            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Mover Pedidos
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Inicializar Sortable
        const sortable = new Sortable(document.getElementById('sortable-columns'), {
            animation: 150,
            ghostClass: 'opacity-50',
            handle: '.cursor-move'
        });

        // Salvar ordem
        function saveOrder() {
            const statusIds = Array.from(document.querySelectorAll('[data-status-id]'))
                .map(el => parseInt(el.dataset.statusId)); // Converter para inteiro

            fetch('{{ route("kanban.columns.reorder") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ statuses: statusIds })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.message || 'Erro ao salvar ordem');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert('Ordem salva com sucesso!');
                    location.reload();
                } else {
                    alert('Erro ao salvar ordem: ' + (data.message || 'Erro desconhecido'));
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao salvar ordem das colunas: ' + error.message);
            });
        }

        // Modal de mover pedidos
        let currentStatusId = null;

        function openMoveModal(statusId, statusName, ordersCount) {
            currentStatusId = statusId;
            document.getElementById('moveCount').textContent = ordersCount;
            document.getElementById('moveFromColumn').textContent = statusName;
            document.getElementById('moveForm').action = `{{ url('kanban/columns') }}/${statusId}/move-orders`;
            document.getElementById('moveModal').classList.remove('hidden');
        }

        function closeMoveModal() {
            document.getElementById('moveModal').classList.add('hidden');
            currentStatusId = null;
        }

        function submitMove() {
            const form = document.getElementById('moveForm');
            const targetStatusId = form.target_status_id.value;
            
            if (!targetStatusId) {
                alert('Selecione uma coluna de destino');
                return;
            }

            form.submit();
        }

        // Fechar modal ao clicar fora
        document.getElementById('moveModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeMoveModal();
            }
        });
    </script>
</body>
</html>
