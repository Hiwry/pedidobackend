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
            ->orderBy('quantity_from')
            ->orderBy('size_name')
            ->get();

        // Debug: log dos dados carregados
        \Log::info('=== EDIT DEBUG FOR ' . $type . ' ===');
        \Log::info('Prices loaded:', $prices->toArray());
        \Log::info('Prices count: ' . $prices->count());

        return view('admin.personalization-prices.edit', compact('type', 'types', 'sizes', 'prices'));
    }

    public function update(Request $request, $type): RedirectResponse
    {
        // Lógica unificada para todos os tipos

        // Debug: log dos dados recebidos
        \Log::info('=== UPDATE DEBUG FOR ' . $type . ' ===');
        \Log::info('Request all data:', $request->all());
        \Log::info('Prices data:', $request->prices ?? []);
        
        // Detectar formato dos dados (novo ou antigo)
        $isNewFormat = isset($request->prices[0]['quantity_from']);
        
        if ($isNewFormat) {
            // Novo formato: quantity_from, quantity_to, tamanhos dinâmicos
            $validated = $request->validate([
                'prices' => 'required|array|min:1',
                'prices.*.quantity_from' => 'required|integer|min:1',
                'prices.*.quantity_to' => 'nullable|integer|min:1',
            ]);
            $validated['prices'] = $request->prices;
        } else {
            // Formato antigo: from, to, ESCUDO, A4, A3
            $validated = $request->validate([
                'prices' => 'required|array|min:1',
                'prices.*.from' => 'required|integer|min:1',
                'prices.*.to' => 'nullable|integer|min:1',
            ]);
            $validated['prices'] = $request->prices;
        }
        
        \Log::info('Validated data:', $validated);

        // Deletar preços existentes para este tipo
        PersonalizationPrice::where('personalization_type', $type)->delete();

        // Criar novos preços para cada faixa de quantidade e cada tamanho
        foreach ($validated['prices'] as $priceData) {
            if ($isNewFormat) {
                // Novo formato: quantity_from, quantity_to, tamanhos dinâmicos
                $quantityFrom = $priceData['quantity_from'];
                $quantityTo = $priceData['quantity_to'] ?? null;
                
                // Processar todos os campos que não são quantity_from ou quantity_to
                foreach ($priceData as $key => $value) {
                    if ($key !== 'quantity_from' && $key !== 'quantity_to' && !empty($value)) {
                        $newPrice = PersonalizationPrice::create([
                            'personalization_type' => $type,
                            'size_name' => strtoupper($key),
                            'size_dimensions' => null,
                            'quantity_from' => $quantityFrom,
                            'quantity_to' => $quantityTo,
                            'price' => $value,
                        ]);
                        
                        \Log::info('Created price record (new format):', $newPrice->toArray());
                    }
                }
            } else {
                // Formato antigo: from, to, ESCUDO, A4, A3
                $quantityFrom = $priceData['from'];
                $quantityTo = $priceData['to'] ?? null;
                
                // Processar tamanhos fixos
                $sizes = ['ESCUDO', 'A4', 'A3'];
                foreach ($sizes as $size) {
                    if (isset($priceData[$size]) && !empty($priceData[$size])) {
                        $newPrice = PersonalizationPrice::create([
                            'personalization_type' => $type,
                            'size_name' => $size,
                            'size_dimensions' => null,
                            'quantity_from' => $quantityFrom,
                            'quantity_to' => $quantityTo,
                            'price' => $priceData[$size],
                        ]);
                        
                        \Log::info('Created price record (old format):', $newPrice->toArray());
                    }
                }
            }
        }
        
        // Processar cores separadas se for SERIGRAFIA
        if ($type === 'SERIGRAFIA' && $request->has('color_prices')) {
            \Log::info('Processing separate color prices for SERIGRAFIA:', $request->color_prices);
            
            // Atualizar preços das cores individualmente
            foreach ($request->color_prices as $colorId => $price) {
                if (!empty($price)) {
                    \App\Models\SerigraphyColor::where('id', $colorId)
                        ->update(['price' => $price]);
                    
                    \Log::info("Updated color ID {$colorId} to {$price}");
                }
            }
        }
        
        // Debug: verificar se os dados foram salvos
        $savedPrices = PersonalizationPrice::where('personalization_type', $type)->get();
        \Log::info('Total prices saved for ' . $type . ':', $savedPrices->toArray());

        return redirect()->route('admin.personalization-prices.edit', $type)
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
