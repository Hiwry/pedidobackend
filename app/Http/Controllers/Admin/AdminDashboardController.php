<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\SublimationSize;
use App\Models\ProductOption;
use App\Models\Setting;
use App\Models\CashTransaction;
use App\Models\DeliveryRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        // Estatísticas gerais
        $stats = [
            'total_orders' => Order::where('is_draft', false)->count(),
            'total_users' => User::count(),
            'total_sizes' => SublimationSize::where('active', true)->count(),
            'total_product_options' => ProductOption::count(),
            'total_settings' => Setting::count(),
            'pending_delivery_requests' => DeliveryRequest::where('status', 'pending')->count(),
            'total_cash_transactions' => CashTransaction::count(),
        ];

        // Pedidos recentes
        $recent_orders = Order::with(['client', 'status'])
            ->where('is_draft', false)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Usuários recentes
        $recent_users = User::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Solicitações de entrega pendentes
        $pending_delivery_requests = DeliveryRequest::with(['order.client'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Transações de caixa recentes
        $recent_cash_transactions = CashTransaction::with(['user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recent_orders',
            'recent_users',
            'pending_delivery_requests',
            'recent_cash_transactions'
        ));
    }
}
