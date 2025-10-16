@props(['order'])

<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Histórico de Edições</h3>
                <p class="text-sm text-gray-600">Registro de todas as alterações realizadas</p>
            </div>
        </div>
    </div>

    <div class="p-6">
        @if($order->editHistory->count() > 0)
            <div class="space-y-4">
                @foreach($order->editHistory->sortByDesc('created_at') as $history)
                <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center
                            @if($history->action === 'finalize') bg-green-100 text-green-600
                            @elseif($history->action === 'client_changes') bg-blue-100 text-blue-600
                            @elseif($history->action === 'item_changes') bg-purple-100 text-purple-600
                            @elseif($history->action === 'payment_changes') bg-yellow-100 text-yellow-600
                            @else bg-gray-100 text-gray-600
                            @endif">
                            @if($history->action === 'finalize')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            @elseif($history->action === 'client_changes')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            @elseif($history->action === 'item_changes')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            @elseif($history->action === 'payment_changes')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-medium text-gray-900">{{ $history->description }}</h4>
                            <time class="text-xs text-gray-500" datetime="{{ $history->created_at }}">
                                {{ $history->created_at->format('d/m/Y H:i') }}
                            </time>
                        </div>
                        
                        <p class="text-xs text-gray-600 mt-1">
                            Por: <span class="font-medium">{{ $history->user_name }}</span>
                        </p>
                        
                        @if($history->changes && count($history->changes) > 0)
                        <div class="mt-3">
                            <details class="group">
                                <summary class="text-xs text-indigo-600 hover:text-indigo-800 cursor-pointer font-medium">
                                    Ver alterações detalhadas
                                    <svg class="w-3 h-3 inline ml-1 transform group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </summary>
                                
                                <div class="mt-2 p-3 bg-white rounded border border-gray-200 text-xs">
                                    @foreach($history->changes as $field => $change)
                                    <div class="mb-2 last:mb-0">
                                        <div class="font-medium text-gray-700 capitalize">{{ str_replace('_', ' ', $field) }}:</div>
                                        <div class="ml-2">
                                            @if(is_array($change) && isset($change['old']) && isset($change['new']))
                                                <div class="text-red-600">
                                                    <span class="font-medium">Antes:</span> 
                                                    {{ is_array($change['old']) ? json_encode($change['old']) : $change['old'] }}
                                                </div>
                                                <div class="text-green-600">
                                                    <span class="font-medium">Depois:</span> 
                                                    {{ is_array($change['new']) ? json_encode($change['new']) : $change['new'] }}
                                                </div>
                                            @else
                                                <div class="text-gray-600">
                                                    {{ is_array($change) ? json_encode($change) : $change }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </details>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <p class="text-sm text-gray-500 mb-2">Nenhuma edição registrada</p>
                <p class="text-xs text-gray-400">O histórico de edições aparecerá aqui</p>
            </div>
        @endif
    </div>
</div>
