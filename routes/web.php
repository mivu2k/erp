<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebController;
use App\Http\Controllers\AuthController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('dashboard');
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ]);
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [WebController::class, 'dashboard']);

    // Items Routes
    Route::get('/items/export', [\App\Http\Controllers\ItemImportExportController::class, 'export'])->name('items.export');
    Route::post('/items/import', [\App\Http\Controllers\ItemImportExportController::class, 'import'])->name('items.import');
    Route::delete('/items/bulk-destroy', [\App\Http\Controllers\ItemController::class, 'bulkDestroy'])->name('items.bulk_destroy');
    Route::get('/items/{item}/group', [\App\Http\Controllers\ItemController::class, 'showGroup'])->name('items.group');
    Route::resource('items', \App\Http\Controllers\ItemController::class);
    Route::get('/items/lookup/{barcode}', [\App\Http\Controllers\ItemController::class, 'lookup'])->where('barcode', '.*');
    Route::get('/scan', [\App\Http\Controllers\ItemController::class, 'scan']);
    Route::post('/items/{item}/reserve', [\App\Http\Controllers\ItemController::class, 'reserve'])->name('items.reserve');
    Route::post('/items/{item}/sell', [\App\Http\Controllers\ItemController::class, 'sell'])->name('items.sell');

    Route::get('/materials/export', [\App\Http\Controllers\MaterialImportExportController::class, 'export'])->name('materials.export');
    Route::post('/materials/import', [\App\Http\Controllers\MaterialImportExportController::class, 'import'])->name('materials.import');
    Route::resource('materials', \App\Http\Controllers\MaterialController::class);
    Route::resource('suppliers', \App\Http\Controllers\SupplierController::class);
    Route::resource('customers', \App\Http\Controllers\CustomerController::class);
    Route::resource('lots', \App\Http\Controllers\LotController::class);
    Route::get('/orders/{order}/invoice', [\App\Http\Controllers\OrderController::class, 'invoice'])->name('orders.invoice');
    Route::patch('/orders/{order}/status', [\App\Http\Controllers\OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::resource('orders', \App\Http\Controllers\OrderController::class);
    Route::resource('work_orders', \App\Http\Controllers\WorkOrderController::class);

    // Settings & Users
    Route::post('/settings/company', [\App\Http\Controllers\SettingController::class, 'storeCompany'])->name('settings.company.store');
    Route::get('/settings', [App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings/currency', [App\Http\Controllers\SettingController::class, 'storeCurrency'])->name('settings.currency.store');
    Route::patch('/settings/currency/{currency}/default', [App\Http\Controllers\SettingController::class, 'defaultCurrency'])->name('settings.currency.default');
    Route::delete('/settings/currency/{currency}', [App\Http\Controllers\SettingController::class, 'destroyCurrency'])->name('settings.currency.destroy');

    Route::get('/users/create', [App\Http\Controllers\SettingController::class, 'createUser'])->name('users.create');
    Route::post('/users', [App\Http\Controllers\SettingController::class, 'storeUser'])->name('users.store');
    Route::delete('/users/{user}', [App\Http\Controllers\SettingController::class, 'destroyUser'])->name('users.destroy');

    // Reports
    Route::get('/reports', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/sales', [App\Http\Controllers\ReportController::class, 'sales'])->name('reports.sales');
    Route::get('/reports/inventory', [App\Http\Controllers\ReportController::class, 'inventory'])->name('reports.inventory');
    Route::get('/reports/qrcodes', [App\Http\Controllers\ReportController::class, 'qrCodes'])->name('reports.qrcodes');
    Route::get('/reports/qrcodes/stones', [App\Http\Controllers\ReportController::class, 'qrCodesStones'])->name('reports.qrcodes.stones');
});
