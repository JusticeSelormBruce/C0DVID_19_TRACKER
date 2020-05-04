<?php

namespace App\Http\Controllers;

use App\Role;
use App\Route;
use App\Routes;
use App\User;
use App\UserRoles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class MainController extends Controller
{


    public function dashboard()
    {
        $value = Auth::user();
        if ($value->user_role_id != null) {
            $role_ids = json_decode('[' . Auth::user()->userroles->role_id . ']', true);
            for ($x = 0; $x <= sizeof($role_ids[0]) - 1; $x++) {
                $links[] = Route::whereId($role_ids[0][$x])->first();
            }
            if (sizeof($role_ids[0]) == null) {
                $rolessize = 0;
                Session::put('admin', $rolessize);
            } else {
                $rolessize = sizeof($role_ids[0]);


                Session::put('admin', $rolessize);

            }
        } else {
            $links = null;
        }
        Session::put('routes', $links);
        return view('dashboard');
    }
    public function AssignPrivilegeIndex()
    {

        return view('admin.privilege.index');
    }

    public function AssignPrivilegeForm()
    {

        $privileges = Route::all();
        $users = User::get()->all();
        if (Session::get('user_id') == null) {
            $data = null;
            $userRoles = null;

        } else {
            $data = $this->getSelectedRolesLogic();
            $userRoles = $data[0];

        }
        $me = Session::get('id');
        return view('admin.privilege.form', compact('privileges', 'users', 'data', 'me', 'userRoles'));
    }
    public function getUserRoles(Request $request)
    {
        Session::put('id', $request->user_id);
        $result = Role::where('user_id', $request->user_id)->first();
        Session::put('user_id', $result);
        return back();
    }
    public function getSelectedRolesLogic()
    {
        $data = Session::get('user_id');
        $data = json_decode('[' . $data->role_id . ']', true);
        return $data;
    }

    public function AssignPrivilege(Request $request)
    {
        $user_id = Session::get('id');
        $user_role_exist = Role::where('user_id', $user_id)->get()->first();
        if ($user_role_exist == null) {
            $data = implode(',', $request->role_id);
            $value = "[" . "" . $data . "" . "]";
            $result = Role::create(['user_id' => $user_id, 'role_id' => $value]);
            $this->setUserRole($user_id, $result->id);
        } else {

            Role::whereId($user_role_exist->id)->update(['role_id' => $request->role_id]);
        }

        return back()->with('msg', 'Privileges granted  to user successfully');

    }

    public function setUserRole($user_id, $user_role_id)
    {
        User::whereId($user_id)->update(['user_role_id' => $user_role_id]);
    }
    public function UserAccountsIndex()
    {
        $users = User::all();
        return view('admin.user_account.index', compact('users'));
    }

    public function RegisterUser(Request $request)
    {

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        return back()->with('msg', 'User Account Created successfully');
    }


    public function resetPasswordIndex(Request $request)
    {
        $users = User::all();
        return view('admin.reset_password', compact('users'));
    }

    public function resetPassword(Request $request)
    {
        User::whereId($request->user_id)->update(['password' => Hash::make('password')]);
        return back()->with('msg', 'User Password Reset Successfully');
    }

    public function changePasswordIndex()
    {
        return view('change_password');
    }

    public function changePassword(Request $request)
    {
        User::whereId(Auth::id())->update(['password' => Hash::make($request->password)]);
        return back()->with('msg', 'User Password Changed Successfully');
    }

}


