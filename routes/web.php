<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\NotesController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'index')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/notes', [NotesController::class, 'watch'])->name('notes.index');
    Route::get('/notes/create', [NotesController::class, 'create'])->name('notes.create');
    Route::get('/notes/{note_id}/edit', [NotesController::class, 'edit'])->name('notes.edit');
    Route::put('/notes/{note_id}', [NotesController::class, 'update'])->name('notes.update');
    Route::delete('/notes/{note_id}', [NotesController::class, 'destroy'])->name('notes.destroy');
    Route::get('/notes/search', [NotesController::class, 'search'])->name('notes.search');

    Route::get('/notes/{note_id}', [NotesController::class, 'show'])->name('notes.show');
    Route::post('/notes', [NotesController::class, 'store'])->name('notes.store');

    Route::post('/auth/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

Route::middleware(['guest'])->group(function () {
    Route::get('/auth/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/auth/register', [RegisteredUserController::class, 'store'])->name('register.store');

    Route::get('/auth/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/auth/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});
