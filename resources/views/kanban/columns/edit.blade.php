<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Editar Coluna - Sistema de Pedidos</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <x-app-header />

    <div class="max-w-2xl mx-auto p-6">
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h1 class="text-xl font-semibold">Editar Coluna: {{ $status->name }}</h1>
                    <a href="{{ route('kanban.columns.index') }}" 
                       class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <form method="POST" action="{{ route('kanban.columns.update', $status) }}" class="px-6 py-6">
                @csrf
                @method('PUT')

                <!-- Nome da Coluna -->
                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nome da Coluna *
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $status->name) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('name') border-red-500 @enderror"
                           placeholder="Ex: Fila de Corte, Em Produção, Pronto..."
                           required>
                    @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Cor da Coluna -->
                <div class="mb-6">
                    <label for="color" class="block text-sm font-medium text-gray-700 mb-2">
                        Cor da Coluna *
                    </label>
                    <div class="flex items-center space-x-4">
                        <input type="color" 
                               id="color" 
                               name="color" 
                               value="{{ old('color', $status->color) }}"
                               class="w-16 h-10 border border-gray-300 rounded-md cursor-pointer @error('color') border-red-500 @enderror"
                               required>
                        <input type="text" 
                               id="colorText" 
                               value="{{ old('color', $status->color) }}"
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                               placeholder="#6b7280"
                               pattern="^#[0-9A-Fa-f]{6}$"
                               required>
                    </div>
                    @error('color')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Escolha uma cor que represente bem esta etapa do processo</p>
                </div>

                <!-- Preview da Coluna -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Preview da Coluna
                    </label>
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="px-4 py-3 font-semibold flex justify-between items-center" 
                             id="previewHeader" 
                             style="background: {{ old('color', $status->color) }}; color: #fff">
                            <span id="previewName">{{ old('name', $status->name) }}</span>
                            <span class="bg-white bg-opacity-30 px-2 py-1 rounded-full text-xs">{{ $status->orders_count ?? 0 }}</span>
                        </div>
                        <div class="p-3 bg-gray-50 min-h-[100px] flex items-center justify-center text-gray-500">
                            <span>{{ $status->orders_count ?? 0 }} pedido(s) nesta coluna</span>
                        </div>
                    </div>
                </div>

                <!-- Cores Sugeridas -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Cores Sugeridas
                    </label>
                    <div class="grid grid-cols-6 gap-2">
                        @php
                        $suggestedColors = [
                            '#7c3aed', '#0ea5e9', '#22c55e', '#f59e0b', '#10b981', '#ef4444',
                            '#8b5cf6', '#06b6d4', '#84cc16', '#f97316', '#14b8a6', '#f43f5e',
                            '#6366f1', '#0891b2', '#65a30d', '#ea580c', '#0d9488', '#dc2626'
                        ];
                        @endphp
                        @foreach($suggestedColors as $color)
                        <button type="button" 
                                onclick="setColor('{{ $color }}')"
                                class="w-10 h-10 rounded-md border-2 border-gray-300 hover:border-gray-400 transition-colors
                                       {{ $color === $status->color ? 'border-indigo-500 ring-2 ring-indigo-200' : '' }}"
                                style="background-color: {{ $color }}"
                                title="{{ $color }}">
                        </button>
                        @endforeach
                    </div>
                </div>

                <!-- Informações da Coluna -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Informações da Coluna</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">Posição:</span>
                            <span class="font-medium">{{ $status->position }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Pedidos:</span>
                            <span class="font-medium">{{ $status->orders_count ?? 0 }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Criada em:</span>
                            <span class="font-medium">{{ $status->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Atualizada em:</span>
                            <span class="font-medium">{{ $status->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Botões -->
                <div class="flex justify-between">
                    <div>
                        <form method="POST" action="{{ route('kanban.columns.destroy', $status) }}" 
                              class="inline" 
                              onsubmit="return confirm('Tem certeza que deseja excluir esta coluna?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="px-4 py-2 text-red-700 bg-red-100 rounded-md hover:bg-red-200
                                           {{ $status->orders_count > 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                    {{ $status->orders_count > 0 ? 'disabled' : '' }}>
                                Excluir Coluna
                            </button>
                        </form>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('kanban.columns.index') }}" 
                           class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Salvar Alterações
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Sincronizar inputs de cor
        const colorInput = document.getElementById('color');
        const colorText = document.getElementById('colorText');
        const previewHeader = document.getElementById('previewHeader');
        const previewName = document.getElementById('previewName');

        colorInput.addEventListener('input', function() {
            colorText.value = this.value;
            updatePreview();
        });

        colorText.addEventListener('input', function() {
            if (this.value.match(/^#[0-9A-Fa-f]{6}$/)) {
                colorInput.value = this.value;
                updatePreview();
            }
        });

        // Atualizar preview
        function updatePreview() {
            const color = colorInput.value;
            previewHeader.style.background = color;
        }

        // Definir cor sugerida
        function setColor(color) {
            colorInput.value = color;
            colorText.value = color;
            updatePreview();
        }

        // Atualizar nome no preview
        document.getElementById('name').addEventListener('input', function() {
            previewName.textContent = this.value || 'Nome da Coluna';
        });

        // Atualizar preview inicial
        updatePreview();
    </script>
</body>
</html>
