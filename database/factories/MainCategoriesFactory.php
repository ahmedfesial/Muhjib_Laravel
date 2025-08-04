<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Brand;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MainCategories>
 */
class MainCategoriesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name_en' => $this->faker->word(),
            'name_ar' => 'فئة ' . $this->faker->word(),
            'color_code' => $this->faker->hexColor(),
            'image_url' => $this->faker->imageUrl(),
            'brand_id' => Brand::factory(),
        ];
    }
}
