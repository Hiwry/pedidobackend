<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Caixa - Sistema de Pedidos</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <x-app-header />

    <div class="max-w-7xl mx-auto p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold">Controle de Caixa</h1>
            <a href="{{ route('cash.create') }}" 
               class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                + Nova Transação
            </a>
        </div>

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
        @endif

        <!-- Cards de Resumo -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
            <!-- Saldo Atual (Confirmado) -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                <p class="text-xs text-gray-600 mb-1">💰 Saldo Atual</p>
                <p class="text-2xl font-bold {{ $saldoAtual >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                    R$ {{ number_format($saldoAtual, 2, ',', '.') }}
                </p>
                <p class="text-xs text-gray-500 mt-1">Dinheiro em caixa</p>
            </div>

            <!-- Saldo Geral -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                <p class="text-xs text-gray-600 mb-1">📊 Saldo Geral</p>
                <p class="text-2xl font-bold {{ $saldoGeral >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    R$ {{ number_format($saldoGeral, 2, ',', '.') }}
                </p>
                <p class="text-xs text-gray-500 mt-1">Total acumulado</p>
            </div>

            <!-- Saldo Pendente -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-orange-500">
                <p class="text-xs text-gray-600 mb-1">⏳ Saldo Pendente</p>
                <p class="text-2xl font-bold text-orange-600">
                    R$ {{ number_format($saldoPendente, 2, ',', '.') }}
                </p>
                <p class="text-xs text-gray-500 mt-1">Aguardando conclusão</p>
            </div>

            <!-- Saídas Totais -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
                <p class="text-xs text-gray-600 mb-1">📤 Saídas Totais</p>
                <p class="text-2xl font-bold text-red-600">
                    R$ {{ number_format($totalSaidasGeral, 2, ',', '.') }}
                </p>
                <p class="text-xs text-gray-500 mt-1">Todas as despesas</p>
            </div>

            <!-- Saldo do Período -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                <p class="text-xs text-gray-600 mb-1">📅 Saldo (Período)</p>
                <p class="text-2xl font-bold {{ $saldoPeriodo >= 0 ? 'text-purple-600' : 'text-red-600' }}">
                    R$ {{ number_format($saldoPeriodo, 2, ',', '.') }}
                </p>
                <p class="text-xs text-gray-500 mt-1">Filtro aplicado</p>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <form method="GET" action="{{ route('cash.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data Inicial</label>
                    <input type="date" name="start_date" value="{{ $startDate }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data Final</label>
                    <input type="date" name="end_date" value="{{ $endDate }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                    <select name="type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="all" {{ $type === 'all' ? 'selected' : '' }}>Todos</option>
                        <option value="entrada" {{ $type === 'entrada' ? 'selected' : '' }}>Entradas</option>
                        <option value="saida" {{ $type === 'saida' ? 'selected' : '' }}>Saídas</option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Filtrar
                    </button>
                    <a href="{{ route('cash.index') }}" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-center">
                        Limpar
                    </a>
                </div>
            </form>
        </div>

        <!-- Tabela de Transações -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categoria</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descrição</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pagamento</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuário</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($transactions as $transaction)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $transaction->transaction_date->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $transaction->status === 'confirmado' ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800' }}">
                                {{ $transaction->status === 'confirmado' ? '✓ Confirmado' : '⏳ Pendente' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $transaction->type === 'entrada' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $transaction->type === 'entrada' ? 'Entrada' : 'Saída' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $transaction->category }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $transaction->description }}
                            @if($transaction->order_id)
                                <br>
                                <a href="{{ route('orders.show', $transaction->order_id) }}" 
                                   class="text-xs text-indigo-600 hover:text-indigo-800 hover:underline font-medium">
                                    Pedido #{{ str_pad($transaction->order_id, 6, '0', STR_PAD_LEFT) }}
                                </a>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 capitalize">
                            {{ $transaction->payment_method }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium 
                            {{ $transaction->type === 'entrada' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $transaction->type === 'entrada' ? '+' : '-' }} R$ {{ number_format($transaction->amount, 2, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $transaction->user_name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('cash.edit', $transaction) }}" 
                               class="text-indigo-600 hover:text-indigo-900 mr-3">
                                Editar
                            </a>
                            <form action="{{ route('cash.destroy', $transaction) }}" 
                                  method="POST" 
                                  class="inline"
                                  onsubmit="return confirm('Tem certeza que deseja excluir esta transação?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    Excluir
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                            Nenhuma transação encontrada no período selecionado.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
