<?php

namespace App\Http\Requests\Services;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
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
            'service_category_id' => [
                'required',
                'integer',
                'exists:service_categories,id',
            ],

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'slug' => [
                'required',
                'string',
                'max:255',
                'unique:services,slug',
            ],

            'short_description' => [
                'nullable',
                'string',
            ],

            'description' => [
                'required',
                'string',
            ],

            'status' => [
                'required',
                'string',
                'in:draft,published',
            ],

            'sort_order' => [
                'required',
                'integer',
                'min:0',
            ],

            'published_at' => [
                'nullable',
                'date',
            ],
        ];
    }
}

