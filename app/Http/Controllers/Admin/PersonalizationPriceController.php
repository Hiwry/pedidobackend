<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PersonalizationPrice;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PersonalizationPriceController extends Controller
{
    public function index(): View
    {
        $types = PersonalizationPrice::getPersonalizationTypes();
        $pricesByType = [];
        
        foreach ($types as $key => $label) {
            $pricesByType[$key] = [
                'label' => $label,
                'sizes' => PersonalizationPrice::getSizesForType($key),
                'total_ranges' => PersonalizationPrice::where('personalization_type', $key)
                    ->where('active', true)
                    ->count()
            ];
        }

        return view('admin.personalization-prices.index', compact('types', 'pricesByType'));
    }

    public function edit($type): View
    {
        $types = PersonalizationPrice::getPersonalizationTypes();
        
        if (!array_key_exists($type, $types)) {
            abort(404, 'Tipo de personalização não encontrado');
        }

        // Se for SERIGRAFIA, usar view especializada
        if ($type === 'SERIGRAFIA') {
            $sizes = PersonalizationPrice::getSizesForType($type);
            $prices = PersonalizationPrice::where('personalization_type', $type)
                ->where('active', true)
                ->orderBy('size_name')
                ->orderBy('quantity_from')
                ->get()
                ->groupBy('size_name');
            
            $colors = \App\Models\SerigraphyColor::orderBy('order')->get();

            // Debug: Log dos dados carregados
            \Log::info('=== SERIGRAFIA EDIT DEBUG ===');
            \Log::info('Prices loaded:', $prices->toArray());
            \Log::info('Colors loaded:', $colors->toArray());
            \Log::info('Prices count: ' . $prices->count());
            \Log::info('Colors count: ' . $colors->count());

            return view('admin.personalization-prices.edit-serigraphy', compact('type', 'types', 'sizes', 'prices', 'colors'));
        }

        $sizes = PersonalizationPrice::getSizesForType($type);
        $prices = PersonalizationPrice::where('personalization_type', $type)
            ->where('active', true)
            ->orderBy('size_name')
            ->orderBy('quantity_from')
            ->get()
            ->groupBy('size_name');

        return view('admin.personalization-prices.edit', compact('type', 'types', 'sizes', 'prices'));
    }

    public function update(Request $request, $type): RedirectResponse
    {
        // Se for SERIGRAFIA, usar validação diferente (formato de tabela)
        if ($type === 'SERIGRAFIA') {
            $request->validate([
                'prices' => 'required|array',
                'colors' => 'nullable|array',
            ]);

            // Log para debug (remover depois)
            \Log::info('=== SERIGRAFIA UPDATE DEBUG ===');
            \Log::info('Request all data:', $request->all());
            \Log::info('Prices data:', $request->prices);
            \Log::info('Colors data:', $request->colors ?? []);
            \Log::info('Prices count: ' . count($request->prices));
            \Log::info('Colors count: ' . count($request->colors ?? []));

            // Deletar preços existentes
            PersonalizationPrice::where('personalization_type', $type)->delete();

            // Processar tabela de preços
            $sizes = ['ESCUDO', 'A4', 'A3'];
            
            foreach ($request->prices as $rowIndex => $row) {
                // Pegar from e to da linha (são comuns para todos os tamanhos)
                $qtyFrom = $row['from'] ?? null;
                $qtyTo = $row['to'] ?? null;
                
                if (!$qtyFrom) {
                    continue; // Pular linhas sem quantidade mínima
                }
                
                foreach ($sizes as $size) {
                    if (isset($row[$size]) && !empty($row[$size])) {
                        $price = $row[$size];
                        
                        if ($price > 0) {
                            PersonalizationPrice::create([
                                'personalization_type' => $type,
                                'size_name' => $size,
                                'size_dimensions' => null,
                                'quantity_from' => $qtyFrom,
                                'quantity_to' => $qtyTo,
                                'price' => $price,
                            ]);
                        }
                    }
                }
            }

            // Processar preços de cores
            // Limpar todas as cores antigas
            \App\Models\SerigraphyColor::truncate();
            
            // Criar cor base (1 cor incluída - preço 0)
            \App\Models\SerigraphyColor::create([
                'name' => '1 Cor',
                'price' => 0,
                'is_neon' => false,
                'order' => 1,
                'active' => true,
            ]);
            
            // Criar cores adicionais baseadas nas faixas de quantidade
            if ($request->has('colors')) {
                foreach ($request->colors as $colorIndex => $colorData) {
                    if (isset($colorData['price']) && !empty($colorData['price']) && $colorData['price'] > 0) {
                        // Pegar from e to correspondente da mesma linha de preços
                        $qtyFrom = $request->prices[$colorIndex]['from'] ?? 0;
                        $qtyTo = $request->prices[$colorIndex]['to'] ?? 9999;
                        
                        \App\Models\SerigraphyColor::create([
                            'name' => "COR + ({$qtyFrom}-{$qtyTo})",
                            'price' => $colorData['price'],
                            'is_neon' => false,
                            'order' => $colorIndex + 2, // +2 porque a primeira cor é ordem 1
                            'active' => true,
                        ]);
                    }
                }
            }

            return redirect()->route('admin.personalization-prices.index')
                ->with('success', 'Tabela de preços de Serigrafia atualizada com sucesso!');
        }

        // Validação padrão para outros tipos
        $validated = $request->validate([
            'prices' => 'required|array',
            'prices.*.size_name' => 'required|string',
            'prices.*.size_dimensions' => 'nullable|string',
            'prices.*.quantity_from' => 'required|integer|min:1',
            'prices.*.quantity_to' => 'nullable|integer|min:1|gte:prices.*.quantity_from',
            'prices.*.price' => 'required|numeric|min:0',
        ]);

        // Deletar preços existentes para este tipo
        PersonalizationPrice::where('personalization_type', $type)->delete();

        // Criar novos preços
        foreach ($validated['prices'] as $priceData) {
            if (!empty($priceData['size_name']) && !empty($priceData['quantity_from']) && !empty($priceData['price'])) {
                PersonalizationPrice::create([
                    'personalization_type' => $type,
                    'size_name' => $priceData['size_name'],
                    'size_dimensions' => $priceData['size_dimensions'],
                    'quantity_from' => $priceData['quantity_from'],
                    'quantity_to' => $priceData['quantity_to'] ?: null,
                    'price' => $priceData['price'],
                ]);
            }
        }

        return redirect()->route('admin.personalization-prices.index')
            ->with('success', 'Preços atualizados com sucesso!');
    }

    public function addPriceRow(Request $request): View
    {
        $type = $request->get('type');
        $sizeName = $request->get('size_name');
        $index = $request->get('index', 0);
        $price = null; // Nova linha de preço sempre começa vazia
        
        return view('admin.personalization-prices.partials.price-row', compact('type', 'sizeName', 'index', 'price'));
    }

    public function getSizesForType(Request $request)
    {
        $type = $request->get('type');
        $sizes = PersonalizationPrice::getSizesForType($type);
        
        return response()->json($sizes);
    }
}
