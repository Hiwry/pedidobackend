<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PersonalizationPrice;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PersonalizationPriceController extends Controller
{
    /**
     * Buscar preço para um tipo de personalização, tamanho e quantidade específicos
     */
    public function getPrice(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|string|in:DTF,SERIGRAFIA,BORDADO,SUBLIMACAO,EMBORRACHADO,SUBLIMACAO_TOTAL',
            'size' => 'required|string',
            'quantity' => 'required|integer|min:1',
        ]);

        \Log::info('API getPrice chamada:', $validated);

        $price = PersonalizationPrice::getPriceForPersonalization(
            $validated['type'],
            $validated['size'],
            $validated['quantity']
        );

        \Log::info('Preço encontrado: ' . ($price ? json_encode($price->toArray()) : 'null'));

        if (!$price) {
            return response()->json([
                'success' => false,
                'message' => 'Preço não encontrado para os parâmetros fornecidos',
                'price' => 0
            ]);
        }

        return response()->json([
            'success' => true,
            'price' => $price->price,
            'size_name' => $price->size_name,
            'size_dimensions' => $price->size_dimensions,
            'quantity_from' => $price->quantity_from,
            'quantity_to' => $price->quantity_to,
        ]);
    }

    /**
     * Buscar todos os tamanhos disponíveis para um tipo de personalização
     */
    public function getSizes(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|string|in:DTF,SERIGRAFIA,BORDADO,SUBLIMACAO,EMBORRACHADO,SUBLIMACAO_TOTAL',
        ]);

        $sizes = PersonalizationPrice::getSizesForType($validated['type']);

        return response()->json([
            'success' => true,
            'sizes' => $sizes
        ]);
    }

    /**
     * Buscar todas as faixas de preço para um tipo e tamanho específicos
     */
    public function getPriceRanges(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|string|in:DTF,SERIGRAFIA,BORDADO,SUBLIMACAO,EMBORRACHADO,SUBLIMACAO_TOTAL',
            'size' => 'required|string',
        ]);

        $ranges = PersonalizationPrice::getPriceRangesForTypeAndSize(
            $validated['type'],
            $validated['size']
        );

        return response()->json([
            'success' => true,
            'ranges' => $ranges
        ]);
    }

    /**
     * Buscar preços para múltiplas combinações (para otimizar requisições)
     */
    public function getMultiplePrices(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'requests' => 'required|array',
            'requests.*.type' => 'required|string|in:DTF,SERIGRAFIA,BORDADO,SUBLIMACAO,EMBORRACHADO,SUBLIMACAO_TOTAL',
            'requests.*.size' => 'required|string',
            'requests.*.quantity' => 'required|integer|min:1',
        ]);

        $results = [];

        foreach ($validated['requests'] as $index => $req) {
            $price = PersonalizationPrice::getPriceForPersonalization(
                $req['type'],
                $req['size'],
                $req['quantity']
            );

            $results[] = [
                'index' => $index,
                'success' => $price !== null,
                'price' => $price ? $price->price : 0,
                'size_name' => $price ? $price->size_name : $req['size'],
                'size_dimensions' => $price ? $price->size_dimensions : null,
                'quantity_from' => $price ? $price->quantity_from : null,
                'quantity_to' => $price ? $price->quantity_to : null,
            ];
        }

        return response()->json([
            'success' => true,
            'results' => $results
        ]);
    }
}
