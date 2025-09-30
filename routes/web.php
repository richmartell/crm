<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\TwoFactorController;
use App\Livewire\ContactForm;
use App\Livewire\ContactList;
use App\Livewire\ContactShow;
use App\Livewire\ListForm;
use App\Livewire\ListIndex;
use App\Livewire\ListShow;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::middleware('auth')->group(function () {
    Route::get('/contacts', ContactList::class)->name('contacts.index');
    Route::get('/contacts/create', ContactForm::class)->name('contacts.create');
    Route::get('/contacts/{contact}', ContactShow::class)->name('contacts.show');
    Route::get('/contacts/{contact}/edit', ContactForm::class)->name('contacts.edit');

    Route::get('/lists', ListIndex::class)->name('lists.index');
    Route::get('/lists/create', ListForm::class)->name('lists.create');
    Route::get('/lists/{list}', ListShow::class)->name('lists.show');
    Route::get('/lists/{list}/edit', ListForm::class)->name('lists.edit');

    Route::view('/profile', 'profile')->name('profile.edit');

    Route::get('/settings/two-factor', [TwoFactorController::class, 'create'])->name('two-factor.settings');
    Route::post('/settings/two-factor/enable', [TwoFactorController::class, 'store'])->name('two-factor.enable');
    Route::delete('/settings/two-factor/disable', [TwoFactorController::class, 'destroy'])->name('two-factor.disable');
});

Route::middleware('auth')->group(function () {
    Route::get('/two-factor/verify', [TwoFactorController::class, 'showVerify'])->name('two-factor.verify');
    Route::post('/two-factor/verify', [TwoFactorController::class, 'verify']);
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

require __DIR__.'/auth.php';