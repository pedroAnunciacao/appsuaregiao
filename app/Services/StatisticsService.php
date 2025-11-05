<?php

namespace App\Services;

use App\Models\User;
use App\Models\Post;
use App\Models\BannedWord;
use Carbon\Carbon;

class StatisticsService
{
    public function getDashboardData()
    {
        return [
            'total_users' => User::count(),
            'banned_users' => User::where('is_banned', true)->count(),
            'recent_bans' => User::where('is_banned', true)
                                 ->where('updated_at', '>=', Carbon::now()->subDays(7))
                                 ->count(),
            'total_posts' => Post::count(),
            'banned_words' => BannedWord::count(),
            'posts_banned' => Post::where('status', 'blocked')->count(),
            'posts_banned_recently' => Post::where('status', 'blocked')
                                           ->where('updated_at', '>=', Carbon::now()->subDays(7))
                                           ->count(),
        ];
    }
}
