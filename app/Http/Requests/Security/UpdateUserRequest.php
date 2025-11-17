<?php

namespace App\Http\Requests\Security;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'name'              => 'nullable|max:255',
            'first_name'        => 'required|string|max:100',
            'last_name'         => 'required|string|max:100',
            'second_last_name'  => 'nullable|string|max:55',
            'email'             => 'required|email|max:255|unique:users,email,' . $this->id,
            'password'          => 'nullable|max:20',
            'roles'             => 'nullable|array',
            'roles.*'           => 'exists:roles,id',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'              => 'nombre',
            'first_name'        => 'primer nombre',
            'last_name'         => 'primer apellido',
            'second_last_name'  => 'segundo apellido',
            'email'             => 'correo',
            'password'          => 'contraseña',
            'roles'             => 'roles',
        ];
    }
}
