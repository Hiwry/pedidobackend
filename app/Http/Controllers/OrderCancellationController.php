<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderCancellation;
use App\Models\OrderLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderCancellationController extends Controller
{
    public function request(Request $request, Order $order)
    {
        $request->validate([
            'reason' => 'required|string|max:1000'
        ]);

        // Verificar se já existe uma solicitação pendente
        if ($order->pendingCancellation) {
            return response()->json([
                'success' => false,
                'message' => 'Já existe uma solicitação de cancelamento pendente para este pedido.'
            ], 400);
        }

        // Verificar se o pedido pode ser cancelado
        if ($order->is_cancelled) {
            return response()->json([
                'success' => false,
                'message' => 'Este pedido já foi cancelado.'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Criar solicitação de cancelamento
            $cancellation = OrderCancellation::create([
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'reason' => $request->reason,
                'status' => 'pending'
            ]);

            // Atualizar pedido
            $order->update([
                'has_pending_cancellation' => true
            ]);

            // Criar log
            OrderLog::create([
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'action' => 'cancellation_requested',
                'description' => 'Solicitação de cancelamento enviada',
                'details' => json_encode([
                    'reason' => $request->reason
                ])
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Solicitação de cancelamento enviada com sucesso.',
                'cancellation' => $cancellation
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar solicitação de cancelamento.'
            ], 500);
        }
    }

    public function approve(Request $request, OrderCancellation $cancellation)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        if ($cancellation->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Esta solicitação já foi processada.'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Aprovar cancelamento
            $cancellation->update([
                'status' => 'approved',
                'admin_notes' => $request->admin_notes,
                'approved_by' => Auth::id(),
                'approved_at' => now()
            ]);

            // Cancelar pedido
            $cancellation->order->update([
                'is_cancelled' => true,
                'cancelled_at' => now(),
                'cancellation_reason' => $cancellation->reason,
                'has_pending_cancellation' => false,
                'last_updated_at' => now()
            ]);

            // Criar log
            OrderLog::create([
                'order_id' => $cancellation->order_id,
                'user_id' => Auth::id(),
                'action' => 'cancellation_approved',
                'description' => 'Cancelamento aprovado',
                'details' => json_encode([
                    'reason' => $cancellation->reason,
                    'admin_notes' => $request->admin_notes
                ])
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cancelamento aprovado com sucesso.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao aprovar cancelamento.'
            ], 500);
        }
    }

    public function reject(Request $request, OrderCancellation $cancellation)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:1000'
        ]);

        if ($cancellation->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Esta solicitação já foi processada.'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Rejeitar cancelamento
            $cancellation->update([
                'status' => 'rejected',
                'admin_notes' => $request->admin_notes,
                'approved_by' => Auth::id(),
                'approved_at' => now()
            ]);

            // Atualizar pedido
            $cancellation->order->update([
                'has_pending_cancellation' => false
            ]);

            // Criar log
            OrderLog::create([
                'order_id' => $cancellation->order_id,
                'user_id' => Auth::id(),
                'action' => 'cancellation_rejected',
                'description' => 'Cancelamento rejeitado',
                'details' => json_encode([
                    'admin_notes' => $request->admin_notes
                ])
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cancelamento rejeitado.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao rejeitar cancelamento.'
            ], 500);
        }
    }

    public function index()
    {
        $cancellations = OrderCancellation::with(['order.client', 'user', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.cancellations.index', compact('cancellations'));
    }
}
