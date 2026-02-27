<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->isMethod('PATCH') || $this->isMethod('PUT')) {
            return [
                'title'    => ['sometimes', 'string', 'min:3'],
                'price'    => ['sometimes', 'numeric', 'gt:0'],
                'category' => ['sometimes', 'string'],
            ];
        }

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

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Dados inválidos, verifique o formulário.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
