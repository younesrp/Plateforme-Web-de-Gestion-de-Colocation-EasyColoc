<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Colocation;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public static function seedForColocation(Colocation $colocation): void
    {
        $categories = [
            ['name' => 'Loyer', 'color' => '#EF4444'],
            ['name' => 'Internet', 'color' => '#3B82F6'],
            ['name' => 'Électricité', 'color' => '#F59E0B'],
            ['name' => 'Eau', 'color' => '#06B6D4'],
            ['name' => 'Gaz', 'color' => '#8B5CF6'],
            ['name' => 'Courses', 'color' => '#10B981'],
            ['name' => 'Ménage', 'color' => '#EC4899'],
            ['name' => 'Assurance', 'color' => '#6366F1'],
        ];

        foreach ($categories as $category) {
            Category::create([
                'colocation_id' => $colocation->id,
                'name' => $category['name'],
                'color' => $category['color'],
            ]);
        }
    }
}
