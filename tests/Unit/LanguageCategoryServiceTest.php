<?php

namespace Tests\Unit;

use App\Services\LanguageCategoryService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class LanguageCategoryServiceTest extends TestCase
{
    /**
     * @param list<string> $languages
     */
    #[DataProvider('languageCombinations')]
    public function test_it_normalizes_language_combinations(array $languages, string $expected): void
    {
        $result = (new LanguageCategoryService())->normalize($languages);

        $this->assertSame($expected, $result['language_category']);
    }

    /**
     * @return array<string, array{list<string>, string}>
     */
    public static function languageCombinations(): array
    {
        return [
            'English' => [['English'], 'English'],
            'Tagalog' => [['Tagalog'], 'Tagalog'],
            'Ilocano' => [['Ilocano'], 'Ilocano'],
            'English and Tagalog' => [['Tagalog', 'English'], 'Taglish'],
            'English and Ilocano' => [['Ilocano', 'English'], 'Iloclish'],
            'Tagalog and Ilocano' => [['Ilocano', 'Tagalog'], 'Taglocano'],
            'all supported languages' => [['Ilocano', 'English', 'Tagalog'], 'Trilingual'],
            'unsupported language' => [['Spanish'], 'Other Language'],
            'supported and unsupported' => [['English', 'Spanish'], 'Other Language'],
            'common aliases' => [['Filipino', 'Iloko'], 'Taglocano'],
        ];
    }
}
