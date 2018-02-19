<?php

use Illuminate\Database\Seeder;

class SentinelUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->delete();
        DB::table('users')->truncate();

        $credentials = [
            'email'    => 'admin@super.com',
            'password' => 'password',
        ];

        $user = Sentinel::registerAndActivate($credentials);

        // update additional field
        $user->is_customer = false;
        $user->is_active = true;
        $user->save();
    }

}
