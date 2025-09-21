<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Helpers\ApiResponse;


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

        if (!$user) {
            return ApiResponse::notFound('user');
        }

        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return ApiResponse::notFound('user');
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
            return ApiResponse::notFound('user');
        }

        $user->delete();
        return new UserResource($user);
    }

    public function restore(string $id)
    {
        $user = User::withTrashed()->find($id);

        if (!$user) {
            return ApiResponse::notFound('user');
        }

        if ($user->trashed()) {
            $user->restore();
        } else {
            return ApiResponse::badRequest('bad request', ['user' => 'user is not deleted']);
        }

        return new UserResource($user);
    }
}
