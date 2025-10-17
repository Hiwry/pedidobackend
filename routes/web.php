<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Rotas Públicas para Clientes
Route::prefix('pedido')->name('client.order.')->group(function () {
    Route::get('/{token}', [\App\Http\Controllers\ClientOrderController::class, 'show'])->name('show');
    Route::post('/{token}/confirmar', [\App\Http\Controllers\ClientOrderController::class, 'confirm'])->name('confirm');
});

// Todas as rotas autenticadas
Route::middleware('auth')->group(function () {
    // Home/Dashboard
    Route::get('/', [\App\Http\Controllers\DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    
    // Lista de Pedidos
    Route::get('/pedidos', [\App\Http\Controllers\OrderController::class, 'index'])->name('orders.index');
    Route::get('/pedidos/{id}/detalhes', [\App\Http\Controllers\OrderController::class, 'show'])->name('orders.show');
    
    // Gerenciamento de Pagamentos
    Route::post('/pedidos/{id}/pagamento/adicionar', [\App\Http\Controllers\OrderController::class, 'addPayment'])->name('orders.payment.add');
    Route::put('/pedidos/{id}/pagamento/editar', [\App\Http\Controllers\OrderController::class, 'updatePayment'])->name('orders.payment.update');
    Route::delete('/pedidos/{id}/pagamento/remover', [\App\Http\Controllers\OrderController::class, 'deletePayment'])->name('orders.payment.delete');
    
    // Download de Nota do Cliente
    Route::get('/pedidos/{id}/nota-cliente', [\App\Http\Controllers\OrderController::class, 'downloadClientReceipt'])->name('orders.client-receipt');
    
    // Gerar Link de Compartilhamento
    Route::post('/pedidos/{id}/gerar-link', [\App\Http\Controllers\OrderController::class, 'generateShareLink'])->name('orders.generate-share-link');
    
    // Edição de Pedidos
    Route::get('/pedidos/{id}/pagamento/{paymentId}', [\App\Http\Controllers\OrderController::class, 'getPayment'])->name('orders.payment.get');
    Route::post('/pedidos/{id}/solicitar-edicao', [\App\Http\Controllers\OrderController::class, 'requestEdit'])->name('orders.request-edit');
    Route::post('/pedidos/{id}/aprovar-edicao', [\App\Http\Controllers\OrderController::class, 'approveEdit'])->name('orders.approve-edit');
    Route::post('/pedidos/{id}/rejeitar-edicao', [\App\Http\Controllers\OrderController::class, 'rejectEdit'])->name('orders.reject-edit');
    Route::get('/pedidos/{id}/editar', [\App\Http\Controllers\OrderController::class, 'editOrder'])->name('orders.edit');
    Route::put('/pedidos/{id}/atualizar', [\App\Http\Controllers\OrderController::class, 'updateOrder'])->name('orders.update');


    // Wizard de Pedido (5 etapas)
    Route::prefix('pedidos')->group(function () {
        Route::get('novo', [\App\Http\Controllers\OrderWizardController::class, 'start'])->name('orders.wizard.start');
        Route::post('cliente', [\App\Http\Controllers\OrderWizardController::class, 'storeClient'])->name('orders.wizard.client');
        Route::match(['get','post'],'costura', [\App\Http\Controllers\OrderWizardController::class, 'sewing'])->name('orders.wizard.sewing');
        Route::match(['get','post'],'personalizacao', [\App\Http\Controllers\OrderWizardController::class, 'customization'])->name('orders.wizard.customization');
        Route::match(['get','post'],'pagamento', [\App\Http\Controllers\OrderWizardController::class, 'payment'])->name('orders.wizard.payment');
        Route::get('confirmacao', [\App\Http\Controllers\OrderWizardController::class, 'confirm'])->name('orders.wizard.confirm');
        Route::post('finalizar', [\App\Http\Controllers\OrderWizardController::class, 'finalize'])->name('orders.wizard.finalize');
        Route::get('finalizar', function () {
            return redirect()->route('kanban.index')->with('info', 'Pedido já foi finalizado ou sessão expirou.');
        });
    });

    // Kanban
    Route::get('/kanban', [\App\Http\Controllers\KanbanController::class, 'index'])->name('kanban.index');
    Route::post('/kanban/update-status', [\App\Http\Controllers\KanbanController::class, 'updateStatus'])->name('kanban.update-status');
    Route::get('/kanban/order/{id}', [\App\Http\Controllers\KanbanController::class, 'getOrderDetails']);
    Route::post('/kanban/order/{id}/comment', [\App\Http\Controllers\KanbanController::class, 'addComment']);
    Route::post('/kanban/order/{id}/add-payment', [\App\Http\Controllers\KanbanController::class, 'addPayment']);
    Route::get('/kanban/download-costura/{id}', [\App\Http\Controllers\KanbanController::class, 'downloadCostura']);
    Route::get('/kanban/download-personalizacao/{id}', [\App\Http\Controllers\KanbanController::class, 'downloadPersonalizacao']);
    Route::get('/kanban/download-files/{id}', [\App\Http\Controllers\KanbanController::class, 'downloadFiles']);

    // Produção
    Route::prefix('producao')->name('production.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ProductionController::class, 'index'])->name('index');
        Route::get('/kanban', [\App\Http\Controllers\ProductionController::class, 'kanban'])->name('kanban');
    });

    // Gerenciamento de Colunas do Kanban
    Route::prefix('kanban/columns')->name('kanban.columns.')->group(function () {
        Route::get('/', [\App\Http\Controllers\StatusController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\StatusController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\StatusController::class, 'store'])->name('store');
        Route::get('/{status}/edit', [\App\Http\Controllers\StatusController::class, 'edit'])->name('edit');
        Route::put('/{status}', [\App\Http\Controllers\StatusController::class, 'update'])->name('update');
        Route::delete('/{status}', [\App\Http\Controllers\StatusController::class, 'destroy'])->name('destroy');
        Route::post('/reorder', [\App\Http\Controllers\StatusController::class, 'reorder'])->name('reorder');
        Route::post('/{status}/move-orders', [\App\Http\Controllers\StatusController::class, 'moveOrders'])->name('move-orders');
    });

    // API Routes
    Route::prefix('api')->group(function () {
        Route::get('/clients/search', [\App\Http\Controllers\Api\ClientController::class, 'search']);
        Route::get('/product-options', [\App\Http\Controllers\Api\ClientController::class, 'getProductOptions']);
        Route::get('/product-options-with-parents', [\App\Http\Controllers\Api\ClientController::class, 'getProductOptionsWithParents']);
        Route::get('/sublimation-sizes', [\App\Http\Controllers\Api\ClientController::class, 'getSublimationSizes']);
        Route::get('/sublimation-locations', [\App\Http\Controllers\Api\ClientController::class, 'getSublimationLocations']);
        Route::get('/sublimation-price/{sizeId}/{quantity}', [\App\Http\Controllers\Api\ClientController::class, 'getSublimationPrice']);
        Route::get('/serigraphy-colors', [\App\Http\Controllers\Api\ClientController::class, 'getSerigraphyColors']);
        Route::get('/size-surcharge/{size}/{totalPrice}', [\App\Http\Controllers\Api\ClientController::class, 'getSizeSurcharge']);
        
        // Preços de Personalização (novo sistema unificado)
        Route::get('/personalization-prices/price', [\App\Http\Controllers\Api\PersonalizationPriceController::class, 'getPrice']);
        Route::get('/personalization-prices/sizes', [\App\Http\Controllers\Api\PersonalizationPriceController::class, 'getSizes']);
        Route::get('/personalization-prices/ranges', [\App\Http\Controllers\Api\PersonalizationPriceController::class, 'getPriceRanges']);
        Route::post('/personalization-prices/multiple', [\App\Http\Controllers\Api\PersonalizationPriceController::class, 'getMultiplePrices']);
    });

    // Caixa
    Route::get('/cash/test', function() {
        return view('cash.test');
    })->name('cash.test');
    Route::resource('cash', \App\Http\Controllers\CashController::class);

    // Solicitações de Antecipação de Entrega
    Route::post('/delivery-requests', [\App\Http\Controllers\DeliveryRequestController::class, 'store']);
    Route::get('/delivery-requests', [\App\Http\Controllers\DeliveryRequestController::class, 'index'])->name('delivery-requests.index');
    Route::post('/delivery-requests/{deliveryRequest}/approve', [\App\Http\Controllers\DeliveryRequestController::class, 'approve'])->name('delivery-requests.approve');
    Route::post('/delivery-requests/{deliveryRequest}/reject', [\App\Http\Controllers\DeliveryRequestController::class, 'reject'])->name('delivery-requests.reject');

    // Perfil do usuário
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin (apenas para administradores)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // Dashboard principal
    Route::get('/', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('product-options', \App\Http\Controllers\Admin\ProductOptionController::class);
    Route::resource('settings', \App\Http\Controllers\Admin\SettingController::class);
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    
    // Preços de Sublimação (legado)
    Route::get('sublimation-prices', [\App\Http\Controllers\Admin\SublimationPriceController::class, 'index'])->name('sublimation-prices.index');
    Route::get('sublimation-prices/{size}/edit', [\App\Http\Controllers\Admin\SublimationPriceController::class, 'edit'])->name('sublimation-prices.edit');
    Route::put('sublimation-prices/{size}', [\App\Http\Controllers\Admin\SublimationPriceController::class, 'update'])->name('sublimation-prices.update');
    Route::get('sublimation-prices/add-row', [\App\Http\Controllers\Admin\SublimationPriceController::class, 'addPriceRow'])->name('sublimation-prices.add-row');
    
    // Preços de Personalização (novo sistema unificado)
    Route::get('personalization-prices', [\App\Http\Controllers\Admin\PersonalizationPriceController::class, 'index'])->name('personalization-prices.index');
    Route::get('personalization-prices/{type}/edit', [\App\Http\Controllers\Admin\PersonalizationPriceController::class, 'edit'])->name('personalization-prices.edit');
    Route::put('personalization-prices/{type}', [\App\Http\Controllers\Admin\PersonalizationPriceController::class, 'update'])->name('personalization-prices.update');
    Route::get('personalization-prices/add-row', [\App\Http\Controllers\Admin\PersonalizationPriceController::class, 'addPriceRow'])->name('personalization-prices.add-row');
    Route::get('personalization-prices/sizes', [\App\Http\Controllers\Admin\PersonalizationPriceController::class, 'getSizesForType'])->name('personalization-prices.sizes');
});


// Webhook para deploy automático
Route::post('/deploy', function() {
    // Incluir o script de deploy
    include __DIR__ . '/../deploy.php';
})->name('deploy.webhook');

require __DIR__.'/auth.php';