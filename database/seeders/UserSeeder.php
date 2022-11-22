<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->count(1)
            ->create()
            ->each(
                function ($user) {
                    $user->assignRole('super-admin');
                }
            );
        User::factory()->count(2)
            ->create()
            ->each(
                function ($user) {
                    $user->assignRole('system-admin');
                }
            );
        User::factory()->count(4)
            ->create()
            ->each(
                function ($user) {
                    $user->assignRole('store-owner');
                }
            );
        User::factory()->count(6)
            ->create()
            ->each(
                function ($user) {
                    $user->assignRole('store-admin');
                }
            );
        User::factory()->count(10)
            ->create()
            ->each(
                function ($user) {
                    $user->assignRole('customer');
                }
            );
    }
}
