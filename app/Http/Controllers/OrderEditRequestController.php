<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderEditRequest;
use App\Models\OrderLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderEditRequestController extends Controller
{
    public function request(Request $request, Order $order)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
            'changes' => 'required|array'
        ]);

        // Verificar se já existe uma solicitação pendente
        if ($order->pendingEditRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Já existe uma solicitação de edição pendente para este pedido.'
            ], 400);
        }

        // Verificar se o pedido pode ser editado
        if ($order->is_cancelled) {
            return response()->json([
                'success' => false,
                'message' => 'Pedidos cancelados não podem ser editados.'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Criar solicitação de edição
            $editRequest = OrderEditRequest::create([
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'reason' => $request->reason,
                'changes' => $request->changes,
                'status' => 'pending'
            ]);

            // Atualizar pedido
            $order->update([
                'has_pending_edit' => true
            ]);

            // Criar log
            OrderLog::create([
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'action' => 'edit_requested',
                'description' => 'Solicitação de edição enviada',
                'details' => json_encode([
                    'reason' => $request->reason,
                    'changes' => $request->changes
                ])
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Solicitação de edição enviada com sucesso.',
                'edit_request' => $editRequest
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar solicitação de edição.'
            ], 500);
        }
    }

    public function approve(Request $request, OrderEditRequest $editRequest)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        if ($editRequest->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Esta solicitação já foi processada.'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Aprovar edição
            $editRequest->update([
                'status' => 'approved',
                'admin_notes' => $request->admin_notes,
                'approved_by' => Auth::id(),
                'approved_at' => now()
            ]);

            // Atualizar pedido
            $editRequest->order->update([
                'has_pending_edit' => false,
                'last_updated_at' => now()
            ]);

            // Criar log
            OrderLog::create([
                'order_id' => $editRequest->order_id,
                'user_id' => Auth::id(),
                'action' => 'edit_approved',
                'description' => 'Edição aprovada - Aguardando implementação',
                'details' => json_encode([
                    'reason' => $editRequest->reason,
                    'admin_notes' => $request->admin_notes,
                    'changes' => $editRequest->changes
                ])
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Edição aprovada. O usuário pode agora implementar as alterações.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao aprovar edição.'
            ], 500);
        }
    }

    public function reject(Request $request, OrderEditRequest $editRequest)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:1000'
        ]);

        if ($editRequest->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Esta solicitação já foi processada.'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Rejeitar edição
            $editRequest->update([
                'status' => 'rejected',
                'admin_notes' => $request->admin_notes,
                'approved_by' => Auth::id(),
                'approved_at' => now()
            ]);

            // Atualizar pedido
            $editRequest->order->update([
                'has_pending_edit' => false
            ]);

            // Criar log
            OrderLog::create([
                'order_id' => $editRequest->order_id,
                'user_id' => Auth::id(),
                'action' => 'edit_rejected',
                'description' => 'Edição rejeitada',
                'details' => json_encode([
                    'admin_notes' => $request->admin_notes
                ])
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Edição rejeitada.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao rejeitar edição.'
            ], 500);
        }
    }

    public function complete(Request $request, OrderEditRequest $editRequest)
    {
        if ($editRequest->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Esta edição não foi aprovada ainda.'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Marcar como concluída
            $editRequest->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);

            // Atualizar pedido
            $editRequest->order->update([
                'last_updated_at' => now()
            ]);

            // Criar log
            OrderLog::create([
                'order_id' => $editRequest->order_id,
                'user_id' => Auth::id(),
                'action' => 'edit_completed',
                'description' => 'Edição implementada com sucesso',
                'details' => json_encode([
                    'changes' => $editRequest->changes
                ])
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Edição implementada com sucesso.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao marcar edição como concluída.'
            ], 500);
        }
    }

    public function index()
    {
        $editRequests = OrderEditRequest::with(['order.client', 'user', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.edit-requests.index', compact('editRequests'));
    }

    public function showChanges(OrderEditRequest $editRequest)
    {
        return response()->json([
            'success' => true,
            'changes' => $editRequest->changes
        ]);
    }
}
