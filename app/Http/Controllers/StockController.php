<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\StockRequest;
use App\Http\Resources\StockResource;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Store;

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
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function byStore(string $id)
    {
        $store = Store::find($id);

        if (!$store) {
            return ApiResponse::notFound('resource not found','store');
        }

        $stocks = Stock::where('store', $id)->get();

        if ($stocks->isEmpty()) {
            return ApiResponse::notFound('This store has no associated stock','stocks');
        }

        return StockResource::collection($stocks);
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function byProduct(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return ApiResponse::notFound('resource not found','product');
        }

        $stocks = Stock::where('product', $id)->get();

        if ($stocks->isEmpty()) {
            return ApiResponse::notFound('This product has no associated stock','stocks');
        }

        return StockResource::collection($stocks);
    }
}
