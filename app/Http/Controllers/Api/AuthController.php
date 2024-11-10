<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('user_username', 'password');

        $validator = Validator::make($credentials, [
            'user_username' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors());
        }

        $user = User::where('user_username', $credentials['user_username'])->first();

        if (!$user) {
            return $this->returnError('User not found', 404);
        }
        if (!Auth::attempt($credentials)) {
            return $this->returnError('Invalid credentials', 401);
        }
        $token = JWTAuth::fromUser($user);

        return $this->returnSuccess(['token' => $token], 'Login successful');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return $this->returnError($validator->errors());
        }
        $user = User::create([

            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        $token = Auth::login($user);
        return $this->returnSuccess(['user' => $user, 'token' => $token], 'Admin created successfully');
    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }

}
