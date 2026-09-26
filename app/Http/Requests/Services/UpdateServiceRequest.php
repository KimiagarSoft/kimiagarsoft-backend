<?php

namespace App\Http\Requests\Services;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateServiceRequest extends FormRequest
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
                'sometimes',
                'integer',
                'exists:service_categories,id',
            ],

            'title' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'slug' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('services', 'slug')->ignore($this->route('service')),
            ],

            'short_description' => [
                'sometimes',
                'nullable',
                'string',
            ],

            'description' => [
                'sometimes',
                'string',
            ],

            'status' => [
                'sometimes',
                'string',
                'in:draft,published',
            ],

            'sort_order' => [
                'sometimes',
                'integer',
                'min:0',
            ],

            'published_at' => [
                'sometimes',
                'nullable',
                'date',
            ],
        ];
    }
}

