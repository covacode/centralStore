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
            'quantity' => 'required|integer|min:0'
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
            'quantity.required' => 'The quantity field is required.',
            'quantity.integer' => 'The quantity field must be an integer.',
            'quantity.min' => 'The quantity must be at least 0.'
        ];
    }
}
