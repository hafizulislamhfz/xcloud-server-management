<?php

namespace Database\Factories;

use App\Enums\Server\Provider;
use App\Enums\Server\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Server>
 */
class ServerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->company(),
            'ip_address' => $this->faker->unique()->ipv4(),
            'provider' => $this->faker->randomElement(Provider::values()),
            'status' => $this->faker->randomElement(Status::values()),
            'cpu_cores' => $this->faker->numberBetween(1, 32),
            'ram_mb' => $this->faker->randomElement([1024, 2048, 4096, 8192]),
            'storage_gb' => $this->faker->randomElement([50, 100, 200, 500]),
        ];
    }
}
