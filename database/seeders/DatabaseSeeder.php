<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Item;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Create Default Users ──────────────────────────

        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@eborrow.test',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
            'phone' => '081234567890',
            'department' => 'IT',
            'employee_id' => 'EMP-ADMIN-001',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Staff Gudang',
            'email' => 'staff@eborrow.test',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'phone' => '081234567891',
            'department' => 'Logistics',
            'employee_id' => 'EMP-STAFF-001',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        User::create([
            'name' => 'User Demo',
            'email' => 'user@eborrow.test',
            'password' => Hash::make('password'),
            'role' => 'user',
            'phone' => '081234567892',
            'department' => 'Marketing',
            'employee_id' => 'EMP-USER-001',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        // Extra demo users
        User::factory(10)->create();

        // ── 2. Create Categories & Items with Assets ─────────

        $categoriesData = [
            [
                'name' => 'Laptop & Komputer',
                'icon' => '💻',
                'color' => '#3B82F6',
                'items' => [
                    ['name' => 'Laptop ASUS ROG Strix G16', 'stock' => 5, 'fine' => 25000, 'max_days' => 7],
                    ['name' => 'MacBook Pro 14" M3', 'stock' => 3, 'fine' => 50000, 'max_days' => 5],
                    ['name' => 'Dell XPS 15 9530', 'stock' => 4, 'fine' => 30000, 'max_days' => 7],
                ],
            ],
            [
                'name' => 'Proyektor & Display',
                'icon' => '📽️',
                'color' => '#8B5CF6',
                'items' => [
                    ['name' => 'Proyektor Epson EB-X51', 'stock' => 6, 'fine' => 15000, 'max_days' => 3],
                    ['name' => 'Proyektor BenQ MH560', 'stock' => 4, 'fine' => 20000, 'max_days' => 3],
                ],
            ],
            [
                'name' => 'Kamera & Multimedia',
                'icon' => '📷',
                'color' => '#EC4899',
                'items' => [
                    ['name' => 'Kamera Canon EOS R50', 'stock' => 3, 'fine' => 50000, 'max_days' => 5],
                    ['name' => 'Kamera Sony A6400', 'stock' => 2, 'fine' => 50000, 'max_days' => 5],
                    ['name' => 'GoPro Hero 12 Black', 'stock' => 4, 'fine' => 25000, 'max_days' => 7],
                ],
            ],
            [
                'name' => 'Peralatan Jaringan',
                'icon' => '🌐',
                'color' => '#14B8A6',
                'items' => [
                    ['name' => 'Switch Cisco Catalyst 1000', 'stock' => 3, 'fine' => 15000, 'max_days' => 14],
                    ['name' => 'Router MikroTik RB750Gr3', 'stock' => 5, 'fine' => 10000, 'max_days' => 14],
                ],
            ],
            [
                'name' => 'Peralatan Kantor',
                'icon' => '🖨️',
                'color' => '#F59E0B',
                'items' => [
                    ['name' => 'Printer HP LaserJet Pro M404dn', 'stock' => 4, 'fine' => 10000, 'max_days' => 7],
                    ['name' => 'Scanner Epson WorkForce DS-530', 'stock' => 3, 'fine' => 10000, 'max_days' => 5],
                ],
            ],
            [
                'name' => 'Peralatan Audio',
                'icon' => '🎤',
                'color' => '#EF4444',
                'items' => [
                    ['name' => 'Speaker JBL Professional EON715', 'stock' => 4, 'fine' => 25000, 'max_days' => 3],
                    ['name' => 'Microphone Shure SM58', 'stock' => 8, 'fine' => 15000, 'max_days' => 3],
                ],
            ],
        ];

        $sortOrder = 0;
        foreach ($categoriesData as $catData) {
            $category = Category::create([
                'name' => $catData['name'],
                'slug' => Str::slug($catData['name']),
                'description' => "Kategori untuk {$catData['name']}",
                'icon' => $catData['icon'],
                'color' => $catData['color'],
                'is_active' => true,
                'sort_order' => $sortOrder++,
            ]);

            foreach ($catData['items'] as $itemData) {
                $item = Item::create([
                    'category_id' => $category->id,
                    'name' => $itemData['name'],
                    'slug' => Str::slug($itemData['name']),
                    'description' => "Deskripsi untuk {$itemData['name']}. Barang ini tersedia untuk dipinjam oleh seluruh karyawan yang terdaftar dalam sistem.",
                    'specifications' => json_encode([
                        'brand' => explode(' ', $itemData['name'])[0] ?? '-',
                        'condition' => 'Baru/Baik',
                        'year' => rand(2022, 2024),
                    ]),
                    'total_stock' => $itemData['stock'],
                    'available_stock' => $itemData['stock'],
                    'max_borrow_days' => $itemData['max_days'],
                    'max_qty_per_user' => min(2, $itemData['stock']),
                    'fine_per_day' => $itemData['fine'],
                    'is_active' => true,
                    'requires_approval' => true,
                ]);

                // Create individual asset units for each item
                $prefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $itemData['name']), 0, 3));
                for ($i = 1; $i <= $itemData['stock']; $i++) {
                    Asset::create([
                        'item_id' => $item->id,
                        'asset_code' => "AST-{$prefix}-" . str_pad($item->id, 2, '0', STR_PAD_LEFT) . str_pad($i, 3, '0', STR_PAD_LEFT),
                        'barcode' => '890' . str_pad($item->id, 4, '0', STR_PAD_LEFT) . str_pad($i, 6, '0', STR_PAD_LEFT),
                        'serial_number' => strtoupper(Str::random(4)) . '-' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT),
                        'status' => 'available',
                        'condition' => fake()->randomElement(['excellent', 'good', 'good']),
                        'purchase_date' => fake()->dateTimeBetween('-2 years', '-1 month'),
                        'purchase_price' => fake()->randomFloat(2, 1000000, 35000000),
                        'location' => fake()->randomElement([
                            'Gudang A - Rak 1', 'Gudang A - Rak 2', 'Gudang B - Rak 1', 'Gudang B - Rak 3', 'Ruang IT',
                        ]),
                    ]);
                }
            }
        }

        // ── 3. Create Default Settings ───────────────────────

        $settings = [
            ['key' => 'app_name', 'value' => 'E-Borrow', 'group' => 'general', 'type' => 'string', 'description' => 'Nama aplikasi'],
            ['key' => 'company_name', 'value' => 'PT. Digital Nusantara', 'group' => 'general', 'type' => 'string', 'description' => 'Nama perusahaan'],
            ['key' => 'default_fine_per_day', 'value' => '10000', 'group' => 'borrowing', 'type' => 'integer', 'description' => 'Denda default per hari (Rp)'],
            ['key' => 'default_max_borrow_days', 'value' => '7', 'group' => 'borrowing', 'type' => 'integer', 'description' => 'Maksimal hari peminjaman default'],
            ['key' => 'auto_approve', 'value' => 'false', 'group' => 'borrowing', 'type' => 'boolean', 'description' => 'Otomatis approve peminjaman'],
            ['key' => 'reminder_days_before', 'value' => '1', 'group' => 'notification', 'type' => 'integer', 'description' => 'Kirim reminder H-X sebelum tenggat'],
            ['key' => 'enable_email_notification', 'value' => 'true', 'group' => 'notification', 'type' => 'boolean', 'description' => 'Aktifkan notifikasi email'],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
