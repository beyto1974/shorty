<?php

namespace App\Actions\Shortener;

use App\Models\Shortener;
use Illuminate\Support\Facades\Log;

class ShortenerHitAction
{
    public static function run(Shortener $shortener): Shortener
    {
        $shortener->hits = $shortener->hits + 1;

        $shortener->update();

        Log::info('Shortener hit', [
            ...$shortener->only([
                'id',
                'handle',
                'original_url',
            ]),
            'ip' => request()->ip(),
        ]);

        return $shortener;
    }
}
