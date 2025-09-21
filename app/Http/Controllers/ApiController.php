<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Helpers\ApiResponse;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ApiController extends Controller
{
    /**
     *
     * API health check
     *
     * @return \Illuminate\Http\Response
     */
    public function healthCheck(){
        return ApiResponse::success('API is healthy');
    }

    /**
     *
     * user login
     *
     * @params \Illuminate\Http\Request $request
     * @return \Illumninate\Http\Response
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'=>'required|string|email',
            'password'=>'required|min:8'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return ApiResponse::unauthorized('Invalid credentials');
        }

        $user->tokens()->delete();
        $token = $user->createToken('my-app-token')->plainTextToken;

        $response = [
            'user' => new UserResource($user),
            'token' => $token
        ];

        return ApiResponse::success('login successful', $response);
    }

    /**
     *
     * user logout
     *
     * @params \Illuminate\Http\Request $request
     * @return \Illumninate\Http\Response
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();
        return ApiResponse::success('logged out');
    }
}
