<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\SubCategories;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
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
            'name_ar' => 'منتج ' . $this->faker->word(),
            'features' => $this->faker->paragraph(),
            'main_color' => $this->faker->colorName(),
            'sub_category_id' => SubCategories::factory(),
            'main_image' => $this->faker->imageUrl(),
            'pdf_hs' => $this->faker->url(),
            'pdf_msds' => $this->faker->url(),
            'pdf_technical' => $this->faker->url(),
            'hs_code' => (string) $this->faker->randomNumber(4),
            'sku' => $this->faker->uuid(),
            'pack_size' => '10x10',
            'dimensions' => '10x5x3',
            'capacity' => '1L',
            'specification' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 10, 500),
            'is_visible' => true,
        ];
    }
}
