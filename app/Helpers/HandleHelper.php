<?php

namespace App\Helpers;

use App\Models\Shortener;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PUGX\Shortid\Shortid;

class HandleHelper
{
    public static function getNewHandle(): string
    {
        $handle = null;

        $tries = 0;

        do {
            if ($handle !== null) {
                /**
                 * Set a larget value for app.handle_length if happens frequently.
                 */
                Log::warning("Could not get a new handle on try {$tries}");
            }

            $handle = Shortid::generate(
                length: Config::integer('app.handle_length'),
                alphabet: Config::get('app.handle_alphabet'),
            );

            $tries++;
        } while (Shortener::where('handle', $handle)->exists());

        return $handle;
    }

    /**
     * Ensures correct length.
     */
    public static function toAlphabet(string $alphabet): string
    {
        return Str::of($alphabet)
            ->substr(0, 64)
            ->padRight(64, $alphabet);
    }

    public static function getCombinationCount(?string $alphabet = null, ?int $length = null): int
    {
        $alphabet = self::toAlphabet(
            $alphabet ??
            Config::get('app.handle_alphabet') ??
            Shortid::generate()->getFactory()->getAlphabet()
        );
        $length ??= Config::integer('app.handle_length');

        return pow(
            count(array_unique(str_split($alphabet))),
            $length
        );
    }
}
