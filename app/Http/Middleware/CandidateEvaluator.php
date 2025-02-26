<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CandidateEvaluator
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $candidate = Candidate::find($request->route('candidate')); // Obtén el candidato de la ruta

        if (!$candidate || $candidate->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para gestionar este candidato.');
        }

        return $next($request);
    }
}
