<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gerenciar Opções de Produtos</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <x-app-header />

    <div class="max-w-7xl mx-auto p-6">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-md">
            <!-- Tabs -->
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px">
                    @foreach($types as $key => $label)
                        <a href="{{ route('admin.product-options.index', ['type' => $key]) }}"
                           class="px-6 py-3 text-sm font-medium border-b-2 {{ $type === $key ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </nav>
            </div>

            <!-- Header -->
            <div class="p-6 flex items-center justify-between border-b">
                <div>
                    <h2 class="text-xl font-semibold">{{ $types[$type] }}</h2>
                    <p class="text-sm text-gray-600 mt-1">Gerencie as opções de {{ strtolower($types[$type]) }}</p>
                </div>
                <a href="{{ route('admin.product-options.create', ['type' => $type]) }}"
                   class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    + Nova Opção
                </a>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Preço</th>
                            @if(in_array($type, ['tecido', 'tipo_tecido', 'cor', 'tipo_corte', 'detalhe', 'gola']))
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pai</th>
                            @endif
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ordem</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($options as $option)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $option->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        @if($option->price > 0)
                                            <span class="text-green-600 font-medium">+R$ {{ number_format($option->price, 2, ',', '.') }}</span>
                                        @else
                                            <span class="text-gray-400">R$ 0,00</span>
                                        @endif
                                    </div>
                                </td>
                                @if(in_array($type, ['tecido', 'tipo_tecido', 'cor', 'tipo_corte', 'detalhe', 'gola']))
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-600">
                                            @if($option->parents->count() > 0)
                                                @foreach($option->parents as $parent)
                                                    <span class="inline-block px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded mr-1 mb-1">
                                                        {{ $parent->name }}
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </div>
                                    </td>
                                @endif
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-600">{{ $option->order }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($option->active)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            Ativo
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            Inativo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.product-options.edit', $option->id) }}"
                                       class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        Editar
                                    </a>
                                    <form action="{{ route('admin.product-options.destroy', $option->id) }}"
                                          method="POST" class="inline"
                                          onsubmit="return confirm('Tem certeza que deseja remover esta opção?')">
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
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <div class="text-lg mb-2">Nenhuma opção cadastrada</div>
                                    <a href="{{ route('admin.product-options.create', ['type' => $type]) }}"
                                       class="text-indigo-600 hover:text-indigo-800">
                                        Criar primeira opção
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($options->hasPages())
                <div class="px-6 py-4 border-t">
                    {{ $options->appends(['type' => $type])->links() }}
                </div>
            @endif
        </div>
    </div>
</body>
</html>
