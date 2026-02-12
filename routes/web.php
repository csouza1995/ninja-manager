<?php

use App\Livewire\Dashboard;
use App\Livewire\Pages\Executors\Index as ExecutorsIndex;
use App\Livewire\Pages\Roles\Index as RolesIndex;
use App\Livewire\Pages\ServiceItems\Index as ServiceItemsIndex;
use App\Livewire\Pages\Clients\Index as ClientsIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', Dashboard::class)->name('dashboard');
Route::get('/executors', ExecutorsIndex::class)->name('executors.index');
Route::get('/roles', RolesIndex::class)->name('roles.index');
Route::get('/service-items', ServiceItemsIndex::class)->name('service-items.index');
Route::get('/clients', ClientsIndex::class)->name('clients.index');
Route::get('/services', \App\Livewire\Pages\Services\Index::class)->name('services.index');
Route::get('/receipts', \App\Livewire\Pages\Receipts\Index::class)->name('receipts.index');
Route::get('/receipts/{receipt}/print', [\App\Http\Controllers\ReceiptController::class, 'print'])->name('receipts.print');
Route::get('/settings/database', \App\Livewire\Pages\Settings\Database::class)->name('settings.database');

Route::prefix('financial')->group(function () {
    Route::get('/dashboard', App\Livewire\Pages\Financial\Dashboard::class)->name('financial.dashboard');
    Route::get('/taxes', App\Livewire\Pages\Financial\Taxes\Index::class)->name('financial.taxes');
    Route::get('/bank-accounts', App\Livewire\Pages\Financial\BankAccounts\Index::class)->name('financial.bank-accounts');
    Route::get('/revenues', App\Livewire\Pages\Financial\Revenues\Index::class)->name('financial.revenues');
    Route::get('/expenditures', App\Livewire\Pages\Financial\Expenditures\Index::class)->name('financial.expenditures');
    Route::get('/invoices', App\Livewire\Pages\Financial\Invoices\Index::class)->name('financial.invoices');
    Route::get('/outflows', App\Livewire\Pages\Financial\Outflows\Index::class)->name('financial.outflows');
});
