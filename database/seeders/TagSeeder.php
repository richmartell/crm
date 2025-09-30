<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['name' => 'Family', 'color' => '#ef4444'],
            ['name' => 'Rich Uni', 'color' => '#3b82f6'],
            ['name' => 'Florence School', 'color' => '#10b981'],
            ['name' => 'Work', 'color' => '#f59e0b'],
            ['name' => 'Friends', 'color' => '#8b5cf6'],
            ['name' => 'Neighbors', 'color' => '#ec4899'],
            ['name' => 'Sports Club', 'color' => '#14b8a6'],
            ['name' => 'VIP', 'color' => '#fbbf24'],
        ];

        foreach ($tags as $tag) {
            Tag::create($tag);
        }
    }
}