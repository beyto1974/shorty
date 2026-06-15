<?php

namespace App\Http\Controllers;

use App\Actions\Shortener\ShortenerCreateAction;
use App\Actions\Shortener\ShortenerDeleteAction;
use App\Actions\Shortener\ShortenerHitAction;
use App\Models\Shortener;
use Illuminate\Support\Facades\Config;

class ShortenerController extends Controller
{
    public function put()
    {
        $post = request()->validate([
            'original_url' => 'required|url',
        ]);

        return ShortenerCreateAction::run($post, auth()->user())->append('redirect_url');
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

        $results = $query->paginate(
            perPage: request()->integer('per_page', 15),
            page: request()->integer('page', 1),
        );

        $results->getCollection()->transform(function ($item) {
            $item->setAppends(['redirect_url']);

            return $item;
        });

        return $results;
    }

    public function redirect(Shortener $shortener)
    {
        // Validate UTM parameters from request
        request()->validate([
            'utm_source' => 'nullable|string|max:256',
            'utm_medium' => 'nullable|string|max:256',
            'utm_campaign' => 'nullable|string|max:256',
            'utm_content' => 'nullable|string|max:256',
            'utm_term' => 'nullable|string|max:256',
        ]);

        ShortenerHitAction::run($shortener);

        $url = $shortener->original_url;

        if (Config::boolean('app.url_tracking')) {
            // Build tracking parameters
            $params = [];

            // Add referer from current request
            $referer = request()->header('Referer');
            if ($referer) {
                // Sanitize and limit referer length
                $referer = $this->sanitizeParam($referer);
                if (strlen($referer) > 512) {
                    $referer = substr($referer, 0, 512);
                }
                $params['referer'] = $referer;
            }

            // Add UTM parameters from current request (already validated above, then sanitized)
            $utmSource = request()->query('utm_source');
            $utmMedium = request()->query('utm_medium');
            $utmCampaign = request()->query('utm_campaign');
            $utmContent = request()->query('utm_content');
            $utmTerm = request()->query('utm_term');

            if ($utmSource) {
                $params['utm_source'] = $this->sanitizeParam($utmSource);
            }
            if ($utmMedium) {
                $params['utm_medium'] = $this->sanitizeParam($utmMedium);
            }
            if ($utmCampaign) {
                $params['utm_campaign'] = $this->sanitizeParam($utmCampaign);
            }
            if ($utmContent) {
                $params['utm_content'] = $this->sanitizeParam($utmContent);
            }
            if ($utmTerm) {
                $params['utm_term'] = $this->sanitizeParam($utmTerm);
            }

            // Append parameters to URL if any exist
            if (! empty($params)) {
                $separator = str_contains($url, '?') ? '&' : '?';
                $query = http_build_query($params);
                $url = $url . $separator . $query;
            }
        }

        return redirect($url);
    }

    /**
     * Sanitize a parameter value to prevent XSS and injection attacks.
     */
    private function sanitizeParam(string $value): string
    {
        // Remove HTML tags
        $value = strip_tags($value);

        // Escape HTML special characters to prevent XSS
        $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

        // Trim whitespace
        $value = trim($value);

        return $value;
    }
}
