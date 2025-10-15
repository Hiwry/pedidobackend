<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class StatusController extends Controller
{
    public function index(): View
    {
        $statuses = Status::withCount('orders')->orderBy('position')->get();
        return view('kanban.columns.index', compact('statuses'));
    }

    public function create(): View
    {
        return view('kanban.columns.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:statuses,name',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        // Definir posição como a última
        $maxPosition = Status::max('position') ?? 0;
        $validated['position'] = $maxPosition + 1;

        Status::create($validated);

        return redirect()->route('kanban.columns.index')
            ->with('success', 'Coluna criada com sucesso!');
    }

    public function edit(Status $status): View
    {
        return view('kanban.columns.edit', compact('status'));
    }

    public function update(Request $request, Status $status): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:statuses,name,' . $status->id,
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $status->update($validated);

        return redirect()->route('kanban.columns.index')
            ->with('success', 'Coluna atualizada com sucesso!');
    }

    public function destroy(Status $status): RedirectResponse
    {
        // Verificar se há pedidos nesta coluna
        $ordersCount = $status->orders()->count();
        
        if ($ordersCount > 0) {
            return redirect()->route('kanban.columns.index')
                ->with('error', "Não é possível excluir a coluna '{$status->name}' pois existem {$ordersCount} pedido(s) nela. Mova os pedidos para outra coluna primeiro.");
        }

        // Reordenar posições das colunas restantes
        Status::where('position', '>', $status->position)
            ->decrement('position');

        $status->delete();

        return redirect()->route('kanban.columns.index')
            ->with('success', 'Coluna excluída com sucesso!');
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'statuses' => 'required|array',
            'statuses.*' => 'required|integer|exists:statuses,id',
        ]);

        // Atualizar posições
        foreach ($validated['statuses'] as $index => $statusId) {
            Status::where('id', $statusId)->update(['position' => $index + 1]);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Ordem das colunas atualizada com sucesso!']);
        }

        return redirect()->route('kanban.columns.index')
            ->with('success', 'Ordem das colunas atualizada com sucesso!');
    }

    public function moveOrders(Request $request, Status $status): RedirectResponse
    {
        $validated = $request->validate([
            'target_status_id' => 'required|integer|exists:statuses,id',
        ]);

        $targetStatus = Status::findOrFail($validated['target_status_id']);
        
        // Mover todos os pedidos desta coluna para a coluna de destino
        $movedCount = $status->orders()->update(['status_id' => $targetStatus->id]);

        return redirect()->route('kanban.columns.index')
            ->with('success', "{$movedCount} pedido(s) movido(s) para '{$targetStatus->name}' com sucesso!");
    }
}
