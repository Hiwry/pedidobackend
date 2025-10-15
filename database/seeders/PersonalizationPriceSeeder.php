<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PersonalizationPrice;

class PersonalizationPriceSeeder extends Seeder
{
    public function run(): void
    {
        // DTF - Preços baseados no guia existente
        $dtfPrices = [
            ['size_name' => '10x15cm', 'size_dimensions' => '10x15cm', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 8.00],
            ['size_name' => '10x15cm', 'size_dimensions' => '10x15cm', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 6.00],
            ['size_name' => '10x15cm', 'size_dimensions' => '10x15cm', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 4.00],
            
            ['size_name' => 'A4', 'size_dimensions' => '21x29.7cm', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 12.00],
            ['size_name' => 'A4', 'size_dimensions' => '21x29.7cm', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 9.00],
            ['size_name' => 'A4', 'size_dimensions' => '21x29.7cm', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 6.00],
            
            ['size_name' => 'A3', 'size_dimensions' => '29.7x42cm', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 20.00],
            ['size_name' => 'A3', 'size_dimensions' => '29.7x42cm', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 15.00],
            ['size_name' => 'A3', 'size_dimensions' => '29.7x42cm', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 10.00],
        ];

        foreach ($dtfPrices as $price) {
            PersonalizationPrice::create(array_merge($price, ['personalization_type' => 'DTF']));
        }

        // SERIGRAFIA - Preços baseados no guia existente
        $serigraphyPrices = [
            ['size_name' => 'A4', 'size_dimensions' => '21x29.7cm', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 15.00],
            ['size_name' => 'A4', 'size_dimensions' => '21x29.7cm', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 12.00],
            ['size_name' => 'A4', 'size_dimensions' => '21x29.7cm', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 8.00],
            
            ['size_name' => 'A3', 'size_dimensions' => '29.7x42cm', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 25.00],
            ['size_name' => 'A3', 'size_dimensions' => '29.7x42cm', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 20.00],
            ['size_name' => 'A3', 'size_dimensions' => '29.7x42cm', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 15.00],
            
            ['size_name' => '20x30cm', 'size_dimensions' => '20x30cm', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 18.00],
            ['size_name' => '20x30cm', 'size_dimensions' => '20x30cm', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 14.00],
            ['size_name' => '20x30cm', 'size_dimensions' => '20x30cm', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 10.00],
        ];

        foreach ($serigraphyPrices as $price) {
            PersonalizationPrice::create(array_merge($price, ['personalization_type' => 'SERIGRAFIA']));
        }

        // BORDADO - Preços baseados no guia existente
        $embroideryPrices = [
            ['size_name' => '5x5cm', 'size_dimensions' => '5x5cm', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 12.00],
            ['size_name' => '5x5cm', 'size_dimensions' => '5x5cm', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 10.00],
            ['size_name' => '5x5cm', 'size_dimensions' => '5x5cm', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 8.00],
            
            ['size_name' => '10x10cm', 'size_dimensions' => '10x10cm', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 18.00],
            ['size_name' => '10x10cm', 'size_dimensions' => '10x10cm', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 15.00],
            ['size_name' => '10x10cm', 'size_dimensions' => '10x10cm', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 12.00],
            
            ['size_name' => '15x15cm', 'size_dimensions' => '15x15cm', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 25.00],
            ['size_name' => '15x15cm', 'size_dimensions' => '15x15cm', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 20.00],
            ['size_name' => '15x15cm', 'size_dimensions' => '15x15cm', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 16.00],
        ];

        foreach ($embroideryPrices as $price) {
            PersonalizationPrice::create(array_merge($price, ['personalization_type' => 'BORDADO']));
        }

        // SUBLIMACAO - Preços baseados no sistema existente
        $sublimationPrices = [
            ['size_name' => 'A4', 'size_dimensions' => '21x29.7cm', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 10.00],
            ['size_name' => 'A4', 'size_dimensions' => '21x29.7cm', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 8.00],
            ['size_name' => 'A4', 'size_dimensions' => '21x29.7cm', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 6.00],
            
            ['size_name' => 'A3', 'size_dimensions' => '29.7x42cm', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 15.00],
            ['size_name' => 'A3', 'size_dimensions' => '29.7x42cm', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 12.00],
            ['size_name' => 'A3', 'size_dimensions' => '29.7x42cm', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 9.00],
            
            ['size_name' => '20x30cm', 'size_dimensions' => '20x30cm', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 12.00],
            ['size_name' => '20x30cm', 'size_dimensions' => '20x30cm', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 10.00],
            ['size_name' => '20x30cm', 'size_dimensions' => '20x30cm', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 8.00],
        ];

        foreach ($sublimationPrices as $price) {
            PersonalizationPrice::create(array_merge($price, ['personalization_type' => 'SUBLIMACAO']));
        }

        // EMBORRACHADO - Preços baseados em tamanhos comuns
        $emborrachadoPrices = [
            ['size_name' => '5x5cm', 'size_dimensions' => '5x5cm', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 15.00],
            ['size_name' => '5x5cm', 'size_dimensions' => '5x5cm', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 12.00],
            ['size_name' => '5x5cm', 'size_dimensions' => '5x5cm', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 9.00],
            
            ['size_name' => '10x10cm', 'size_dimensions' => '10x10cm', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 25.00],
            ['size_name' => '10x10cm', 'size_dimensions' => '10x10cm', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 20.00],
            ['size_name' => '10x10cm', 'size_dimensions' => '10x10cm', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 15.00],
            
            ['size_name' => '15x15cm', 'size_dimensions' => '15x15cm', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 35.00],
            ['size_name' => '15x15cm', 'size_dimensions' => '15x15cm', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 28.00],
            ['size_name' => '15x15cm', 'size_dimensions' => '15x15cm', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 22.00],
            
            ['size_name' => '20x20cm', 'size_dimensions' => '20x20cm', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 45.00],
            ['size_name' => '20x20cm', 'size_dimensions' => '20x20cm', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 36.00],
            ['size_name' => '20x20cm', 'size_dimensions' => '20x20cm', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 28.00],
        ];

        foreach ($emborrachadoPrices as $price) {
            PersonalizationPrice::create(array_merge($price, ['personalization_type' => 'EMBORRACHADO']));
        }

        // SUBLIMACAO_TOTAL - Preços para sublimação em peças inteiras
        $sublimacaoTotalPrices = [
            ['size_name' => 'P', 'size_dimensions' => 'Pequeno', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 8.00],
            ['size_name' => 'P', 'size_dimensions' => 'Pequeno', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 6.00],
            ['size_name' => 'P', 'size_dimensions' => 'Pequeno', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 4.00],
            
            ['size_name' => 'M', 'size_dimensions' => 'Médio', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 10.00],
            ['size_name' => 'M', 'size_dimensions' => 'Médio', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 8.00],
            ['size_name' => 'M', 'size_dimensions' => 'Médio', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 6.00],
            
            ['size_name' => 'G', 'size_dimensions' => 'Grande', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 12.00],
            ['size_name' => 'G', 'size_dimensions' => 'Grande', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 10.00],
            ['size_name' => 'G', 'size_dimensions' => 'Grande', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 8.00],
            
            ['size_name' => 'GG', 'size_dimensions' => 'Extra Grande', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 15.00],
            ['size_name' => 'GG', 'size_dimensions' => 'Extra Grande', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 12.00],
            ['size_name' => 'GG', 'size_dimensions' => 'Extra Grande', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 10.00],
            
            ['size_name' => 'XG', 'size_dimensions' => 'Super Grande', 'quantity_from' => 1, 'quantity_to' => 9, 'price' => 18.00],
            ['size_name' => 'XG', 'size_dimensions' => 'Super Grande', 'quantity_from' => 10, 'quantity_to' => 49, 'price' => 15.00],
            ['size_name' => 'XG', 'size_dimensions' => 'Super Grande', 'quantity_from' => 50, 'quantity_to' => null, 'price' => 12.00],
        ];

        foreach ($sublimacaoTotalPrices as $price) {
            PersonalizationPrice::create(array_merge($price, ['personalization_type' => 'SUBLIMACAO_TOTAL']));
        }
    }
}