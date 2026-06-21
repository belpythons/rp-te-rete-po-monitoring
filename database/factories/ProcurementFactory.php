<?php

namespace Database\Factories;

use App\Models\Procurement;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProcurementFactory extends Factory
{
    protected $model = Procurement::class;

    public function definition(): array
    {
        return [
            'no' => (string) $this->faker->unique()->randomNumber(4),
            'rp_number' => 'RP-' . $this->faker->unique()->randomNumber(5),
            'description' => $this->faker->sentence(),
            'date_created' => $this->faker->date('Y-m-d'),
            'status' => 'Pending',
            'phase' => 'RP',
            'tanggal_masuk' => $this->faker->date('Y-m-d'),
            'vendor' => $this->faker->company(),
        ];
    }
}
