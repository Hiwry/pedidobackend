<nav class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 py-3">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-8">
                <h1 class="text-xl font-bold text-gray-800">Sistema de Pedidos</h1>
                <div class="hidden md:flex gap-6">
                    <a href="/" class="text-gray-600 hover:text-gray-900 {{ request()->is('/') ? 'text-indigo-600 font-semibold' : '' }}">
                        Home
                    </a>
                    <a href="{{ route('orders.index') }}" class="text-gray-600 hover:text-gray-900 {{ request()->is('pedidos*') ? 'text-indigo-600 font-semibold' : '' }}">
                        Pedidos
                    </a>
                    <a href="{{ route('kanban.index') }}" class="text-gray-600 hover:text-gray-900 {{ request()->is('kanban') ? 'text-indigo-600 font-semibold' : '' }}">
                        Kanban
                    </a>
                    <a href="{{ route('production.index') }}" class="text-gray-600 hover:text-gray-900 {{ request()->is('producao*') ? 'text-indigo-600 font-semibold' : '' }}">
                        Produção
                    </a>
                    @auth
                        <a href="{{ route('cash.index') }}" class="text-gray-600 hover:text-gray-900 {{ request()->is('cash*') ? 'text-indigo-600 font-semibold' : '' }}">
                            Caixa
                        </a>
                        @if(Auth::user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-gray-900 {{ request()->is('admin*') ? 'text-indigo-600 font-semibold' : '' }}">
                            Painel Administrativo
                        </a>
                        @endif
                    @endauth
                </div>
            </div>
            <div class="flex items-center">
                @auth
                    <span class="text-sm text-gray-600 mr-4">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">
                            Sair
                        </button>
                    </form>
                @endauth
            </div>
        </div>
    </div>
</nav>
