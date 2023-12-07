<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Auth;


class AuthController extends Controller
{
    public function signup(Request $request)
    {

        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed',
            'role_id' => 'required'
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'role_id' => $data['role_id']
        ]);

        $user->assignRole('user');

        $user->notify(new \App\Notifications\WelcomeMailNotification($user));

        $token = $user->createToken('apiToken')->plainTextToken;

        $res = [
            'user' => $user,
            'token' => $token
        ];

        return response($res, 201);

    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response([
                'msg' => 'incorrect username or password'
            ], 401);
        }

        Auth::login($user);

        $token = $user->createToken('apiToken')->plainTextToken;

        $res = [
            'user' => $user,
            'isAdmin'=> Auth::user()->hasRole('admin'),
            'token' => $token
        ];

        return response($res, 201);
    }

    public function logout(Request $request)
    {
        $request->session()->invalidate();
        Auth::logout();
        return ['message' => 'user logged out'];

    }

    public function me(Request $request){
        return response()->json([
            'data' => $request->user(),
        ]);
    }


}
