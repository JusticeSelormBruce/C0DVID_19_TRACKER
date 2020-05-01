<?php

namespace App\Http\Controllers;



use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return User::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user_details =  $this->ValidateUserDetails();
        $user_details['password'] =  Hash::make($user_details['password']);
        return    User::create($user_details);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return  User::whereId($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user_details =  $this->ValidateUserDetails();
        $user_details['password'] =  Hash::make($user_details['password']);
        return User::whereId($id)->update($user_details);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return   User::whereId($id)->delete();
    }

    public function ValidateUserDetails()
    {
        return request()->validate([
            'name' => 'required|string',
            'username' => 'required',
            'password' => 'required',
            'phone' => 'required|string',
            'phone1' => '',
            'gender' => 'required|string',
            'domicile' => 'required|required',
            'region_id' => 'required|numeric'
        ]);
    }
}
