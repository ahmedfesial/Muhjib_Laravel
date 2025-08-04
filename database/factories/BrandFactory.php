<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Brand>
 */
class BrandFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name_en' => $this->faker->company(),
            'name_ar' => 'شركة ' . $this->faker->word(),
            'logo' => $this->faker->imageUrl(),
            'short_description_en' => $this->faker->text(50),
            'short_description_ar' => $this->faker->text(50),
            'full_description_en' => $this->faker->text(100),
            'full_description_ar' => $this->faker->text(100),
            'background_image_url' => $this->faker->imageUrl(),
            'color_code' => $this->faker->hexColor(),
            'catalog_pdf_url' => $this->faker->url(),
        ];
    }
}
