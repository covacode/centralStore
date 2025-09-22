<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'store' => 'required|integer|exists:stores,id',
            'product' => 'required|integer|exists:products,id',
            'combination' => 'unique:stocks,store,product',
            'available_quantity' => 'nullable|integer|min:0',
            'reserved_quantity' => 'nullable|integer|min:0',
            'quantity_ToSell' => 'nullable|integer|min:0',
            'quantity_ToRefund' => 'nullable|integer|min:0',
            'total_quantity' => 'nullable|integer|min:0'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'store.required' => 'The store field is required.',
            'store.integer' => 'The store field must be an integer.',
            'store.exists' => 'The selected store does not exist.',
            'product.required' => 'The product field is required.',
            'product.integer' => 'The product field must be an integer.',
            'product.exists' => 'The selected product does not exist.',
            'combination.unique' => 'The combination of store and product must be unique.',
            'available_quantity.integer' => 'The available quantity field must be an integer.',
            'available_quantity.min' => 'The available quantity must be at least 0.',
            'available_quantity.max' => 'The available quantity may not be greater than the total quantity.',
            'reserved_quantity.integer' => 'The reserved quantity field must be an integer.',
            'reserved_quantity.min' => 'The reserved quantity must be at least 0.',
            'reserver_quantity.max' => 'The reserved quantity may not be greater than the available quantity.',
            'quantity_ToSell.integer' => 'The quantity to sell field must be an integer.',
            'quantity_ToSell.min' => 'The quantity to sell must be at least 0.',
            'quantity_ToSell.max' => 'The quantity to sell may not be greater than the available quantity.',
            'quantity_ToRefund.integer' => 'The quantity to refund field must be an integer.',
            'quantity_ToRefund.min' => 'The quantity to refund must be at least 0.',
            'total_quantity.integer' => 'The total quantity field must be an integer.',
            'total_quantity.min' => 'The total quantity must be at least 0.',
            'total_quantity.max' => 'The total quantity may not be greater than the sum of available and reserved quantities.',
        ];
    }
}
