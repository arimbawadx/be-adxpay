<?php

namespace Database\Factories;

use App\Models\Midtrans;
use Illuminate\Database\Eloquent\Factories\Factory;

class MidtransFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Midtrans::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'number' => $this->faker->randomNumber(8),
            'total_price' => $this->faker->numberBetween(25000, 200000),
            'payment_status' => 1,
        ];
    }
}
