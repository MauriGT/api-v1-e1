<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use function auth;
use function response;

class PassportAuthController extends Controller
{
    /**
     * @throws ValidationException
     */
    public function register(Request $request): \Illuminate\Http\JsonResponse
    {
        $validateData = $request->validate([
            'name' => 'required|min:4',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        $validateData['password'] = Hash::make($request->password);
        $user = User::create($validateData);

        if ($user) {
            $accessToken = $user->createToken('authToken')->accessToken;
            return response()->json([
                "success" => true,
                "message" => "User created successfully.",
                'user' => $user,
                'accessToken' => $accessToken
            ],
                201);
        }
        return response()->json(['message' => 'Error to create user.'], 500);
    }

    /**
     * Login Req
     */
    public function login(Request $request)
    {
        $loginData = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if (!auth()->attempt($loginData)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $accessToken = auth()->user()->createToken('authToken')->accessToken;

        return response([
                'accessToken' => $accessToken
            ]
        );
    }

    public function userInfo()
    {
        $user = auth()->user();
        return response(['user' => $user]);
    }

}
