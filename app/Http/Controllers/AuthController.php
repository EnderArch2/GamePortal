<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users,username|min:4|max:60',
            'password' => 'required|min:5|max:10',
        ]);

        if($validator->fails()) {
            if($validator->errors()->has('username')) {
                return response()->json([
                    'status' => 'invalid',
                    'message' => 'Username already exists'
                ], 400);
            }
        }

        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('user_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|min:4|max:60',
            'password' => 'required|min:5',
        ]);

        $user = User::where('username', $request->username)->first();
        if(!$user) {
            $user = Admin::where('username', $request->username)->first();
        }

        if(!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'invalid',
                'message' => 'Wrong username or password'
            ], 401);
        }

        $role = ($user instanceof Admin) ? 'admin' : 'user';

        $token = $user->createToken('auth_token', [$role])->plainTextToken;

        return response()->json([
            'status' => 'success',
            'token' => $token,
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success'
        ]);
    }
}
