<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $categories = collect(Category::FEEDBACK_CATEGORIES)
            ->map(fn (string $name): array => [
                'name' => $name,
                'slug' => Str::slug($name),
                'icon' => '🏫',
                'description' => "Feedback intended for {$name}",
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

        DB::transaction(function () use ($categories): void {
            Category::query()
                ->whereNotIn('slug', $categories->pluck('slug'))
                ->update(['is_active' => false]);

            Category::query()->upsert(
                $categories->all(),
                ['slug'],
                ['name', 'icon', 'description', 'is_active', 'updated_at']
            );
        });
    }
}
