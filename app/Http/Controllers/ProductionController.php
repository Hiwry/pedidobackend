<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Status;
use App\Models\PersonalizationPrice;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class ProductionController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $personalizationType = $request->get('personalization_type');
        $period = $request->get('period', 'day'); // day, week, month
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        // Definir datas baseadas no período selecionado
        if (!$startDate || !$endDate) {
            $now = Carbon::now();
            switch ($period) {
                case 'week':
                    $startDate = $now->startOfWeek()->format('Y-m-d');
                    $endDate = $now->endOfWeek()->format('Y-m-d');
                    break;
                case 'month':
                    $startDate = $now->startOfMonth()->format('Y-m-d');
                    $endDate = $now->endOfMonth()->format('Y-m-d');
                    break;
                case 'day':
                default:
                    $startDate = $now->format('Y-m-d');
                    $endDate = $now->format('Y-m-d');
                    break;
            }
        }

        $query = Order::with(['client', 'status', 'items'])
            ->where('is_draft', false)
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        // Busca por número do pedido, nome do cliente ou nome da arte
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('client', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('phone_primary', 'like', "%{$search}%");
                  })
                  ->orWhereHas('items', function($q3) use ($search) {
                      $q3->where('art_name', 'like', "%{$search}%");
                  });
            });
        }

        // Filtro por status
        if ($status) {
            $query->where('status_id', $status);
        }

        // Filtro por tipo de personalização
        if ($personalizationType) {
            $query->whereHas('items', function($q) use ($personalizationType) {
                $q->where('print_type', $personalizationType);
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);
        $statuses = Status::orderBy('position')->get();
        $personalizationTypes = PersonalizationPrice::getPersonalizationTypes();

        // Estatísticas
        $totalOrders = $orders->total();
        $totalValue = $orders->sum('total');
        $ordersByStatus = $orders->groupBy('status_id');
        $ordersByPersonalization = $orders->groupBy(function($order) {
            return $order->items->first()->print_type ?? 'N/A';
        });

        return view('production.index', compact(
            'orders', 
            'statuses', 
            'personalizationTypes',
            'search', 
            'status', 
            'personalizationType',
            'period',
            'startDate', 
            'endDate',
            'totalOrders',
            'totalValue',
            'ordersByStatus',
            'ordersByPersonalization'
        ));
    }

    public function kanban(Request $request): View
    {
        $search = $request->get('search');
        $personalizationType = $request->get('personalization_type');
        $period = $request->get('period', 'day');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        // Definir datas baseadas no período selecionado
        if (!$startDate || !$endDate) {
            $now = Carbon::now();
            switch ($period) {
                case 'week':
                    $startDate = $now->startOfWeek()->format('Y-m-d');
                    $endDate = $now->endOfWeek()->format('Y-m-d');
                    break;
                case 'month':
                    $startDate = $now->startOfMonth()->format('Y-m-d');
                    $endDate = $now->endOfMonth()->format('Y-m-d');
                    break;
                case 'day':
                default:
                    $startDate = $now->format('Y-m-d');
                    $endDate = $now->format('Y-m-d');
                    break;
            }
        }

        $statuses = Status::withCount('orders')->orderBy('position')->get();
        
        $query = Order::with(['client', 'items', 'items.files'])
            ->where('is_draft', false)
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        
        // Aplicar busca se fornecida
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('client', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('phone_primary', 'like', "%{$search}%");
                  })
                  ->orWhereHas('items', function($q3) use ($search) {
                      $q3->where('art_name', 'like', "%{$search}%");
                  });
            });
        }

        // Filtro por tipo de personalização
        if ($personalizationType) {
            $query->whereHas('items', function($q) use ($personalizationType) {
                $q->where('print_type', $personalizationType);
            });
        }
        
        $ordersByStatus = $query->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('status_id');

        $personalizationTypes = PersonalizationPrice::getPersonalizationTypes();

        return view('production.kanban', compact(
            'statuses', 
            'ordersByStatus', 
            'search', 
            'personalizationType',
            'period',
            'startDate',
            'endDate',
            'personalizationTypes'
        ));
    }
}
