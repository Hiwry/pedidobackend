<?php

namespace App\Http\Controllers;

use App\Models\StockRequest;
use App\Models\Stock;
use App\Models\Store;
use App\Models\User;
use App\Models\Notification;
use App\Models\ProductOption;
use App\Models\Order;
use App\Helpers\StoreHelper;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockRequestController extends Controller
{
    /**
     * Listar solicitações de estoque
     */
    public function index(Request $request): View
    {
        $status = $request->get('status');
        $storeId = $request->get('store_id');

        $query = StockRequest::with([
            'order',
            'requestingStore',
            'targetStore',
            'fabric',
            'color',
            'cutType',
            'requestedBy',
            'approvedBy' // Relacionamento com usuário que aprovou
        ]);

        // Aplicar filtro de loja
        $user = Auth::user();
        if ($user->isAdminLoja()) {
            $storeIds = $user->getStoreIds();
            if (!empty($storeIds)) {
                $query->where(function($q) use ($storeIds) {
                    $q->whereIn('requesting_store_id', $storeIds)
                      ->orWhereIn('target_store_id', $storeIds);
                });
            }
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($storeId) {
            $query->where(function($q) use ($storeId) {
                $q->where('requesting_store_id', $storeId)
                  ->orWhere('target_store_id', $storeId);
            });
        }

        // Ordenar: primeiro por order_id (pedidos primeiro), depois por data de criação (mais recentes primeiro)
        $allRequests = $query->orderByRaw('CASE WHEN order_id IS NULL THEN 1 ELSE 0 END')
            ->orderBy('order_id', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Agrupar solicitações por pedido e especificações (tecido, cor, corte)
        $groupedRequests = [];
        foreach ($allRequests as $stockRequest) {
            // Criar chave única: order_id + fabric_id + color_id + cut_type_id + status
            $groupKey = sprintf(
                '%s_%s_%s_%s_%s',
                $stockRequest->order_id ?? 'sem_pedido_' . $stockRequest->id,
                $stockRequest->fabric_id ?? '0',
                $stockRequest->color_id ?? '0',
                $stockRequest->cut_type_id ?? '0',
                $stockRequest->status
            );
            
            if (!isset($groupedRequests[$groupKey])) {
                $order = $stockRequest->order;
                // Verificar se é PDV: só é PDV se o pedido existir E tiver is_pdv = true
                $isPdv = false;
                if ($order && isset($order->is_pdv)) {
                    $isPdv = (bool) $order->is_pdv;
                }
                
                $groupedRequests[$groupKey] = [
                    'order_id' => $stockRequest->order_id,
                    'order' => $order,
                    'is_pdv' => $isPdv, // Identificar se é venda PDV ou pedido
                    'fabric' => $stockRequest->fabric,
                    'color' => $stockRequest->color,
                    'cut_type' => $stockRequest->cutType,
                    'requesting_store' => $stockRequest->requestingStore,
                    'target_store' => $stockRequest->targetStore,
                    'status' => $stockRequest->status,
                    'created_at' => $stockRequest->created_at,
                    'approved_by' => $stockRequest->approvedBy, // Usuário que aprovou
                    'approved_at' => $stockRequest->approved_at, // Data/hora da aprovação
                    'request_notes' => $stockRequest->request_notes, // Observações da solicitação
                    'requests' => [],
                    'sizes_summary' => [], // Para armazenar resumo: ['P' => 5, 'M' => 2, 'G' => 4]
                ];
            }
            
            $groupedRequests[$groupKey]['requests'][] = $stockRequest;
            
            // Agrupar tamanhos e quantidades
            $size = $stockRequest->size;
            if (!isset($groupedRequests[$groupKey]['sizes_summary'][$size])) {
                $groupedRequests[$groupKey]['sizes_summary'][$size] = 0;
            }
            $groupedRequests[$groupKey]['sizes_summary'][$size] += $stockRequest->requested_quantity;
        }
        
        // Converter para array e paginar
        $groupedArray = array_values($groupedRequests);
        $perPage = 20;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $paginatedGroups = array_slice($groupedArray, $offset, $perPage);
        
        // Criar paginator manual
        $requests = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedGroups,
            count($groupedArray),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $stores = StoreHelper::getAvailableStores();
        $statuses = ['pendente', 'aprovado', 'rejeitado', 'em_transferencia', 'concluido', 'cancelado'];
        
        // Dados para os modais de criação de solicitações
        $fabrics = ProductOption::where('type', 'tecido')->where('active', true)->orderBy('name')->get();
        $colors = ProductOption::where('type', 'cor')->where('active', true)->orderBy('name')->get();
        $cutTypes = ProductOption::where('type', 'tipo_corte')->where('active', true)->orderBy('name')->get();
        $sizes = ['PP', 'P', 'M', 'G', 'GG', 'EXG', 'G1', 'G2', 'G3'];
        
        // Pedidos recentes para solicitações vinculadas a pedidos (excluir vendas PDV)
        $recentOrders = Order::where(function($query) {
                $query->where('is_pdv', false)
                      ->orWhereNull('is_pdv');
            })
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get(['id', 'order_number', 'client_id', 'created_at']);

        return view('stock-requests.index', compact('requests', 'stores', 'statuses', 'status', 'storeId', 'fabrics', 'colors', 'cutTypes', 'sizes', 'recentOrders'));
    }

    /**
     * Criar solicitação de estoque
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => 'nullable|exists:orders,id',
            'requesting_store_id' => 'required|exists:stores,id',
            'target_store_id' => 'nullable|exists:stores,id',
            'fabric_id' => 'nullable|exists:product_options,id',
            'color_id' => 'nullable|exists:product_options,id',
            'cut_type_id' => 'nullable|exists:product_options,id',
            'size' => 'required|string|in:PP,P,M,G,GG,EXG,G1,G2,G3',
            'requested_quantity' => 'required|integer|min:1',
            'request_notes' => 'nullable|string|max:1000',
        ]);

        // Verificar permissão
        if (!StoreHelper::canAccessStore($validated['requesting_store_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para acessar esta loja.',
            ], 403);
        }

        // Se não especificou loja de destino, buscar loja com estoque disponível
        if (!$validated['target_store_id']) {
            $availableStore = $this->findStoreWithStock(
                $validated['fabric_id'],
                $validated['color_id'],
                $validated['cut_type_id'],
                $validated['size'],
                $validated['requested_quantity']
            );

            if ($availableStore) {
                $validated['target_store_id'] = $availableStore->id;
            }
        }

        $stockRequest = StockRequest::create([
            ...$validated,
            'requested_by' => Auth::id(),
            'status' => 'pendente',
        ]);

        // Carregar relacionamentos para notificação
        $stockRequest->load(['requestingStore', 'targetStore', 'fabric', 'color', 'cutType']);
        
        // Criar notificação para usuários de estoque
        $estoqueUsers = User::where('role', 'estoque')->orWhere('role', 'admin')->get();
        $productInfo = ($stockRequest->fabric ? $stockRequest->fabric->name : 'N/A') . 
                      ' - ' . ($stockRequest->color ? $stockRequest->color->name : 'N/A') . 
                      ' (' . ($stockRequest->cutType ? $stockRequest->cutType->name : 'N/A') . 
                      ' - ' . $stockRequest->size . ')';
        
        foreach ($estoqueUsers as $estoqueUser) {
            Notification::createStockRequestCreated(
                $estoqueUser->id,
                $stockRequest->id,
                $stockRequest->requestingStore->name ?? 'N/A',
                $productInfo
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Solicitação de estoque criada com sucesso!',
            'request' => $stockRequest,
        ]);
    }

    /**
     * Aprovar solicitação
     */
    public function approve(Request $request, $id): JsonResponse
    {
        try {
            $stockRequest = StockRequest::with('order')->findOrFail($id);

            // Verificar permissão (admin, estoque ou admin da loja)
            $user = Auth::user();
            if (!$user->isAdmin() && !$user->isEstoque() && !StoreHelper::canAccessStore($stockRequest->requesting_store_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não tem permissão para aprovar esta solicitação.',
                ], 403);
            }

        $validated = $request->validate([
            'approved_quantity' => 'required|integer|min:1|max:' . ($stockRequest->requested_quantity ?? 999999),
            'approval_notes' => 'nullable|string|max:1000',
        ]);

            // Verificar se há estoque disponível na loja de destino (se especificada)
            if ($stockRequest->target_store_id) {
                $stock = Stock::findByParams(
                    $stockRequest->target_store_id,
                    $stockRequest->fabric_id,
                    $stockRequest->color_id,
                    $stockRequest->cut_type_id,
                    $stockRequest->size
                );

                if (!$stock || !$stock->hasStock($validated['approved_quantity'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Estoque insuficiente na loja de destino. Disponível: ' . ($stock ? $stock->available_quantity : 0),
                    ], 400);
                }
            }

            $result = $stockRequest->approve($validated['approved_quantity'], Auth::id(), $validated['approval_notes'] ?? null);
            
            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não foi possível aprovar a solicitação. Verifique se o status está correto.',
                ], 400);
            }

            // Se for venda PDV, descontar estoque automaticamente após aprovação
            if ($stockRequest->order && $stockRequest->order->is_pdv && $stockRequest->target_store_id) {
                $stock = Stock::findByParams(
                    $stockRequest->target_store_id,
                    $stockRequest->fabric_id,
                    $stockRequest->color_id,
                    $stockRequest->cut_type_id,
                    $stockRequest->size
                );
                
                if ($stock && $stock->hasStock($validated['approved_quantity'])) {
                    $stock->use($validated['approved_quantity']);
                }
            }

            // Criar notificação para o usuário que solicitou
            if ($stockRequest->requested_by) {
                $stockRequest->load(['requestingStore', 'fabric', 'color', 'cutType']);
                $productInfo = ($stockRequest->fabric ? $stockRequest->fabric->name : 'N/A') . 
                              ' - ' . ($stockRequest->color ? $stockRequest->color->name : 'N/A') . 
                              ' (' . ($stockRequest->cutType ? $stockRequest->cutType->name : 'N/A') . 
                              ' - ' . $stockRequest->size . ')';
                
                Notification::createStockRequestApproved(
                    $stockRequest->requested_by,
                    $stockRequest->id,
                    Auth::user()->name,
                    $productInfo,
                    $validated['approved_quantity']
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Solicitação aprovada com sucesso!',
                'request' => $stockRequest->fresh()->load(['order', 'requestingStore', 'targetStore', 'fabric', 'color', 'cutType']),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação: ' . $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Erro ao aprovar solicitação de estoque', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_id' => $id,
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao aprovar solicitação: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Rejeitar solicitação
     */
    public function reject(Request $request, $id): JsonResponse
    {
        try {
            $stockRequest = StockRequest::with('order')->findOrFail($id);

            // Verificar permissão (admin, estoque ou admin da loja)
            $user = Auth::user();
            if (!$user->isAdmin() && !$user->isEstoque() && !StoreHelper::canAccessStore($stockRequest->requesting_store_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não tem permissão para rejeitar esta solicitação.',
                ], 403);
            }

            $validated = $request->validate([
                'rejection_reason' => 'required|string|max:1000',
            ]);

            $result = $stockRequest->reject(Auth::id(), $validated['rejection_reason']);
            
            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não foi possível rejeitar a solicitação. Verifique se o status está correto.',
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Solicitação rejeitada.',
                'request' => $stockRequest->fresh()->load(['order', 'requestingStore', 'targetStore', 'fabric', 'color', 'cutType']),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação: ' . $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Erro ao rejeitar solicitação de estoque', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_id' => $id,
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao rejeitar solicitação: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Concluir transferência
     */
    public function complete(Request $request, $id): JsonResponse
    {
        $stockRequest = StockRequest::findOrFail($id);

        // Verificar permissão (admin, estoque ou admin da loja)
        $user = Auth::user();
        if (!$user->isAdminGeral() && !$user->isEstoque() && !StoreHelper::canAccessStore($stockRequest->target_store_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para concluir esta transferência.',
            ], 403);
        }

        $validated = $request->validate([
            'transferred_quantity' => 'required|integer|min:1|max:' . $stockRequest->approved_quantity,
        ]);

        $stockRequest->complete($validated['transferred_quantity']);

        return response()->json([
            'success' => true,
            'message' => 'Transferência concluída com sucesso!',
            'request' => $stockRequest->fresh()->load(['requestingStore', 'targetStore', 'fabric', 'color', 'cutType']),
        ]);
    }

    /**
     * Buscar loja com estoque disponível
     */
    private function findStoreWithStock(?int $fabricId, ?int $colorId, ?int $cutTypeId, string $size, int $quantity): ?Store
    {
        $stores = StoreHelper::getAvailableStores();

        foreach ($stores as $store) {
            $stock = Stock::findByParams($store->id, $fabricId, $colorId, $cutTypeId, $size);
            
            if ($stock && $stock->hasStock($quantity)) {
                return $store;
            }
        }

        return null;
    }
}
