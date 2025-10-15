<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Order;
use App\Models\OrderItem;

echo "=== VERIFICAÇÃO DOS DADOS ATUAIS ===\n\n";

// Verificar pedido ID 2 (que está sendo editado)
$order = Order::with(['client', 'items'])->find(2);
if (!$order) {
    echo "Pedido ID 2 não encontrado!\n";
    exit;
}

echo "Pedido ID: {$order->id}\n";
echo "Cliente: {$order->client->name}\n";
echo "Itens: {$order->items->count()}\n";
echo "Última atualização: {$order->updated_at}\n\n";

echo "ITENS ATUAIS NO BANCO:\n";
foreach ($order->items as $item) {
    echo "- Item {$item->item_number}:\n";
    echo "  * ID: {$item->id}\n";
    echo "  * Personalização: {$item->print_type}\n";
    echo "  * Tecido: {$item->fabric}\n";
    echo "  * Cor: {$item->color}\n";
    echo "  * Gola: {$item->collar}\n";
    echo "  * Modelo: {$item->model}\n";
    echo "  * Detalhe: {$item->detail}\n";
    echo "  * Quantidade: {$item->quantity}\n";
    echo "  * Preço Unit.: R$ {$item->unit_price}\n";
    echo "  * Total: R$ {$item->total_price}\n";
    echo "  * Última atualização: {$item->updated_at}\n";
    echo "  * Tamanhos: " . ($item->sizes ? json_encode(json_decode($item->sizes, true)) : 'N/A') . "\n";
    echo "\n";
}

echo "=== FIM VERIFICAÇÃO ===\n";
?>
