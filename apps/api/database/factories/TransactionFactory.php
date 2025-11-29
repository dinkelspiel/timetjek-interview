<?php

namespace Database\Factories;

use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $account = fake()->randomElement(Account::all());

        return [
            "account_id" => $account->id,
            "external_ref" => Str::random(10),
            "posted_at" => fake()->date(),
            "authorized_at" => null,
            "description" => fake()->text(),
            "amount_minor" => fake()->randomNumber(5),
            "currency" => $account->currency,
            "direction" => fake()->randomElement(["debit", "credit"]),
            "status" => "posted",
            "counterpart_id" => null,
            "raw_json" => null,
        ];
    }
}
