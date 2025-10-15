<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Teste - Caixa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold mb-4">Teste de Autenticação e Dados</h1>
        
        <div class="mb-4 p-4 bg-blue-50 rounded">
            <h2 class="font-semibold mb-2">Informações do Usuário:</h2>
            @auth
                <p>✅ Usuário autenticado: {{ Auth::user()->name }}</p>
                <p>ID: {{ Auth::user()->id }}</p>
                <p>Email: {{ Auth::user()->email }}</p>
            @else
                <p class="text-red-600">❌ Usuário NÃO autenticado</p>
            @endauth
        </div>

        <div class="mb-4 p-4 bg-green-50 rounded">
            <h2 class="font-semibold mb-2">Total de Transações no Banco:</h2>
            <p>{{ \App\Models\CashTransaction::count() }} transações registradas</p>
        </div>

        <div class="mb-4 p-4 bg-yellow-50 rounded">
            <h2 class="font-semibold mb-2">Últimas 5 Transações:</h2>
            @php
                $transactions = \App\Models\CashTransaction::orderBy('created_at', 'desc')->limit(5)->get();
            @endphp
            
            @if($transactions->count() > 0)
                <ul class="space-y-2">
                    @foreach($transactions as $t)
                        <li class="text-sm">
                            <strong>ID {{ $t->id }}:</strong> 
                            {{ $t->type }} - R$ {{ number_format($t->amount, 2, ',', '.') }} 
                            - {{ $t->description }}
                            ({{ $t->created_at->format('d/m/Y H:i') }})
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-500">Nenhuma transação encontrada</p>
            @endif
        </div>

        <div class="flex gap-4">
            <a href="{{ route('cash.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Ir para Caixa
            </a>
            <a href="{{ route('cash.create') }}" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                Nova Transação
            </a>
        </div>
    </div>
</body>
</html>
