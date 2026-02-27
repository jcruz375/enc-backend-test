<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'        => ['required', 'string', 'min:3'],
            'price'        => ['required', 'numeric', 'gt:0'],
            'description'  => ['required', 'string'],
            'category'     => ['required', 'string'],
            'image'        => ['nullable', 'url', 'max:2048'],
            'rating_rate'  => ['nullable', 'numeric', 'between:0,5'],
            'rating_count' => ['nullable', 'integer', 'min:0'],
            'external_id'  => [
                'required',
                'string',
                Rule::unique('products', 'external_id')->ignore($this->product),
            ],
        ];
    }
}
