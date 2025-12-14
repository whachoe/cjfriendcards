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

// Relationship routes
Route::post('/cards/{card}/relationships', [RelationshipController::class, 'store'])->name('relationships.store');
Route::patch('/cards/{card}/relationships/{relationship}', [RelationshipController::class, 'update'])->name('relationships.update');
Route::delete('/cards/{card}/relationships/{relationship}', [RelationshipController::class, 'destroy'])->name('relationships.destroy');
Route::get('/cards/{card}/relationships/autocomplete', [RelationshipController::class, 'autocomplete'])->name('relationships.autocomplete');
