<?php

namespace App\Http\Requests\V1\Server;

use App\Enums\Server\Provider;
use App\Enums\Server\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServerIndexRequest extends FormRequest
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
            'search' => ['nullable', 'string', 'max:255'],
            'provider' => ['nullable', Rule::in(Provider::values())],
            'status' => ['nullable', Rule::in(Status::values())],
            'sort_by' => ['nullable', Rule::in(['name', 'provider', 'status', 'cpu_cores', 'ram_mb', 'storage_gb', 'created_at'])],
            'sort_order' => ['nullable', Rule::in(['asc', 'desc'])],
            'per_page' => ['nullable', 'integer', 'between:1,100'],
            'page_number' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'per_page.integer' => 'Per page must be an integer.',
            'per_page.between' => 'Per page must be between 1 and 100.',
            'page_number.integer' => 'Page number must be an integer.',
            'page_number.min' => 'Page number must be at least 1.',
            'search.string' => 'Search must be a string.',
            'search.max' => 'Search term cannot exceed 255 characters.',
            'provider.in' => 'Provider must be one of: '.implode(', ', Provider::values()),
            'status.in' => 'Status must be one of: '.implode(', ', Status::values()),
            'sort_by.in' => 'Sort by must be a valid column.',
            'sort_order.in' => 'Sort order must be either asc or desc.',
        ];
    }
}
