<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Http\Resources\UserResource;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return UserResource::collection(User::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        $user = User::create($request->validated());
        return new UserResource($user);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);

        if ($user) {
            return new UserResource($user);
        }

        return response()->json([
            'code'    => 404,
            'success' => false,
            'message' => 'validation errors',
            'errors'  => ['user' => 'User not found']
        ], 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'code'    => 404,
                'success' => false,
                'message' => 'validation errors',
                'errors'  => ['user' => 'User not found']
            ], 404);
        }

        $user->update($request->validated());
        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'code'    => 404,
                'success' => false,
                'message' => 'validation errors',
                'errors'  => ['user' => 'User not found']
            ], 404);
        }

        $user->delete();
        return new UserResource($user);

        /*return response()->json([
            'code'    => 200,
            'success' => true,
            'message' => 'User deleted successfully'
        ]);*/
    }
}
