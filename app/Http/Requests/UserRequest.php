<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRequest extends FormRequest
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
        $user = $this->route('user');

        $emailRule = 'required|string|email|max:255|unique:users,email';
        if ($this->method() === 'PUT' || $this->method() === 'PATCH') {
            $emailRule .= ',' . $user;
        }

        return [
            'name' => 'required|string|max:100',
            'email' => $emailRule,
            'password' => 'required|string|min:8',
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
            'email.required' => 'email is required',
            'email.unique' => 'email must be unique',
            'password.required' => 'password is required',
            'password.min' => 'password must be at least 8 characters',
        ];
    }
}
