<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Auth\LoginRequest;
use App\Http\Requests\v1\Auth\LogoutRequest;
use App\Http\Requests\v1\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Token;

class AuthController extends Controller
{
    /**
     * Return an access token for a User.
     *
     * @return JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        if (!Auth::attempt($data)) {
            return response()->json(['message' => 'Invalid login credentials.'], 401);
        }

        $accessToken = Auth::user()->createToken('authToken')->accessToken;

        return response()->json(['user' => Auth::user(), 'access_token' => $accessToken]);
    }

    /**
     * Revoke token for User who logged out.
     *
     * @return JsonResponse
     */
    public function logout(LogoutRequest $request)
    {
        $token = $request->user()->token();
        $token->revoke();

        return response()->json(['message' => 'You have been successfully logged out!']);
    }

    /**
     * Return an access token for a User
     *
     * @return JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        $access_token = $user->createToken('authToken')->accessToken;

        return response()->json(['user' => $user->fresh(), 'access_token' => $access_token]);
    }
}
