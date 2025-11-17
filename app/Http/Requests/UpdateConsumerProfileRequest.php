<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\LocationRules;
use App\Http\Requests\Traits\PhoneRules;
use Illuminate\Foundation\Http\FormRequest;

class UpdateConsumerProfileRequest extends FormRequest
{
    use LocationRules, PhoneRules;
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
        return array_merge([
            'description' => 'nullable|string|max:500',
        ], $this->locationRules(), $this->phoneNumberRules());
    }

    public function attributes(): array
    {
        return array_merge([
            'description' => 'descripción',
        ], $this->locationAttributes(), $this->phoneNumberAttributes());
    }
}
