<?php

namespace App\Http\Controllers;

use App\Models\Voter;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VoteController extends Controller
{
    // show voting form
    public function show(string $token, Request $request)
    {
        $voter = Voter::where('token', $token)->first();

        // invalid link
        if (!$voter) {
            return Inertia::render('Vote403', [
                'name' => null,
                'reason' => 'invalid'
            ]);
        }

        // already voted
        if ($voter->voted) {
            return Inertia::render('Vote403', [
                'name' => $voter->name,
                'reason' => 'already'
            ]);
        }

        // link already used once (prevent second login)
        if ($voter->token_used) {
            return Inertia::render('Vote403', [
                'name' => $voter->name,
                'reason' => 'used'
            ]);
        }

        // mark token as used
        $voter->update(['token_used' => true]);
        $request->session()->put('voter_id', $voter->id);

        $googleFormUrl = 'https://docs.google.com/forms/d/e/1FAIpQLSd__7ivQQ5v7jDLPT4sSMTCH-I4-UmIphqjGgo-9v-Fygkw6g/viewform?embedded=true';

        return Inertia::render('Vote', [
            'name' => $voter->name,
            'token' => $voter->token,
            'googleFormUrl' => $googleFormUrl,
        ]);
    }

    // when voter finishes form
    public function finish(Request $request)
    {
        if ($voterId = $request->session()->get('voter_id')) {
            $voter = Voter::find($voterId);
            if ($voter) {
                $voter->update([
                    'voted' => true,
                    'voted_at' => now(),
                ]);
            }
            $request->session()->forget('voter_id');
        }

        return Inertia::render('Vote403', [
            'name' => $voter->name ?? null,
            'reason' => 'already'
        ]);
    }
}