<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Helpers\HandleHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getLogPayload(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
        ];
    }

    public function getStats(): array
    {
        $byUserQuery = Shortener::where('created_by_user_id', $this->id);

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
