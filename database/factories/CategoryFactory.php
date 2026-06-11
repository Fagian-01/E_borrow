<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Laptop & Komputer',
            'Proyektor & Display',
            'Kamera & Multimedia',
            'Peralatan Jaringan',
            'Peralatan Kantor',
            'Kendaraan Operasional',
            'Peralatan Laboratorium',
            'Furniture & Mebel',
            'Peralatan Audio',
            'Peralatan Keamanan',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(12),
            'icon' => fake()->randomElement(['💻', '📽️', '📷', '🌐', '🖨️', '🚗', '🔬', '🪑', '🎤', '🔒']),
            'color' => fake()->hexColor(),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }
}
