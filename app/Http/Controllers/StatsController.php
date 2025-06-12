<?php

namespace App\Http\Controllers;

use App\Helpers\HandleHelper;
use App\Models\Shortener;

class StatsController extends Controller
{
    public function getGlobalStats()
    {
        $globalUsedCount = Shortener::count();
        $totalCount = HandleHelper::getCombinationCount();

        return [
            'used' => $globalUsedCount,
            'free' => $totalCount - $globalUsedCount,
            'total' => $totalCount,
            'hits' => Shortener::sum('hits'),
        ];
    }

    public function getUserStats()
    {
        $byUserQuery = Shortener::where('created_by_user_id', auth()->user()->id);

        $globalUsedCount = Shortener::count();
        $userUsedCount = $byUserQuery->clone()->count();
        $totalCount = HandleHelper::getCombinationCount();

        return [
            'used' => $userUsedCount,
            'free' => $totalCount - $globalUsedCount,
            'total' => $totalCount,
            'hits' => $byUserQuery->clone()->sum('hits'),
        ];
    }
}
