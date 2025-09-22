<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseFormRequest;

class ProductRequest extends BaseFormRequest
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
        $product = $this->route('product');

        $ean13Rule = 'required|string|max:13|min:13|unique:products,ean13';
        if ($this->method() === 'PUT' || $this->method() === 'PATCH') {
            $ean13Rule .= ',' . $product;
        }

        return [
            'name' => 'required|string|max:100',
            'ean13' => $ean13Rule,
            'description' => 'required|string|max:255',
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
            'name.required' => 'name is required',
            'name.string' => 'name must be a string',
            'name.max' => 'name must not exceed 100 characters',
            'ean13.required' => 'ean13 is required',
            'ean13.string' => 'ean13 must be a string',
            'ean13.max' => 'ean13 must be exactly 13 characters',
            'ean13.min' => 'ean13 must be exactly 13 characters',
            'ean13.unique' => 'ean13 must be unique',
            'description.required' => 'description is required',
            'description.string' => 'description must be a string',
            'description.max' => 'description must not exceed 255 characters',
        ];
    }
}
