<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bucket>
 */
class BucketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $region = ['North', 'South', 'East', 'West'];
        return [
            'name' => $region[array_rand($region)].'ern Region '.fake()->randomNumber(2),
            'description' => "Example Description",
            'created_by' => 1,
        ];
    }
}
