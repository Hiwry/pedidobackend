<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonalizationPrice extends Model
{
    protected $fillable = [
        'personalization_type',
        'size_name',
        'size_dimensions',
        'quantity_from',
        'quantity_to',
        'price',
        'active',
        'order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'active' => 'boolean',
    ];

    /**
     * Buscar preço para um tipo de personalização, tamanho e quantidade específicos
     */
    public static function getPriceForPersonalization($type, $sizeName, $quantity)
    {
        return static::where('personalization_type', $type)
            ->where('size_name', $sizeName)
            ->where('quantity_from', '<=', $quantity)
            ->where(function($query) use ($quantity) {
                $query->whereNull('quantity_to')
                      ->orWhere('quantity_to', '>=', $quantity);
            })
            ->orderBy('quantity_from', 'desc')
            ->first();
    }

    /**
     * Buscar todos os tamanhos disponíveis para um tipo de personalização
     */
    public static function getSizesForType($type)
    {
        return static::where('personalization_type', $type)
            ->select('size_name', 'size_dimensions')
            ->distinct()
            ->orderBy('order')
            ->get();
    }

    /**
     * Buscar todas as faixas de preço para um tipo e tamanho específicos
     */
    public static function getPriceRangesForTypeAndSize($type, $sizeName)
    {
        return static::where('personalization_type', $type)
            ->where('size_name', $sizeName)
            ->orderBy('quantity_from')
            ->get();
    }

    /**
     * Tipos de personalização disponíveis
     */
    public static function getPersonalizationTypes()
    {
        return [
            'DTF' => 'DTF',
            'SERIGRAFIA' => 'Serigrafia',
            'BORDADO' => 'Bordado',
            'EMBORRACHADO' => 'Emborrachado',
            'SUB. LOCAL' => 'Sublimação Local',
            'SUB. TOTAL' => 'Sublimação Total',
        ];
    }
}