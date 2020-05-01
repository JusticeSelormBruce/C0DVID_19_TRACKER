<?php

namespace App\Http\Controllers;

use App\Emergency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class EmergencyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Emergency::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $user_emergency_contact =  $this->ValidateEmergencyContactDetails();
        return  Emergency::create($user_emergency_contact);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return   Emergency::find($id);
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
        $user_emergency_contact =  $this->ValidateEmergencyContactDetails();
        return  Emergency::whereId($id)->update($user_emergency_contact);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      return   Emergency::whereId($id)->delete();
    }

    public function ValidateEmergencyContactDetails()
    {
        return request()->validate([
            'relationship' => 'required|string',
            'name' => 'required|string',
            'phone' => 'required|string',
            'phone1' => '',
            'user_id' => 'required|numeric'
        ]);
    }
}
