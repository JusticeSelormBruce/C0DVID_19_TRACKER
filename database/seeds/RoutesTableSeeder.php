<?php

use Illuminate\Database\Seeder;
use App\Route;
class RoutesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Route::create([
            'name' => "Reset Password",
            'route' => '/admin/reset-password'
        ]);
        Route::create([
            'name' => "Change  Password",
            'route' => '/change-password-index'
        ]);
        Route::create([
            'name' => "Privileges",
            'route' => '/admin/assign-privilege-index'
        ]);
        Route::create([
            'name' => "Create User Account",
            'route' => '/admin/user-accounts-index'
        ]);
    }
}
