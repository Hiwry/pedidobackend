<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Editar Cliente') }}: {{ $client->name }}
            </h2>
            <a href="{{ route('clients.show', $client->id) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('clients.update', $client->id) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Informações Básicas -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informações Básicas</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Nome -->
                                <div class="md:col-span-2">
                                    <label for="name" class="block text-sm font-medium text-gray-700">
                                        Nome <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        name="name" 
                                        id="name" 
                                        value="{{ old('name', $client->name) }}"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                                    >
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- CPF/CNPJ -->
                                <div>
                                    <label for="cpf_cnpj" class="block text-sm font-medium text-gray-700">
                                        CPF/CNPJ
                                    </label>
                                    <input 
                                        type="text" 
                                        name="cpf_cnpj" 
                                        id="cpf_cnpj" 
                                        value="{{ old('cpf_cnpj', $client->cpf_cnpj) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('cpf_cnpj') border-red-500 @enderror"
                                    >
                                    @error('cpf_cnpj')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Categoria -->
                                <div>
                                    <label for="category" class="block text-sm font-medium text-gray-700">
                                        Categoria
                                    </label>
                                    <input 
                                        type="text" 
                                        name="category" 
                                        id="category" 
                                        value="{{ old('category', $client->category) }}"
                                        placeholder="Ex: Atacado, Varejo, VIP"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('category') border-red-500 @enderror"
                                    >
                                    @error('category')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Contato -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Contato</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Telefone Principal -->
                                <div>
                                    <label for="phone_primary" class="block text-sm font-medium text-gray-700">
                                        Telefone Principal <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        name="phone_primary" 
                                        id="phone_primary" 
                                        value="{{ old('phone_primary', $client->phone_primary) }}"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('phone_primary') border-red-500 @enderror"
                                    >
                                    @error('phone_primary')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Telefone Secundário -->
                                <div>
                                    <label for="phone_secondary" class="block text-sm font-medium text-gray-700">
                                        Telefone Secundário
                                    </label>
                                    <input 
                                        type="text" 
                                        name="phone_secondary" 
                                        id="phone_secondary" 
                                        value="{{ old('phone_secondary', $client->phone_secondary) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('phone_secondary') border-red-500 @enderror"
                                    >
                                    @error('phone_secondary')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div class="md:col-span-2">
                                    <label for="email" class="block text-sm font-medium text-gray-700">
                                        Email
                                    </label>
                                    <input 
                                        type="email" 
                                        name="email" 
                                        id="email" 
                                        value="{{ old('email', $client->email) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('email') border-red-500 @enderror"
                                    >
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Endereço -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Endereço</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Endereço -->
                                <div class="md:col-span-2">
                                    <label for="address" class="block text-sm font-medium text-gray-700">
                                        Endereço
                                    </label>
                                    <input 
                                        type="text" 
                                        name="address" 
                                        id="address" 
                                        value="{{ old('address', $client->address) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('address') border-red-500 @enderror"
                                    >
                                    @error('address')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Cidade -->
                                <div>
                                    <label for="city" class="block text-sm font-medium text-gray-700">
                                        Cidade
                                    </label>
                                    <input 
                                        type="text" 
                                        name="city" 
                                        id="city" 
                                        value="{{ old('city', $client->city) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('city') border-red-500 @enderror"
                                    >
                                    @error('city')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Estado -->
                                <div>
                                    <label for="state" class="block text-sm font-medium text-gray-700">
                                        Estado
                                    </label>
                                    <input 
                                        type="text" 
                                        name="state" 
                                        id="state" 
                                        value="{{ old('state', $client->state) }}"
                                        maxlength="2"
                                        placeholder="UF"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('state') border-red-500 @enderror"
                                    >
                                    @error('state')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- CEP -->
                                <div>
                                    <label for="zip_code" class="block text-sm font-medium text-gray-700">
                                        CEP
                                    </label>
                                    <input 
                                        type="text" 
                                        name="zip_code" 
                                        id="zip_code" 
                                        value="{{ old('zip_code', $client->zip_code) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('zip_code') border-red-500 @enderror"
                                    >
                                    @error('zip_code')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="flex justify-between items-center pt-6 border-t">
                            <button 
                                type="button" 
                                onclick="if(confirm('Tem certeza que deseja excluir este cliente?')) { document.getElementById('delete-form').submit(); }"
                                class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded"
                            >
                                Excluir Cliente
                            </button>
                            <div class="flex space-x-3">
                                <a href="{{ route('clients.show', $client->id) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded">
                                    Cancelar
                                </a>
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                                    Salvar Alterações
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Formulário de exclusão -->
                    <form id="delete-form" action="{{ route('clients.destroy', $client->id) }}" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

