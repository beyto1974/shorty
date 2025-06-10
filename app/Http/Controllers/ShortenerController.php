<?php

namespace App\Http\Controllers;

use App\Actions\Shortener\ShortenerCreateAction;
use App\Actions\Shortener\ShortenerDeleteAction;
use App\Actions\Shortener\ShortenerHitAction;
use App\Models\Shortener;

class ShortenerController extends Controller
{
    public function put()
    {
        $post = request()->validate([
            'original_url' => 'required|url',
        ]);

        return ShortenerCreateAction::run($post, auth()->user());
    }

    public function get(Shortener $shortener)
    {
        return $shortener;
    }

    public function delete(Shortener $shortener)
    {
        ShortenerDeleteAction::run($shortener, auth()->user());
    }

    public function redirect(Shortener $shortener)
    {
        ShortenerHitAction::run($shortener);

        return redirect($shortener->original_url);
    }
}
