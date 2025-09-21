<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreRequest extends FormRequest
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
        $store = $this->route('store');

        $nameRule = 'required|string|max:100|unique:stores,name';
        if ($this->method() === 'PUT' || $this->method() === 'PATCH') {
            $nameRule .= ',' . $store;
        }

        return [
            'name' => $nameRule,
            'user' => 'required|integer|exists:users,id'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'code'  => 422,
            'success' => false,
            'message' => 'validation errors',
            'errors' => $validator->errors()
        ], 422));
    }

    public function messages()
    {
        return [
            'name.required' => 'name is required',
            'name.unique' => 'name must be unique',
            'user.required' => 'user is required',
            'user.exists' => 'user must exist',
        ];
    }
}
