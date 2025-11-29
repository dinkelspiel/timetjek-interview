<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Institution>
 */
class InstitutionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->name();
        $slug = Str::replace(" ", "-", $name);
        $slug = Str::lower($slug);
        return [
            "name" => $name,
            "slug" => $slug,
            "type" => fake()->randomElement([
                "bank",
                "card",
                "broker",
                "crypto_exchange",
                "wallet",
                "other",
            ]),
            "country_code" => fake()->countryCode(),
        ];
    }
}
