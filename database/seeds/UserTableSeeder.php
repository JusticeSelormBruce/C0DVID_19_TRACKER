<?php

use Illuminate\Database\Seeder;
use App\User;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       User::create([
           'name'=>"Justice Selorm Bruce",
           'username'=>"tracker_admin@gmail.com",
           'password'=> Hash::make("password"),
           'phone'=>"0248284049",
           'phone1'=>"",
           'gender'=>"Male",
           'domicile'=>"Koforidua (New Juabean)",
           'region_id'=>1
       ]);
    }
}
