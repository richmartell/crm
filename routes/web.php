<?php

use App\Livewire\ContactList;
use App\Livewire\ContactShow;
use App\Livewire\ContactForm;
use Illuminate\Support\Facades\Route;

Route::get('/', ContactList::class)->name('contacts.index');

Route::get('/contacts', ContactList::class)->name('contacts.index');
Route::get('/contacts/create', ContactForm::class)->name('contacts.create');
Route::get('/contacts/{id}', ContactShow::class)->name('contacts.show');
Route::get('/contacts/{id}/edit', ContactForm::class)->name('contacts.edit');