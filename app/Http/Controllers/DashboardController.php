<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Client;
use App\Models\Status;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Estatísticas gerais (apenas pedidos confirmados)
        $totalPedidos = Order::where('is_draft', false)->count();
        $totalClientes = Client::count();
        $totalFaturamento = Order::where('is_draft', false)->sum('total');
        $pedidosHoje = Order::where('is_draft', false)->whereDate('created_at', Carbon::today())->count();
        
        // Pedidos por status (apenas pedidos confirmados)
        $pedidosPorStatus = Order::select('status_id', DB::raw('count(*) as total'))
            ->where('is_draft', false)
            ->groupBy('status_id')
            ->with('status')
            ->get()
            ->map(function($item) {
                return [
                    'status' => $item->status->name ?? 'Sem Status',
                    'color' => $item->status->color ?? '#gray',
                    'total' => $item->total
                ];
            });

        // Faturamento mensal (últimos 6 meses) - apenas pedidos confirmados
        $faturamentoMensal = Order::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as mes'),
                DB::raw('SUM(total) as total'),
                DB::raw('COUNT(*) as quantidade')
            )
            ->where('is_draft', false)
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('mes')
            ->orderBy('mes', 'asc')
            ->get();

        // Pedidos recentes (apenas pedidos confirmados)
        $pedidosRecentes = Order::with(['client', 'status', 'items'])
            ->where('is_draft', false)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Top 5 clientes (apenas pedidos confirmados)
        $topClientes = Client::select(
                'clients.id',
                'clients.name',
                'clients.phone_primary',
                'clients.phone_secondary',
                'clients.email',
                'clients.cpf_cnpj',
                'clients.address',
                'clients.city',
                'clients.state',
                'clients.zip_code',
                'clients.category',
                'clients.created_at',
                'clients.updated_at',
                DB::raw('COUNT(orders.id) as total_pedidos'),
                DB::raw('SUM(orders.total) as total_gasto')
            )
            ->join('orders', 'clients.id', '=', 'orders.client_id')
            ->where('orders.is_draft', false)
            ->groupBy(
                'clients.id',
                'clients.name',
                'clients.phone_primary',
                'clients.phone_secondary',
                'clients.email',
                'clients.cpf_cnpj',
                'clients.address',
                'clients.city',
                'clients.state',
                'clients.zip_code',
                'clients.category',
                'clients.created_at',
                'clients.updated_at'
            )
            ->orderBy('total_gasto', 'desc')
            ->limit(5)
            ->get();

        // Pagamentos pendentes (apenas pedidos confirmados)
        $pagamentosPendentes = Payment::where('remaining_amount', '>', 0)
            ->whereHas('order', function($query) {
                $query->where('is_draft', false);
            })
            ->with('order.client')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Total de valores pendentes (apenas pedidos confirmados)
        $totalPendente = Payment::where('remaining_amount', '>', 0)
            ->whereHas('order', function($query) {
                $query->where('is_draft', false);
            })
            ->sum('remaining_amount');

        // Pedidos por mês (últimos 12 meses) - apenas pedidos confirmados
        $pedidosPorMes = Order::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as mes'),
                DB::raw('COUNT(*) as total')
            )
            ->where('is_draft', false)
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('mes')
            ->orderBy('mes', 'asc')
            ->get();

        return view('dashboard', compact(
            'totalPedidos',
            'totalClientes',
            'totalFaturamento',
            'pedidosHoje',
            'pedidosPorStatus',
            'faturamentoMensal',
            'pedidosRecentes',
            'topClientes',
            'pagamentosPendentes',
            'totalPendente',
            'pedidosPorMes'
        ));
    }
}
