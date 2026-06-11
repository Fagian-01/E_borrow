<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Laptop ASUS ROG Strix G16',
            'MacBook Pro 14" M3',
            'Dell XPS 15 9530',
            'Proyektor Epson EB-X51',
            'Proyektor BenQ MH560',
            'Kamera Canon EOS R50',
            'Kamera Sony A6400',
            'GoPro Hero 12 Black',
            'Switch Cisco Catalyst 1000',
            'Router MikroTik RB750Gr3',
            'Printer HP LaserJet Pro M404dn',
            'Scanner Epson WorkForce DS-530',
            'Toyota Avanza 2024',
            'Honda HR-V 2024',
            'Mikroskop Olympus CX23',
            'Meja Rapat Conference 240cm',
            'Kursi Ergonomis Herman Miller',
            'Speaker JBL Professional EON715',
            'Microphone Shure SM58',
            'CCTV Hikvision DS-2CD2143G2',
        ]);

        $stock = fake()->numberBetween(3, 15);

        return [
            'category_id' => Category::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->paragraph(3),
            'specifications' => json_encode([
                'brand' => fake()->company(),
                'weight' => fake()->randomFloat(1, 0.5, 25) . ' kg',
                'year' => fake()->year(),
            ]),
            'total_stock' => $stock,
            'available_stock' => $stock,
            'max_borrow_days' => fake()->randomElement([3, 5, 7, 14, 30]),
            'max_qty_per_user' => fake()->randomElement([1, 2, 3]),
            'fine_per_day' => fake()->randomElement([5000, 10000, 15000, 25000, 50000]),
            'is_active' => true,
            'requires_approval' => true,
        ];
    }
}
