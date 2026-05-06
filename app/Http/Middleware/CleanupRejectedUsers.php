<?php

namespace App\Http\Middleware;

use App\Models\UserDocument;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class CleanupRejectedUsers
{
    public function handle(Request $request, Closure $next): Response
    {
        // Eliminar usuarios rechazados hace más de 60 segundos
        $expired = UserDocument::where('status', 'rejected')
            ->whereNotNull('rejected_at')
            ->where('rejected_at', '<=', now()->subSeconds(60))
            ->with('user')
            ->get();

        foreach ($expired as $doc) {
            foreach (['selfie', 'dni_front', 'dni_back', 'utility_bill'] as $field) {
                if ($doc->$field) {
                    Storage::disk('public')->delete($doc->$field);
                }
            }

            if ($doc->user) {
                Storage::disk('public')->deleteDirectory('documents/' . $doc->user_id);
                $doc->delete();
                $doc->user->delete();
            }
        }

        return $next($request);
    }
}
