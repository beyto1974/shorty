<?php

namespace App\Helpers;

use App\Models\Shortener;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
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
}
