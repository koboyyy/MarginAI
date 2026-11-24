<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    /**
     * Create the local demo owner and business.
     */
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        $user = User::query()->updateOrCreate(
            ['email' => config('demo.account.email')],
            [
                'name' => config('demo.account.name'),
                'password' => config('demo.account.password'),
            ],
        );
        $user->forceFill(['email_verified_at' => now()])->save();
        $user->business()->updateOrCreate([], [
            'name' => config('demo.business.name'),
            'category' => 'kuliner',
            'pricing_method' => 'margin',
            'default_margin_percent' => '30.00',
        ]);
    }
}
