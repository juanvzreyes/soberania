<?php

namespace App\Http\Requests\Traits;

use Illuminate\Validation\Rule;

trait PhoneRules
{
    protected function phoneNumberRules(): array
    {
        return [
            'phones'                => ['nullable', 'array'],
            'phones.*.number'       => ['required', 'string', 'digits:10'],
            'phones.*.dial_code'    => ['nullable', 'string', 'max:10'],
            'phones.*.type'         => [
                'required',
                'string',
                Rule::in(['oficina', 'celular', 'casa', 'fax'])
            ],
        ];
    }

    protected function phoneNumberAttributes(): array
    {
        return [
            'phones'                => 'telefonos',
            'phones.*.number'       => 'número',
            'phones.*.dial_code'    => 'código de área',
            'phones.*.type'         => 'tipo',
        ];
    }
}
