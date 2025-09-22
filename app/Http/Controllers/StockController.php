<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\StockRequest;
use App\Http\Resources\StockResource;
use App\Models\Stock;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return StockResource::collection(Stock::all());
    }

    /**
     * Store a newly created resource in storage.
     * @param  \App\Http\Requests\StockRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StockRequest $request)
    {
        $stock = Stock::create($request->validated());
        return ApiResponse::success('success', new StockResource($stock));
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(string $id)
    {
        $stock = Stock::find($id);

        if (!$stock) {
            return ApiResponse::notFound('resource not found','stock');
        }

        return new StockResource($stock);
    }

    /**
     * Update the specified resource in storage.
     * @param  \App\Http\Requests\StockRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StockRequest $request, string $id)
    {
        $stock = Stock::find($id);

        if (!$stock) {
            return ApiResponse::notFound('resource not found','stock');
        }

        $stock->update($request->validated());
        return new StockResource($stock);
    }
}
