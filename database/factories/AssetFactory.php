<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Asset>
 */
class AssetFactory extends Factory
{
    public function definition(): array
    {
        return [
            'item_id' => Item::factory(),
            'asset_code' => 'AST-' . strtoupper(Str::random(3)) . '-' . fake()->unique()->numerify('###'),
            'barcode' => fake()->unique()->ean13(),
            'serial_number' => strtoupper(Str::random(4)) . '-' . fake()->numerify('######'),
            'status' => 'available',
            'condition' => fake()->randomElement(['excellent', 'good', 'good', 'fair']),
            'condition_note' => null,
            'purchase_date' => fake()->dateTimeBetween('-3 years', '-1 month'),
            'purchase_price' => fake()->randomFloat(2, 500000, 50000000),
            'location' => fake()->randomElement([
                'Gudang A - Rak 1', 'Gudang A - Rak 2', 'Gudang B - Rak 1',
                'Gudang B - Rak 3', 'Ruang Server', 'Ruang IT', 'Lobby',
            ]),
        ];
    }
}
