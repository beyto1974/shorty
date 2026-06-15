<?php

namespace Database\Factories;

use App\Helpers\HandleHelper;
use App\Models\Shortener;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Shortener>
 */
class ShortenerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'original_url' => fake()->url(),
            'handle' => HandleHelper::getNewHandle(),
            'created_by_user_id' => User::orderBy('id')->first()->id,
        ];
    }
}
