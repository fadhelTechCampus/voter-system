<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Voter;
use Illuminate\Http\Request;
use App\Http\Controllers\VoteController;

Route::get('/vote/{token}', [VoteController::class, 'show'])->name('vote.show');
Route::post('/vote/finish', [VoteController::class, 'finish'])->name('vote.finish');

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// ✅ VOTER LINK (React + Inertia)
Route::get('/vote/{token}', function (string $token, Request $request) {
    $voter = Voter::where('token', $token)->first();

    if (!$voter || $voter->token_used || $voter->voted) {
        abort(403, 'Invalid or already used voting link.');
    }

    // mark as used and store in session
    $voter->update(['token_used' => true]);
    $request->session()->put('voter_id', $voter->id);

    return Inertia::render('VotePage', [
        'voter' => [
            'id' => $voter->id,
            'name' => $voter->name,
        ],
    ]);
});

Route::get('/vote/finished', function (Request $request) {
    if ($voterId = $request->session()->get('voter_id')) {
        $voter = \App\Models\Voter::find($voterId);
        if ($voter) {
            $voter->update([
                'voted' => true,
                'voted_at' => now(),
            ]);
        }
        $request->session()->forget('voter_id');
    }
    return Inertia::render('VoteFinished');
});