<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        $type = $this->faker->randomElement(['individual', 'company']);

        return [
            'type' => $type,
            'name' => $type === 'company' ? $this->faker->company() : $this->faker->name(),
            'nickname' => $this->faker->optional()->word(),
            'document' => $this->faker->numerify('##.###.###/####-##'),
            'street' => $this->faker->streetName(),
            'number' => $this->faker->buildingNumber(),
            'complement' => $this->faker->optional()->secondaryAddress(),
            'zip_code' => $this->faker->postcode(),
            'neighborhood' => $this->faker->citySuffix(),
            'city' => $this->faker->city(),
            'state' => $this->faker->stateAbbr(),
        ];
    }
}
