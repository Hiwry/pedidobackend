<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Editar Transação - Caixa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <x-app-header />

    <div class="max-w-2xl mx-auto p-6">
        <h1 class="text-2xl font-semibold mb-6">Editar Transação</h1>

        @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <strong>Erro ao atualizar transação:</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="bg-white rounded-lg shadow p-6">
            <form method="POST" action="{{ route('cash.update', $cash) }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                            Tipo *
                        </label>
                        <select id="type" 
                                name="type" 
                                required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('type') border-red-500 @enderror">
                            <option value="">Selecione...</option>
                            <option value="entrada" {{ old('type', $cash->type) === 'entrada' ? 'selected' : '' }}>Entrada</option>
                            <option value="saida" {{ old('type', $cash->type) === 'saida' ? 'selected' : '' }}>Saída</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                            Categoria *
                        </label>
                        <input type="text" 
                               id="category" 
                               name="category" 
                               value="{{ old('category', $cash->category) }}"
                               required
                               placeholder="Ex: Venda, Compra, Despesa"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('category') border-red-500 @enderror">
                        @error('category')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Descrição *
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="3"
                              required
                              placeholder="Descreva a transação..."
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('description') border-red-500 @enderror">{{ old('description', $cash->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                            Valor (R$) *
                        </label>
                        <input type="number" 
                               id="amount" 
                               name="amount" 
                               step="0.01"
                               min="0.01"
                               value="{{ old('amount', $cash->amount) }}"
                               required
                               placeholder="0,00"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('amount') border-red-500 @enderror">
                        @error('amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">
                            Forma de Pagamento *
                        </label>
                        <select id="payment_method" 
                                name="payment_method" 
                                required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('payment_method') border-red-500 @enderror">
                            <option value="">Selecione...</option>
                            <option value="dinheiro" {{ old('payment_method', $cash->payment_method) === 'dinheiro' ? 'selected' : '' }}>Dinheiro</option>
                            <option value="pix" {{ old('payment_method', $cash->payment_method) === 'pix' ? 'selected' : '' }}>PIX</option>
                            <option value="cartao" {{ old('payment_method', $cash->payment_method) === 'cartao' ? 'selected' : '' }}>Cartão</option>
                            <option value="transferencia" {{ old('payment_method', $cash->payment_method) === 'transferencia' ? 'selected' : '' }}>Transferência</option>
                            <option value="boleto" {{ old('payment_method', $cash->payment_method) === 'boleto' ? 'selected' : '' }}>Boleto</option>
                        </select>
                        @error('payment_method')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label for="transaction_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Data da Transação *
                    </label>
                    <input type="date" 
                           id="transaction_date" 
                           name="transaction_date" 
                           value="{{ old('transaction_date', $cash->transaction_date->format('Y-m-d')) }}"
                           required
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('transaction_date') border-red-500 @enderror">
                    @error('transaction_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Observações
                    </label>
                    <textarea id="notes" 
                              name="notes" 
                              rows="2"
                              placeholder="Observações adicionais (opcional)"
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes', $cash->notes) }}</textarea>
                </div>

                <div class="flex justify-between">
                    <a href="{{ route('cash.index') }}" 
                       class="px-4 py-2 text-gray-600 hover:text-gray-900">
                        ← Voltar
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Atualizar Transação
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
