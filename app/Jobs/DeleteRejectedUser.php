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
            $user->documents()->delete();
            $user->delete();
        }
    }
}
