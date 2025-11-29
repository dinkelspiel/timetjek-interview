<?php

namespace Database\Factories;

use App\Models\Institution;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "external_ref" => Str::random(10),
            "institution_id" => fake()->randomElement(Institution::all())->id,
            "name" => fake()->randomElement([
                "Private account",
                "Savings account",
                "Checking account",
            ]),
            "account_type" => fake()->randomElement([
                "checking",
                "savings",
                "credit_card",
                "loan",
                "mortgage",
                "brokerage",
                "retirement",
                "crypto_wallet",
                "custody",
                "other",
            ]),
            "currency" => fake()->currencyCode(),
            "balance_minor" => fake()->randomNumber(7),
            "direction" => fake()->randomElement(["positive", "negative"]),
            "status" => fake()->randomElement(["open", "closed"]),
            "raw_json" => null,
            "opened_at" => fake()->date(),
            "closed_at" => null,
        ];
    }
}
