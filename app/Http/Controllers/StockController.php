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

    /**
     * Reserve stock for an order.
     * @param  \App\Http\Requests\StockRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function reserve(StockRequest $request)
    {
        $storeId = $request->input('store');
        $productId = $request->input('product');
        $quantityToReserve = $request->input('reserved_quantity');

        if ($quantityToReserve <= 0) {
            return ApiResponse::badRequest('The quantity to reserve must be greater than zero',['reserved_quantity' => $quantityToReserve]);
        }

        $stock = Stock::where('product', $productId)
            ->where('store', $storeId)
            ->first();

        if (!$stock) {
            return ApiResponse::notFound('stock not found for the given product and store','stock');
        }

        if ($stock->available_quantity < $quantityToReserve) {
            return ApiResponse::badRequest('insufficient available stock to reserve the requested quantity',['available_quantity' => $stock->available_quantity, 'requested_quantity' => $quantityToReserve]);
        }

        $stock->available_quantity -= $quantityToReserve;
        $stock->reserved_quantity += $quantityToReserve;
        $stock->total_quantity = $stock->available_quantity + $stock->reserved_quantity;
        $stock->save();

        return new StockResource($stock);
    }

    /**
     * Release reserved stock back to available stock.
     * @param  \App\Http\Requests\StockRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function release(StockRequest $request)
    {
        $storeId = $request->input('store');
        $productId = $request->input('product');
        $quantityToRelease = $request->input('available_quantity');

        if ($quantityToRelease <= 0) {
            return ApiResponse::badRequest('The quantity to release must be greater than zero',['available_quantity' => $quantityToRelease]);
        }

        $stock = Stock::where('product', $productId)
            ->where('store', $storeId)
            ->first();

        if (!$stock) {
            return ApiResponse::notFound('stock not found for the given product and store','stock');
        }

        if ($stock->reserved_quantity < $quantityToRelease) {
            return ApiResponse::badRequest('insufficient reserved stock to release the requested quantity',['reserved_quantity' => $stock->reserved_quantity, 'available_quantity' => $quantityToRelease]);
        }

        $stock->available_quantity += $quantityToRelease;
        $stock->reserved_quantity -= $quantityToRelease;
        $stock->total_quantity = $stock->available_quantity + $stock->reserved_quantity;
        $stock->save();

        return new StockResource($stock);
    }

    /**
     * Sell available stock.
     * @param  \App\Http\Requests\StockRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function sell(StockRequest $request)
    {
        $storeId = $request->input('store');
        $productId = $request->input('product');
        $quantityToSell = $request->input('quantity_ToSell');

        if ($quantityToSell <= 0) {
            return ApiResponse::badRequest('The quantity to sell must be greater than zero',['quantity_ToSell' => $quantityToSell]);
        }

        $stock = Stock::where('product', $productId)
            ->where('store', $storeId)
            ->first();

        if (!$stock) {
            return ApiResponse::notFound('stock not found for the given product and store','stock');
        }

        if ($stock->available_quantity < $quantityToSell) {
            return ApiResponse::badRequest('insufficient available stock to sell the requested quantity',['available_quantity' => $stock->available_quantity, 'quantity_ToSell' => $quantityToSell]);
        }

        $stock->available_quantity -= $quantityToSell;
        $stock->sold_quantity += $quantityToSell;
        $stock->total_quantity = $stock->available_quantity + $stock->reserved_quantity;
        $stock->save();

        return new StockResource($stock);
    }

    /**
     * Refund sold stock back to available stock.
     * @param  \App\Http\Requests\StockRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function refund(StockRequest $request)
    {
        $storeId = $request->input('store');
        $productId = $request->input('product');
        $quantityToRefund = $request->input('quantity_ToRefund');

        if ($quantityToRefund <= 0) {
            return ApiResponse::badRequest('The quantity to refund must be greater than zero',['quantity_ToRefund' => $quantityToRefund]);
        }

        $stock = Stock::where('product', $productId)
            ->where('store', $storeId)
            ->first();

        if (!$stock) {
            return ApiResponse::notFound('stock not found for the given product and store','stock');
        }

        if ($stock->sold_quantity < $quantityToRefund) {
            return ApiResponse::badRequest('insufficient sold stock to refund the requested quantity',['sold_quantity' => $stock->sold_quantity, 'quantity_ToRefund' => $quantityToRefund]);
        }

        $stock->available_quantity += $quantityToRefund;
        $stock->sold_quantity -= $quantityToRefund;
        $stock->total_quantity = $stock->available_quantity + $stock->reserved_quantity;
        $stock->save();

        return new StockResource($stock);
    }

    /**
     * Purchase stock to increase available stock.
     * @param  \App\Http\Requests\StockRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function purchase(StockRequest $request)
    {
        $storeId = $request->input('store');
        $productId = $request->input('product');
        $quantityToPurchase = $request->input('quantity_ToPurchase');

        if ($quantityToPurchase <= 0) {
            return ApiResponse::badRequest('The quantity to purchase must be greater than zero',['quantity_ToPurchase' => $quantityToPurchase]);
        }

        $stock = Stock::where('product', $productId)
            ->where('store', $storeId)
            ->first();

        if (!$stock) {
            return ApiResponse::notFound('stock not found for the given product and store','stock');
        }

        $stock->available_quantity += $quantityToPurchase;
        $stock->total_quantity = $stock->available_quantity + $stock->reserved_quantity;
        $stock->save();

        return new StockResource($stock);
    }
}
