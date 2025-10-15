<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Opção de Produto</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <x-app-header />

    <div class="max-w-3xl mx-auto p-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="mb-6">
                <h1 class="text-2xl font-semibold">Editar Opção: {{ $option->name }}</h1>
                <p class="text-sm text-gray-600 mt-1">{{ $types[$option->type] }}</p>
            </div>

            <form method="POST" action="{{ route('admin.product-options.update', $option->id) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nome *</label>
                    <input type="text" id="name" name="name" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           value="{{ old('name', $option->name) }}">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700">Preço Adicional (R$)</label>
                    <input type="number" id="price" name="price" step="0.01" min="0"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           value="{{ old('price', $option->price) }}">
                    <p class="mt-1 text-xs text-gray-500">Deixe 0 se não houver custo adicional</p>
                    @error('price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                @if(count($parents) > 0)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ $parentLabel }} * (selecione um ou mais)</label>
                        <div class="border border-gray-300 rounded-md p-4 max-h-48 overflow-y-auto bg-gray-50">
                            @foreach($parents as $parent)
                                <div class="flex items-center mb-2">
                                    <input type="checkbox" 
                                           id="parent_{{ $parent->id }}" 
                                           name="parent_ids[]" 
                                           value="{{ $parent->id }}"
                                           {{ in_array($parent->id, old('parent_ids', $option->parents->pluck('id')->toArray())) ? 'checked' : '' }}
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label for="parent_{{ $parent->id }}" class="ml-2 block text-sm text-gray-900">
                                        {{ $parent->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Selecione um ou mais {{ strtolower($parentLabel) }}(s) para associar</p>
                        @error('parent_ids')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <div>
                    <label for="order" class="block text-sm font-medium text-gray-700">Ordem de Exibição</label>
                    <input type="number" id="order" name="order" min="0"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           value="{{ old('order', $option->order) }}">
                    <p class="mt-1 text-xs text-gray-500">Menor número aparece primeiro</p>
                    @error('order')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="active" name="active" value="1" 
                           {{ old('active', $option->active) ? 'checked' : '' }}
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="active" class="ml-2 block text-sm text-gray-900">
                        Ativo (disponível para seleção)
                    </label>
                </div>

                <div class="flex justify-between pt-4 border-t">
                    <a href="{{ route('admin.product-options.index', ['type' => $option->type]) }}"
                       class="px-4 py-2 text-gray-600 hover:text-gray-900">
                        ← Voltar
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
