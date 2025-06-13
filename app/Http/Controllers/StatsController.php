<?php

namespace App\Http\Controllers;

use App\Helpers\HandleHelper;
use App\Models\Shortener;
use App\Models\User;

class StatsController extends Controller
{
    public function getGlobalStats()
    {
        $globalUsedCount = Shortener::count();
        $totalCount = HandleHelper::getCombinationCount();

        return [
            'global' => [
                'used' => $globalUsedCount,
                'free' => $totalCount - $globalUsedCount,
                'total' => $totalCount,
                'hits' => Shortener::sum('hits'),
            ],
            'users' => User::orderBy('name')->get()->map(fn (User $user) => [
                'user' => $user,
                'stats' => $user->getStats(),
            ]),
        ];
    }

    public function getUserStats()
    {
        return auth()->user()->getStats();
    }
}
