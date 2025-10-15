<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Order;
use App\Models\OrderItem;

echo "=== CORRIGINDO TOTAL_PRICE ===\n\n";

// Corrigir todos os itens que têm total_price incorreto
$items = OrderItem::all();
$fixed = 0;

foreach ($items as $item) {
    $correctTotal = $item->quantity * $item->unit_price;
    if ($item->total_price != $correctTotal) {
        echo "Item ID {$item->id}: {$item->quantity} x R$ {$item->unit_price} = R$ {$correctTotal} (era R$ {$item->total_price})\n";
        $item->update(['total_price' => $correctTotal]);
        $fixed++;
    }
}

echo "\nTotal de itens corrigidos: {$fixed}\n";
echo "=== FIM CORREÇÃO ===\n";
?>
