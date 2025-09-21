<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseFormRequest;

class UserRequest extends BaseFormRequest
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

        $emailRule = 'required|string|email|max:100|unique:users,email';
        if ($this->method() === 'PUT' || $this->method() === 'PATCH') {
            $emailRule .= ',' . $user;
        }

        return [
            'name' => 'required|string|max:100',
            'email' => $emailRule,
            'password' => 'required|string|min:8',
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
            'name.max' => 'name must not exceed 100 characters',
            'name.string' => 'name must be a string',
            'email.string' => 'email must be a string',
            'email.email' => 'email must be a valid email address',
            'email.max' => 'email must not exceed 100 characters',
            'email.required' => 'email is required',
            'email.unique' => 'email must be unique',
            'password.required' => 'password is required',
            'password.string' => 'password must be a string',
            'password.min' => 'password must be at least 8 characters',
        ];
    }
}
