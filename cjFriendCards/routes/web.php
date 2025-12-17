<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CardController;
use App\Http\Controllers\RelationshipController;

Route::get('/', function () {
    return view('welcome');
});

// Card management routes
Route::resource('cards', CardController::class);

// Birthday calendar view
Route::get('/birthday-calendar', [CardController::class, 'birthdayCalendar'])->name('cards.birthday-calendar');

// Card export
Route::get('/cards/{card}/export/vcard', [CardController::class, 'exportVcard'])->name('cards.export-vcard');
Route::get('/cards/export/csv', [CardController::class, 'exportCsv'])->name('cards.export-csv');

// Relationship routes
Route::post('/cards/{card}/relationships', [RelationshipController::class, 'store'])->name('relationships.store');
Route::patch('/cards/{card}/relationships/{relationship}', [RelationshipController::class, 'update'])->name('relationships.update');
Route::delete('/cards/{card}/relationships/{relationship}', [RelationshipController::class, 'destroy'])->name('relationships.destroy');
Route::get('/cards/{card}/relationships/autocomplete', [RelationshipController::class, 'autocomplete'])->name('relationships.autocomplete');
