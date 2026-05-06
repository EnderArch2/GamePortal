<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function indexAdmin()
    {
        $admin = Admin::all();

        return response()->json([
            'totalElements' => $admin->count(),
            'content' => $admin,
        ]);
    }

    public function index()
    {
        $user = User::all();

        return response()->json([
            'totalElements' => $user->count(),
            'content' => $user,
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users,username|min:4|max:60',
            'password' => 'required|min:5|max:10',
        ]);

        if ($validator->fails()) {
            if ($validator->errors()->has('username')) {
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

        return response()->json([
            'status' => 'success',
            'username' => $request->username,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 'not-found',
                'message' => 'User Not found',
            ], 404);
        }

        if ($request->has('username') && $request->username !== null) {
            $user->username = $request->username;
        }

        if ($request->has('password') && $request->password !== null) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'status' => 'success',
            'username' => $user->username,
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 'not-found',
                'message' => 'User Not found',
            ], 404);
        }

        try {
            \DB::table('games')->where('created_by', $id)->delete();
            $user->delete();
            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Could not delete user due to other dependencies.'
            ], 500);
        }
    }
}
