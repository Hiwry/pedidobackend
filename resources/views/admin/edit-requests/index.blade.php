<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gerenciar Edições - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
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

        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h1 class="text-2xl font-semibold text-gray-900">Gerenciar Edições</h1>
                <p class="text-sm text-gray-600 mt-1">Aprove ou rejeite solicitações de edição de pedidos</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Pedido
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Cliente
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Solicitado por
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Motivo
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Alterações
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Data
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($editRequests as $editRequest)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm font-medium text-gray-900">
                                        #{{ str_pad($editRequest->order->id, 6, '0', STR_PAD_LEFT) }}
                                    </div>
                                    <div class="ml-2 text-sm text-gray-500">
                                        R$ {{ number_format($editRequest->order->total, 2, ',', '.') }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $editRequest->order->client->name }}</div>
                                <div class="text-sm text-gray-500">{{ $editRequest->order->client->phone_primary }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $editRequest->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $editRequest->created_at->format('d/m/Y H:i') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $editRequest->reason }}">
                                    {{ $editRequest->reason }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <button onclick="showChanges({{ $editRequest->id }})" 
                                        class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                    Ver Alterações
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($editRequest->status === 'pending')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Pendente
                                    </span>
                                @elseif($editRequest->status === 'approved')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Aprovado
                                    </span>
                                @elseif($editRequest->status === 'rejected')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        Rejeitado
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        Concluído
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $editRequest->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @if($editRequest->status === 'pending')
                                    <div class="flex space-x-2">
                                        <button onclick="approveEdit({{ $editRequest->id }})" 
                                                class="text-green-600 hover:text-green-900 bg-green-50 px-3 py-1 rounded-md hover:bg-green-100 transition-colors">
                                            Aprovar
                                        </button>
                                        <button onclick="rejectEdit({{ $editRequest->id }})" 
                                                class="text-red-600 hover:text-red-900 bg-red-50 px-3 py-1 rounded-md hover:bg-red-100 transition-colors">
                                            Rejeitar
                                        </button>
                                    </div>
                                @elseif($editRequest->status === 'approved')
                                    <div class="text-sm text-gray-500">
                                        Aguardando implementação
                                    </div>
                                @else
                                    <div class="text-sm text-gray-500">
                                        @if($editRequest->approvedBy)
                                            Processado por: {{ $editRequest->approvedBy->name }}
                                        @endif
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                Nenhuma solicitação de edição encontrada.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($editRequests->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $editRequests->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- Modal de Alterações -->
    <div id="changes-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[80vh] overflow-y-auto">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Alterações Propostas</h3>
                </div>
                <div id="changes-content" class="px-6 py-4">
                    <!-- Conteúdo será carregado via JavaScript -->
                </div>
                <div class="px-6 py-4 bg-gray-50 flex justify-end">
                    <button onclick="closeChangesModal()" 
                            class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Aprovação -->
    <div id="approve-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Aprovar Edição</h3>
                </div>
                <form id="approve-form" method="POST">
                    @csrf
                    <div class="px-6 py-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Observações (opcional)
                        </label>
                        <textarea name="admin_notes" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                  placeholder="Adicione observações sobre a aprovação..."></textarea>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                        <button type="button" onclick="closeApproveModal()" 
                                class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                            Cancelar
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            Aprovar Edição
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Rejeição -->
    <div id="reject-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Rejeitar Edição</h3>
                </div>
                <form id="reject-form" method="POST">
                    @csrf
                    <div class="px-6 py-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Motivo da Rejeição *
                        </label>
                        <textarea name="admin_notes" rows="3" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                  placeholder="Explique o motivo da rejeição..."></textarea>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                        <button type="button" onclick="closeRejectModal()" 
                                class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                            Cancelar
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            Rejeitar Edição
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showChanges(editRequestId) {
            // Buscar dados da edição
            fetch(`/admin/edit-requests/${editRequestId}/changes`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayChanges(data.changes);
                        document.getElementById('changes-modal').classList.remove('hidden');
                    } else {
                        alert('Erro ao carregar alterações');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao carregar alterações');
                });
        }

        function displayChanges(changes) {
            const content = document.getElementById('changes-content');
            let html = '';

            Object.keys(changes).forEach(section => {
                html += `<div class="mb-6">`;
                html += `<h4 class="text-lg font-medium text-gray-900 mb-3">${getSectionTitle(section)}</h4>`;
                
                if (section === 'items') {
                    Object.keys(changes[section]).forEach(itemId => {
                        html += `<div class="bg-gray-50 p-4 rounded-lg mb-3">`;
                        html += `<h5 class="font-medium mb-2">Item ID: ${itemId}</h5>`;
                        Object.keys(changes[section][itemId]).forEach(field => {
                            const change = changes[section][itemId][field];
                            html += `<div class="mb-2">`;
                            html += `<span class="font-medium">${getFieldTitle(field)}:</span> `;
                            html += `<span class="text-red-600">${change.old}</span> → `;
                            html += `<span class="text-green-600">${change.new}</span>`;
                            html += `</div>`;
                        });
                        html += `</div>`;
                    });
                } else {
                    Object.keys(changes[section]).forEach(field => {
                        const change = changes[section][field];
                        html += `<div class="mb-2">`;
                        html += `<span class="font-medium">${getFieldTitle(field)}:</span> `;
                        html += `<span class="text-red-600">${change.old}</span> → `;
                        html += `<span class="text-green-600">${change.new}</span>`;
                        html += `</div>`;
                    });
                }
                
                html += `</div>`;
            });

            content.innerHTML = html;
        }

        function getSectionTitle(section) {
            const titles = {
                'client': 'Dados do Cliente',
                'items': 'Itens do Pedido',
                'personalization': 'Personalização',
                'payment': 'Pagamento e Valores',
                'notes': 'Observações'
            };
            return titles[section] || section;
        }

        function getFieldTitle(field) {
            const titles = {
                'name': 'Nome',
                'phone_primary': 'Telefone',
                'email': 'Email',
                'cpf_cnpj': 'CPF/CNPJ',
                'address': 'Endereço',
                'print_type': 'Tipo de Personalização',
                'art_name': 'Nome da Arte',
                'quantity': 'Quantidade',
                'fabric': 'Tecido',
                'color': 'Cor',
                'unit_price': 'Preço Unitário',
                'contract_type': 'Tipo de Contrato',
                'seller': 'Vendedor',
                'delivery_date': 'Data de Entrega',
                'subtotal': 'Subtotal',
                'discount': 'Desconto',
                'delivery_fee': 'Taxa de Entrega',
                'total': 'Total'
            };
            return titles[field] || field;
        }

        function approveEdit(editRequestId) {
            document.getElementById('approve-form').action = `/admin/edit-requests/${editRequestId}/approve`;
            document.getElementById('approve-modal').classList.remove('hidden');
        }

        function rejectEdit(editRequestId) {
            document.getElementById('reject-form').action = `/admin/edit-requests/${editRequestId}/reject`;
            document.getElementById('reject-modal').classList.remove('hidden');
        }

        function closeChangesModal() {
            document.getElementById('changes-modal').classList.add('hidden');
        }

        function closeApproveModal() {
            document.getElementById('approve-modal').classList.add('hidden');
        }

        function closeRejectModal() {
            document.getElementById('reject-modal').classList.add('hidden');
        }

        // Fechar modais ao clicar fora
        document.getElementById('changes-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeChangesModal();
            }
        });

        document.getElementById('approve-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeApproveModal();
            }
        });

        document.getElementById('reject-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeRejectModal();
            }
        });
    </script>
</body>
</html>
