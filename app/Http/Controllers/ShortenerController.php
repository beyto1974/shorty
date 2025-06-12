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
        $shortener->append('redirect_url');

        return $shortener;
    }

    public function delete(Shortener $shortener)
    {
        ShortenerDeleteAction::run($shortener, auth()->user());
    }

    public function search()
    {
        $query = Shortener::where('created_by_user_id', auth()->user()->id);

        when(request()->string('search'), fn (string $search) => $query->where(function ($q) use ($search) {
            $q
                ->where('original_url', 'ILIKE', "%$search%")
                ->orWhere('handle', 'ILIKE', "%$search%");
        }));

        return $query->paginate(
            perPage: request()->integer('per_page', 15),
            page: request()->integer('page', 1),
        );
    }

    public function redirect(Shortener $shortener)
    {
        ShortenerHitAction::run($shortener);

        return redirect($shortener->original_url);
    }
}
