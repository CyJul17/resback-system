<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->replaceLabels([
            'English + Tagalog' => 'Taglish',
            'English + Ilocano' => 'Iloclish',
            'Tagalog + Ilocano' => 'Taglocano',
            '3 Languages Used' => 'Trilingual',
        ]);
    }

    public function down(): void
    {
        $this->replaceLabels([
            'Taglish' => 'English + Tagalog',
            'Iloclish' => 'English + Ilocano',
            'Taglocano' => 'Tagalog + Ilocano',
            'Trilingual' => '3 Languages Used',
        ]);
    }

    /**
     * @param array<string, string> $labels
     */
    private function replaceLabels(array $labels): void
    {
        foreach ($labels as $oldLabel => $newLabel) {
            DB::table('sentiment_results')
                ->where('language_category', $oldLabel)
                ->update(['language_category' => $newLabel]);
        }
    }
};
