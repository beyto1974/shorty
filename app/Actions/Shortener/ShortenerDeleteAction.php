<?php

namespace App\Actions\Shortener;

use App\Models\Shortener;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ShortenerDeleteAction
{
    public static function run(Shortener $shortener, User $requestBy): void
    {
        $shortener->delete();

        Log::info('Shortener deleted', [
            'request_by' => $requestBy->getLogPayload(),
            'model' => $shortener->toArray(),
        ]);
    }
}
