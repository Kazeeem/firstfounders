<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
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

        $accessToken = Auth::user()->createToken('authToken')->accessToken;
        return response(['user' => Auth::user(), 'access_token' => $accessToken]);
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
        $validateData['password'] = Hash::make($request->password);

        $user = User::create($validateData);

        $accessToken = $user->createToken('authToken')->accessToken;
        return response([
            'user' => $user, 
            'message' => 'You have signed up successfully.',
            'access_token' => $accessToken
        ]);
    }
}
