<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Role;
use App\UserRole;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * User Login
     */
    public function userLogin(Request $request)
    {
        // validate credentials
        $loginCredentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        // Attempt login
        if (!Auth::attempt($loginCredentials)) {
            return response(['error' => 'Invalid login credentials', 'status' => 401]);
        }

        $user = Auth::user();

        // Add user role as scope
        $userRole = $user->roles()->first();

        /*$accessToken = Auth::user()->createToken('authToken')->accessToken;
        return response([
            'user' => $user,  
            'user_role' => $userRole->name,
            'access_token' => $accessToken
        ]);*/

        // token based on user role/scope
        $token = $user->createToken('authToken', [
            $userRole->name
        ]);

        return response()->json([
            'token' => $token,
            'user_details' => $user
        ]);
    }

    /**
     * Register New User
     */
    public function registerUser(Request $request)
    {
        $validateData = $request->validate([
            'name' => 'required',
			'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);

        $validateData['referral_id'] = $this->generateRandomStrings();
        $validateData['password'] = Hash::make($request->password);

        $user = User::create($validateData);

        // assign the role of the user === 'customer', 'admin'
        if (!$this->assignRole('customer', $user->id)) {
            return response()->json(['message' => 'Could not assign a role for this user']);
        }

        // Add user role as scope fot the token
        $userRole = $user->roles()->first();

        $accessToken = $user->createToken('authToken', [
            $userRole->name
        ]);
        return response([
            'user' => $user, 
            'message' => 'You have signed up successfully.',
            'access_token' => $accessToken
        ]);
    }

    public function index()
    {
        $user = User::all();
        return response()->json([
            'users' => $user
        ]);
    }

    public function show($id)
    {
        $user = User::find($id);
        return response()->json([
            'users' => $user
        ]);
    }

    public function generateRandomStrings() {
        $length = 2; 
        $char = 'abcdefghijklmnopqrstuvwxyz';
        $counts = mb_strlen($char);
        for ($b=0, $text=""; $b<$length; $b++) {
            $indez = rand(0, $counts - 1);
            $text .= mb_substr($char, $indez, 1);
        }

        for ($i=0, $text2=""; $i<$length; $i++) {
            $index = rand(0, $counts - 1);
            $text2 .= mb_substr($char, $index, 1);
        }

        $int = mt_rand(100000,999999);
        return $text.$int.$text2;
    }

    public function createRole(Request $request)
    {
        $newRole = new Role;
        $newRole->name = $request->input('role');
        $newRole->save();

        return response()->json(['message' => 'Role created successfully']);
    }

    public function giveRole($role, $id)
    {
        if ($this->assignRole($role, $id)) {
            return response()->json(['message' => 'Role assigned successfully']);
        }
        else {
            return response()->json(['message' => 'Invalid role name passed']);
        }
    }

    public function assignRole($roleName, $user_id)
    {
        $role = Role::where(['name' => $roleName])->first();        

        if (is_null($role)) {
            return false;
        } 
        else {
            $assignRole = new UserRole;
            $assignRole->user_id = $user_id;
            $assignRole->role_id = $role->id;
            $assignRole->save();
            return true;
        }
    }

    public function checkRole($id)
    {
        $user = User::find($id);

        foreach ($user->roles as $role) {
            echo response()->json(['userRole' => $role->name]);
        }
    }
}
