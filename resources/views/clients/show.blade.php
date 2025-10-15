<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $client->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('clients.edit', $client->id) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    Editar Cliente
                </a>
                <a href="{{ route('clients.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                    Voltar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Mensagens -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Estatísticas -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Total de Pedidos -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total de Pedidos</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_orders'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Gasto -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total Gasto</p>
                                <p class="text-2xl font-semibold text-gray-900">R$ {{ number_format($stats['total_spent'], 2, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ticket Médio -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-100 rounded-md p-3">
                                <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Ticket Médio</p>
                                <p class="text-2xl font-semibold text-gray-900">R$ {{ number_format($stats['average_order'], 2, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Saldo Pendente -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 {{ $stats['pending_balance'] > 0 ? 'bg-red-100' : 'bg-gray-100' }} rounded-md p-3">
                                <svg class="h-6 w-6 {{ $stats['pending_balance'] > 0 ? 'text-red-600' : 'text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Saldo Pendente</p>
                                <p class="text-2xl font-semibold {{ $stats['pending_balance'] > 0 ? 'text-red-600' : 'text-gray-900' }}">
                                    R$ {{ number_format($stats['pending_balance'], 2, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informações do Cliente -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informações do Cliente</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Nome</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $client->name }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">CPF/CNPJ</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $client->cpf_cnpj ?? '-' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Telefone Principal</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $client->phone_primary }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Telefone Secundário</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $client->phone_secondary ?? '-' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Email</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $client->email ?? '-' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Categoria</label>
                            <p class="mt-1 text-sm text-gray-900">
                                @if($client->category)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $client->category }}
                                    </span>
                                @else
                                    -
                                @endif
                            </p>
                        </div>

                        @if($client->address)
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-500">Endereço</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $client->address }}
                                    @if($client->city || $client->state)
                                        <br>{{ $client->city }}{{ $client->state ? ' - ' . $client->state : '' }}
                                        @if($client->zip_code)
                                            - CEP: {{ $client->zip_code }}
                                        @endif
                                    @endif
                                </p>
                            </div>
                        @endif

                        @if($stats['last_order_date'])
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Último Pedido</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $stats['last_order_date']->format('d/m/Y H:i') }}</p>
                            </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Cliente desde</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $client->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Histórico de Pedidos -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Histórico de Pedidos</h3>
                    
                    @if($client->orders->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Pedido
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Data
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Itens
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Total
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Saldo
                                        </th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Ações
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($client->orders as $order)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    #{{ $order->id }}
                                                </div>
                                                @if($order->delivery_date)
                                                    <div class="text-xs text-gray-500">
                                                        Entrega: {{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $order->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                                                      style="background-color: {{ $order->status->color }}20; color: {{ $order->status->color }}">
                                                    {{ $order->status->name }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                                {{ $order->items->count() }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                                                R$ {{ number_format($order->total, 2, ',', '.') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                                @php
                                                    $paidAmount = $order->payments->sum('amount');
                                                    $balanceDue = $order->total - $paidAmount;
                                                @endphp
                                                <span class="{{ $balanceDue > 0 ? 'text-red-600 font-medium' : 'text-green-600' }}">
                                                    R$ {{ number_format($balanceDue, 2, ',', '.') }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                                <a href="{{ route('orders.show', $order->id) }}" class="text-blue-600 hover:text-blue-900">
                                                    Ver Pedido
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum pedido encontrado</h3>
                            <p class="mt-1 text-sm text-gray-500">Este cliente ainda não possui pedidos.</p>
                            <div class="mt-6">
                                <a href="{{ route('orders.wizard.start') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    + Novo Pedido
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

