<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Institution;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserAccount;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            "name" => "Test User",
            "email" => "test@example.com",
        ]);

        $user = User::find(1);

        $institutions = Institution::factory(10)->create();

        $accounts = Account::factory(10)->create();

        foreach ($accounts as $account) {
            UserAccount::create([
                "user_id" => $user->id,
                "account_id" => $account->id,
            ]);
        }

        Transaction::factory(200)->create();
    }
}
