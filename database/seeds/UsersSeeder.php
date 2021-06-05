<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use Carbon\Carbon;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $consumer = User::create([
            'name' => 'Consumer User',
            'email' => 'consumer@test.com',
            'email_verified_at' => Carbon::now(),
            'password' => bcrypt('password')
        ]);

        $consumer->assignRole('consumer');

        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'email_verified_at' => Carbon::now(),
            'password' => bcrypt('d0asnas08s43')
        ]);

        $admin->syncRoles('admin');
    }
}
