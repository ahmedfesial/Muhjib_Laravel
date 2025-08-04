<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\MainCategories;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubCategories>
 */
class SubCategoriesFactory extends Factory
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
            'name_ar' => 'تصنيف ' . $this->faker->word(),
            'main_category_id' => MainCategories::factory(),
        ];
    }
}
