<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ProductResource::collection(Product::all());
    }

    /**
     * Store a newly created resource in storage.
     * @param  \App\Http\Requests\ProductRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        $product = Product::create($request->validated());
        return ApiResponse::success('success', new ProductResource($product));
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return ApiResponse::notFound('resource not found','product');
        }

        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     * @param  \App\Http\Requests\ProductRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return ApiResponse::notFound('resource not found','product');
        }

        $product->update($request->validated());
        return new ProductResource($product);
    }

    /**
     * Remove the specified resource from storage.
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return ApiResponse::notFound('resource not found','product');
        }

        $product->delete();
        return new ProductResource($product);
    }

    /**
     * Restore the specified resource from storage.
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore(string $id)
    {
        $product = Product::withTrashed()->find($id);

        if (!$product) {
            return ApiResponse::notFound('resource not found','product');
        }

        if ($product->trashed()) {
            $product->restore();
        } else {
            return ApiResponse::badRequest('bad request', ['product' => 'product is not deleted']);
        }

        return new ProductResource($product);
    }
}
