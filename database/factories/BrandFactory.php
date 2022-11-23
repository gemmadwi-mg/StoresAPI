<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BrandFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $brands = [
            'Louis Vuitton',
            'Gucci',
            'Burberry',
            'Chanel',
            'Prada',
            'Versace',
            'Armani',
            'Puma',
            'Adidas',
            'H&M',
            'Rolex',
            'Nike',
            'Allen Solly',
            'Van Heusen',
            'Aplle',
            'Samsung',
            'One Plus',
            'Pepe',
            'US Polo',
            'United Colors of Benetton'
        ];

        return [
            'name' => $this->faker->unique()->randomElement($brands),
            'details' => $this->faker->paragraph
        ];
    }
}
