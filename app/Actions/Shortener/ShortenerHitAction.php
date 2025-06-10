<?php

namespace App\Actions\Shortener;

use App\Models\Shortener;
use DB;
use Illuminate\Support\Facades\Log;

class ShortenerHitAction
{
    public static function run(Shortener $shortener): Shortener
    {
        DB::table('shorteners')->where('id', $shortener->id)->increment('hits');

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
