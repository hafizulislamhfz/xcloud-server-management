<?php

namespace App\Http\Resources\V1\Server;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'ip_address' => $this->ip_address,
            'provider' => $this->provider,
            'status' => $this->status,
            'cpu_cores' => $this->cpu_cores,
            'ram_mb' => $this->ram_mb,
            'storage_gb' => $this->storage_gb,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
