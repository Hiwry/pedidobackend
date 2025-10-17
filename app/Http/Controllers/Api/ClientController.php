<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ProductOption;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $query = $request->input('q', '');
        
        if (strlen($query) < 3) {
            return response()->json([]);
        }

        $clients = Client::where('name', 'LIKE', "%{$query}%")
            ->orWhere('phone_primary', 'LIKE', "%{$query}%")
            ->orWhere('cpf_cnpj', 'LIKE', "%{$query}%")
            ->orWhere('email', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get();

        return response()->json($clients);
    }

    public function getProductOptions(): JsonResponse
    {
        $options = ProductOption::where('active', true)
            ->orderBy('order')
            ->get()
            ->groupBy('type');

        return response()->json($options);
    }

    public function getProductOptionsWithParents(): JsonResponse
    {
        $options = [
            'personalizacao' => ProductOption::where('type', 'personalizacao')->where('active', true)->orderBy('order')->get(),
            'tecido' => ProductOption::with('parents')->where('type', 'tecido')->where('active', true)->orderBy('order')->get()->map(function($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'price' => $item->price,
                    'parent_ids' => $item->parents->pluck('id')->toArray(),
                ];
            }),
            'tipo_tecido' => ProductOption::with('parents')->where('type', 'tipo_tecido')->where('active', true)->orderBy('order')->get()->map(function($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'price' => $item->price,
                    'parent_id' => $item->parent_id,
                    'parent_ids' => $item->parents->pluck('id')->toArray(),
                ];
            }),
            'cor' => ProductOption::with('parents')->where('type', 'cor')->where('active', true)->orderBy('order')->get()->map(function($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'price' => $item->price,
                    'parent_ids' => $item->parents->pluck('id')->toArray(),
                ];
            }),
            'tipo_corte' => ProductOption::with('parents')->where('type', 'tipo_corte')->where('active', true)->orderBy('order')->get()->map(function($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'price' => $item->price,
                    'parent_ids' => $item->parents->pluck('id')->toArray(),
                ];
            }),
            'detalhe' => ProductOption::with('parents')->where('type', 'detalhe')->where('active', true)->orderBy('order')->get()->map(function($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'price' => $item->price,
                    'parent_ids' => $item->parents->pluck('id')->toArray(),
                ];
            }),
            'gola' => ProductOption::with('parents')->where('type', 'gola')->where('active', true)->orderBy('order')->get()->map(function($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'price' => $item->price,
                    'parent_ids' => $item->parents->pluck('id')->toArray(),
                ];
            }),
        ];

        return response()->json($options);
    }

    public function updateItemCoverImage(Request $request, $id): JsonResponse
    {
        try {
            $request->validate([
                'cover_image' => 'required|image|max:10240', // Máximo 10MB
            ]);

            $item = \App\Models\OrderItem::findOrFail($id);
            
            // Processar e salvar a imagem
            $coverImage = $request->file('cover_image');
            $coverImageName = time() . '_' . uniqid() . '_' . $coverImage->getClientOriginalName();
            $coverImagePath = $coverImage->storeAs('orders/items/covers', $coverImageName, 'public');
            
            // Atualizar o item com a nova imagem de capa
            $item->update(['cover_image' => $coverImagePath]);
            
            return response()->json([
                'success' => true,
                'message' => 'Imagem de capa atualizada com sucesso!',
                'cover_image_path' => $coverImagePath
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação: ' . implode(', ', $e->errors()['cover_image'] ?? ['Arquivo inválido'])
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar imagem de capa do item: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }

    public function getSublimationSizes(): JsonResponse
    {
        $sizes = \App\Models\SublimationSize::where('active', true)
            ->orderBy('order')
            ->get();

        return response()->json($sizes);
    }

    public function getSublimationLocations(): JsonResponse
    {
        $locations = \App\Models\SublimationLocation::where('active', true)
            ->orderBy('order')
            ->get();

        return response()->json($locations);
    }

    public function getSublimationPrice($sizeId, $quantity): JsonResponse
    {
        $size = \App\Models\SublimationSize::findOrFail($sizeId);
        $priceData = $size->getPriceForQuantity($quantity);

        if (!$priceData) {
            return response()->json(['price' => 0], 404);
        }

        return response()->json([
            'price' => $priceData->price,
            'quantity_from' => $priceData->quantity_from,
            'quantity_to' => $priceData->quantity_to,
        ]);
    }

    public function getSerigraphyColors(): JsonResponse
    {
        $colors = \App\Models\SerigraphyColor::where('active', true)
            ->orderBy('order')
            ->get();

        \Log::info('API getSerigraphyColors chamada, cores encontradas: ' . json_encode($colors->toArray()));

        return response()->json($colors);
    }

    public function getSizeSurcharge($size, $totalPrice): JsonResponse
    {
        $surcharge = \App\Models\SizeSurcharge::getSurchargeForSize($size, $totalPrice);

        if (!$surcharge) {
            return response()->json(['surcharge' => 0]);
        }

        return response()->json([
            'surcharge' => $surcharge->surcharge,
            'price_from' => $surcharge->price_from,
            'price_to' => $surcharge->price_to,
        ]);
    }
}
