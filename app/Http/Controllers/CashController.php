<?php

namespace App\Http\Controllers;

use App\Models\CashTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CashController extends Controller
{
    public function index(Request $request)
    {
        // Se não houver filtro, mostrar TODAS as transações
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $type = $request->get('type', 'all');

        $query = CashTransaction::with(['order', 'user']);

        // Aplicar filtro de data apenas se fornecido
        if ($startDate && $endDate) {
            $query->whereBetween('transaction_date', [$startDate, $endDate]);
        }

        $query->orderBy('transaction_date', 'desc')
              ->orderBy('created_at', 'desc');

        if ($type !== 'all') {
            $query->where('type', $type);
        }

        $transactions = $query->get();
        
        // Se não houver filtro, definir datas padrão para exibição
        if (!$startDate) {
            $startDate = Carbon::now()->subDays(30)->format('Y-m-d');
        }
        if (!$endDate) {
            $endDate = Carbon::now()->format('Y-m-d');
        }

        // Calcular totais do período
        $totalEntradas = $transactions->where('type', 'entrada')->sum('amount');
        $totalSaidas = $transactions->where('type', 'saida')->sum('amount');
        $saldoPeriodo = $totalEntradas - $totalSaidas;
        
        // Calcular saldos gerais
        $saldoAtual = CashTransaction::getSaldoAtual(); // Apenas confirmadas
        $saldoGeral = CashTransaction::getSaldoGeral(); // Tudo
        $saldoPendente = CashTransaction::getSaldoPendente(); // Pendentes
        $totalSaidasGeral = CashTransaction::getTotalSaidas(); // Todas as saídas

        return view('cash.index', compact(
            'transactions',
            'totalEntradas',
            'totalSaidas',
            'saldoPeriodo',
            'saldoAtual',
            'saldoGeral',
            'saldoPendente',
            'totalSaidasGeral',
            'startDate',
            'endDate',
            'type'
        ));
    }

    public function create()
    {
        return view('cash.create');
    }

    public function store(Request $request)
    {
        try {
            // Log para debug
            \Log::info('Tentando criar transação', $request->all());
            
            $validated = $request->validate([
                'type' => 'required|in:entrada,saida',
                'category' => 'required|string|max:255',
                'description' => 'required|string',
                'amount' => 'required|numeric|min:0.01',
                'payment_method' => 'required|in:dinheiro,pix,cartao,transferencia,boleto',
                'transaction_date' => 'required|date',
                'order_id' => 'nullable|exists:orders,id',
                'notes' => 'nullable|string',
            ]);

            $user = Auth::user();
            
            if (!$user) {
                \Log::error('Usuário não autenticado');
                return redirect()->route('login')
                    ->with('error', 'Você precisa estar autenticado para registrar transações.');
            }

            $validated['user_id'] = $user->id;
            $validated['user_name'] = $user->name;

            \Log::info('Dados validados', $validated);

            $transaction = CashTransaction::create($validated);
            
            \Log::info('Transação criada com ID: ' . $transaction->id);

            return redirect()->route('cash.index')
                ->with('success', 'Transação registrada com sucesso! ID: ' . $transaction->id);
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Erro de validação', ['errors' => $e->errors()]);
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors())
                ->with('error', 'Erro de validação. Verifique os campos.');
        } catch (\Exception $e) {
            \Log::error('Erro ao criar transação: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erro ao registrar transação: ' . $e->getMessage());
        }
    }

    public function edit(CashTransaction $cash)
    {
        return view('cash.edit', compact('cash'));
    }

    public function update(Request $request, CashTransaction $cash)
    {
        $validated = $request->validate([
            'type' => 'required|in:entrada,saida',
            'category' => 'required|string|max:255',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:dinheiro,pix,cartao,transferencia,boleto',
            'transaction_date' => 'required|date',
            'order_id' => 'nullable|exists:orders,id',
            'notes' => 'nullable|string',
        ]);

        $cash->update($validated);

        return redirect()->route('cash.index')
            ->with('success', 'Transação atualizada com sucesso!');
    }

    public function destroy(CashTransaction $cash)
    {
        $cash->delete();

        return redirect()->route('cash.index')
            ->with('success', 'Transação excluída com sucesso!');
    }
}
