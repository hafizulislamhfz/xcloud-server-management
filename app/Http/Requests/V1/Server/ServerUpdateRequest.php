<?php

namespace App\Http\Requests\V1\Server;

use App\Enums\Server\Provider;
use App\Enums\Server\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServerUpdateRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('servers', 'name')
                    ->ignore($this->id)
                    ->where(fn ($query) => $query->where('provider', $this->provider)),
            ],
            'ip_address' => [
                'required',
                'ipv4',
                Rule::unique('servers', 'ip_address')->ignore($this->id),
            ],
            'provider' => ['required', Rule::in(Provider::values())],
            'status' => ['required', Rule::in(Status::values())],
            'cpu_cores' => ['required', 'integer', 'between:1,128'],
            'ram_mb' => ['required', 'integer', 'between:512,1048576'],
            'storage_gb' => ['required', 'integer', 'between:10,1048576'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Server name is required.',
            'name.string' => 'Server name must be a string.',
            'name.max' => 'Server name cannot exceed 255 characters.',
            'name.unique' => 'Server name must be unique per provider.',

            'ip_address.required' => 'IP address is required.',
            'ip_address.ipv4' => 'IP address must be a valid IPv4.',
            'ip_address.unique' => 'This IP address is already assigned to another server.',

            'provider.required' => 'Provider is required.',
            'provider.in' => 'Provider must be one of: '.implode(', ', Provider::values()),

            'status.in' => 'Status must be one of: '.implode(', ', Status::values()),

            'cpu_cores.required' => 'CPU cores are required.',
            'cpu_cores.integer' => 'CPU cores must be an integer.',
            'cpu_cores.between' => 'CPU cores must be between 1 and 128.',

            'ram_mb.required' => 'RAM is required.',
            'ram_mb.integer' => 'RAM must be an integer.',
            'ram_mb.between' => 'RAM must be between 512 MB and 1 TB.',

            'storage_gb.required' => 'Storage is required.',
            'storage_gb.integer' => 'Storage must be an integer.',
            'storage_gb.between' => 'Storage must be between 10 GB and 1 PB.',
        ];
    }
}
