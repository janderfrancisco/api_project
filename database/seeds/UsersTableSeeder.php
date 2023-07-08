<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name'                  => 'user',
            'username'              => 'user',
            'email'                 => 'user@email.com',
            'email_verified_at'     => now(),
            'active'                => '1',
            'password'              => bcrypt('123456')
        ]);

    }
}
