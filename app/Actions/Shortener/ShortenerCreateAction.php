<?php

namespace App\Actions\Shortener;

use App\Helpers\HandleHelper;
use App\Models\Shortener;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ShortenerCreateAction
{
    public static function run(array $post, User $requestBy): Shortener
    {
        $shortener = new Shortener([
            ...$post,
            'created_by_user_id' => $requestBy->id,
        ]);

        $shortener->handle = HandleHelper::getNewHandle();

        $shortener->save();

        Log::info('Shortener created', [
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
