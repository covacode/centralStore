<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Helpers\ApiResponse;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Activitylog\Models\Activity;

use App\Jobs\ProcessStockEvent;

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
            return ApiResponse::unauthorized('invalid credentials');
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

    /**
     *
     * fetch activity log
     *
     * @params \Illuminate\Http\Request $request
     * @return \Illumninate\Http\Response
     */
    public function audit(Request $request)
    {
        $limit = $request->input('limit');
        $logs = Activity::latest()->take($limit)->get();
        return ApiResponse::success('activity log', $logs);
    }

    /**
     *
     * fetch activity log detail
     *
     * @params \Illuminate\Http\Request $request
     * @return \Illumninate\Http\Response
     */
    public function auditDetail(Request $request)
    {
        $request->validate([
            'log_name'=>'required|string',
            'subject_id'=>'required|integer',
            'limit'=>'integer'
        ]);

        $class = 'App\\Models\\'.ucfirst($request->log_name);

        $log = Activity::latest()->where('log_name',$class)
            ->where('subject_id',$request->subject_id)
            ->take($request->limit)->get();

        if (!$log) {
            return ApiResponse::notFound('activity log not found');
        }

        return ApiResponse::success('activity log detail', $log);
    }
}
