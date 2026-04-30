<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\TodoController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    $userId = auth()->id();
    return Inertia::render('Dashboard', [
        'stats' => [
            'total' => \App\Models\Todo::where('user_id', $userId)->count(),
            'completed' => \App\Models\Todo::where('user_id', $userId)->where('completed', true)->count(),
            'high_priority' => \App\Models\Todo::where('user_id', $userId)->where('priority', 'High')->where('completed', false)->count(),
            'overdue' => \App\Models\Todo::where('user_id', $userId)
                ->where('completed', false)
                ->whereNotNull('due_date')
                ->where('due_date', '<', now())
                ->count(),
        ]
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('todos/reorder', [TodoController::class, 'reorder'])->name('todos.reorder');
    Route::resource('todos', TodoController::class);
});

require __DIR__.'/auth.php';
