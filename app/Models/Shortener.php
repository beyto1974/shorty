<?php

namespace App\Models;

use App\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $original_url
 * @property string $handle
 * @property int $hits
 * @property int $created_by_user_id
 */
class Shortener extends Model
{
    use CrudTrait;

    /** @use HasFactory<\Database\Factories\ShortenerFactory> */
    use HasFactory;

    protected $fillable = [
        'original_url',
        'handle',
        'created_by_user_id',
    ];

    public function getRedirectUrl(): string
    {
        return route('shortener', [
            'shortener' => $this,
        ]);
    }

    public function getRedirectUrlAttribute(): string
    {
        return $this->getRedirectUrl();
    }
}
