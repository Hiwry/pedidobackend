<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderLog;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class ClientOrderController extends Controller
{
    public function show($token): View
    {
        $order = Order::with([
            'client',
            'status',
            'items.sublimations.size',
            'items.sublimations.location',
            'items.files',
            'payments'
        ])->where('client_token', $token)->firstOrFail();

        $payment = $order->payments()->first();

        return view('client.order-show', compact('order', 'payment'));
    }

    public function confirm(Request $request, $token): RedirectResponse
    {
        $order = Order::where('client_token', $token)->firstOrFail();
        
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:20',
            'confirmation_notes' => 'nullable|string|max:500',
        ]);

        // Atualizar dados do cliente se necessário
        $order->client->update([
            'name' => $validated['client_name'],
            'phone_primary' => $validated['client_phone'],
        ]);

        // Marcar pedido como confirmado pelo cliente
        $order->update([
            'client_confirmed' => true,
            'client_confirmed_at' => now(),
            'client_confirmation_notes' => $validated['confirmation_notes'],
            'is_draft' => false, // Se o cliente confirmou, o pedido sai do rascunho
        ]);

        // Registrar log
        OrderLog::create([
            'order_id' => $order->id,
            'user_id' => null, // Cliente não tem user_id
            'user_name' => $validated['client_name'],
            'action' => 'CLIENT_CONFIRMED',
            'description' => 'Cliente confirmou o pedido via link de compartilhamento',
        ]);

        return redirect()->back()->with('success', 'Pedido confirmado com sucesso! Obrigado pela confirmação.');
    }

    public function generateToken($id): RedirectResponse
    {
        $order = Order::findOrFail($id);
        
        // Gerar token único se não existir
        if (!$order->client_token) {
            $order->update([
                'client_token' => \Str::random(32)
            ]);
        }

        $shareUrl = route('client.order.show', $order->client_token);
        
        return redirect()->back()->with('success', 'Link gerado com sucesso!')->with('share_url', $shareUrl);
    }
}
