<?php

namespace App\Http\Resources;

use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'store' => new StoreResource(Store::find($this->store)),
            'product' => new ProductResource(Product::find($this->product)),
            'quantity' => $this->quantity
        ];
    }
}
