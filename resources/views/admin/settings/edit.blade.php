<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Configuração</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <x-app-header />

    <div class="max-w-3xl mx-auto p-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="mb-6">
                <h1 class="text-2xl font-semibold">Editar Configuração</h1>
                <p class="text-sm text-gray-600 mt-1">{{ $setting->key }}</p>
            </div>

            <form method="POST" action="{{ route('admin.settings.update',$setting->id) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="key" class="block text-sm font-medium text-gray-700">Chave</label>
                    <input type="text" id="key" name="key" disabled
                           class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm"
                           value="{{ $setting->key }}">
                    <p class="mt-1 text-xs text-gray-500">A chave não pode ser alterada</p>
                </div>

                <div>
                    <label for="value" class="block text-sm font-medium text-gray-700">Valor</label>
                    <input type="text" id="value" name="value"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           value="{{ old('value', $setting->value) }}">
                    @error('value')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700">Tipo</label>
                    <select id="type" name="type"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="string" {{ $setting->type === 'string' ? 'selected' : '' }}>String</option>
                        <option value="decimal" {{ $setting->type === 'decimal' ? 'selected' : '' }}>Decimal</option>
                        <option value="json" {{ $setting->type === 'json' ? 'selected' : '' }}>JSON</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-between pt-4 border-t">
                    <a href="{{ route('admin.settings.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-900">
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