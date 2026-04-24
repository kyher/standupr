<?php

namespace App\Http\Requests\Standups;

use Illuminate\Foundation\Http\FormRequest;

class StoreStandupNoteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:10000'],
        ];
    }
}
