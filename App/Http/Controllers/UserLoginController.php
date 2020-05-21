<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserLoginController extends Controller
{
    public function login(Request $request)
    {

        $data = $this->validateUserLoginDetails();
        $user =User::where('username', $data['username'])->get()->first();
        if (Hash::check($data['password'], $user['password'])) {
            return $user;
        } else {
            return "Invalid Username or Password";
        }
    }
    public function validateUserLoginDetails()
    {

        return request()->validate(
            [
                'username' => "required",
                'password' => "required"
            ]
        );
    }
}
