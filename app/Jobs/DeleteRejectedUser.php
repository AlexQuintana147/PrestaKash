<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteRejectedUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $userId) {}

    public function handle(): void
    {
        $user = User::find($this->userId);

        // Solo eliminar si sigue rechazado
        if ($user && $user->documents?->status === 'rejected') {
            $doc = $user->documents;

            // Eliminar archivos del storage
            foreach (['selfie', 'dni_front', 'dni_back', 'utility_bill'] as $field) {
                if ($doc->$field) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($doc->$field);
                }
            }

            // Eliminar la carpeta del usuario si quedó vacía
            \Illuminate\Support\Facades\Storage::disk('public')->deleteDirectory('documents/' . $user->id);

            $doc->delete();
            $user->delete();
        }
    }
}
