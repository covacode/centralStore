<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseFormRequest;

class StoreRequest extends BaseFormRequest
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

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'name.required' => 'name is required',
            'name.unique' => 'name must be unique',
            'name.max' => 'name must not exceed 100 characters',
            'name.string' => 'name must be a string',
            'user.integer' => 'user must be an integer',
            'user.required' => 'user is required',
            'user.exists' => 'user must exist',
        ];
    }
}
