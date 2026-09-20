<?php

namespace App\Services;

class LanguageCategoryService
{
    public const CATEGORY_LABELS = [
        'English',
        'Tagalog',
        'Ilocano',
        'Taglish',
        'Iloclish',
        'Taglocano',
        'Trilingual',
        'Other Language',
    ];

    private const LANGUAGE_ORDER = [
        'English',
        'Tagalog',
        'Ilocano',
        'Other Language',
    ];

    private const MIXED_LANGUAGE_LABELS = [
        'English|Tagalog' => 'Taglish',
        'English|Ilocano' => 'Iloclish',
        'Tagalog|Ilocano' => 'Taglocano',
        'English|Tagalog|Ilocano' => 'Trilingual',
    ];

    /**
     * @param array<mixed> $languages
     * @return array{detected_languages: list<string>, language_category: string}
     */
    public function normalize(array $languages): array
    {
        $normalized = collect($languages)
            ->filter(fn (mixed $language): bool => is_string($language) && filled(trim($language)))
            ->map(fn (string $language): string => $this->normalizeName($language))
            ->unique()
            ->sortBy(fn (string $language): int => array_search($language, self::LANGUAGE_ORDER, true))
            ->values();

        if ($normalized->isEmpty()) {
            $normalized->push('Other Language');
        }

        $detectedLanguages = $normalized->all();

        if ($normalized->contains('Other Language')) {
            $category = 'Other Language';
        } else {
            $key = $normalized->implode('|');
            $category = self::MIXED_LANGUAGE_LABELS[$key] ?? $normalized->implode(' + ');
        }

        return [
            'detected_languages' => $detectedLanguages,
            'language_category' => $category,
        ];
    }

    private function normalizeName(string $language): string
    {
        return match (strtolower(trim($language))) {
            'english' => 'English',
            'tagalog', 'filipino' => 'Tagalog',
            'ilocano', 'iloko' => 'Ilocano',
            default => 'Other Language',
        };
    }
}
