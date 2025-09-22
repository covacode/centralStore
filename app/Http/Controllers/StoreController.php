<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\StoreRequest;
use App\Http\Resources\StoreResource;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return StoreResource::collection(Store::all());
    }

    /**
     * Store a newly created resource in storage.
     * @param  \App\Http\Requests\StoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $store = Store::create($request->validated());
        return ApiResponse::success('success', new StoreResource($store));
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(string $id)
    {
        $store = Store::find($id);

        if (!$store) {
            return ApiResponse::notFound('resource not found','store');
        }

        return new StoreResource($store);
    }

    /**
     * Update the specified resource in storage.
     * @param  \App\Http\Requests\StoreRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreRequest $request, string $id)
    {
        $store = Store::find($id);

        if (!$store) {
            return ApiResponse::notFound('resource not found','store');
        }

        $store->update($request->validated());
        return new StoreResource($store);
    }

    /**
     * Remove the specified resource from storage.
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $id)
    {
        $store = Store::find($id);

        if (!$store) {
            return ApiResponse::notFound('resource not found','store');
        }

        $store->delete();
        return new StoreResource($store);
    }

    /**
     * Restore the specified resource from storage.
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore(string $id)
    {
        $store = Store::withTrashed()->find($id);

        if (!$store) {
            return ApiResponse::notFound('resource not found','store');
        }

        if ($store->trashed()) {
            $store->restore();
        } else {
            return ApiResponse::badRequest('bad request', ['store' => 'store is not deleted']);
        }

        return new StoreResource($store);
    }
}
