<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Feedback;
use App\Services\ConcernRankingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConcernRankingTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_ranks_repeated_negative_concerns_above_lower_volume_concerns(): void
    {
        $category = Category::create(['name' => 'CCIS', 'slug' => 'ccis', 'is_active' => true]);

        foreach (range(1, 3) as $number) {
            $this->createAnalyzedFeedback($category->id, "Wi-Fi concern {$number}", 'negative', .95, ['Wi-Fi / Internet']);
        }

        $this->createAnalyzedFeedback($category->id, 'Room concern', 'negative', .95, ['Classroom / Room']);
        $this->createAnalyzedFeedback($category->id, 'Positive teaching report', 'positive', .90, ['Teaching']);

        $rankings = app(ConcernRankingService::class)->rank($category->id);

        $this->assertSame('Wi-Fi / Internet', $rankings->first()['topic']);
        $this->assertSame(3, $rankings->first()['negative_count']);
        $this->assertFalse($rankings->first()['low_evidence']);
        $this->assertTrue($rankings->first()['critical_score'] > $rankings->firstWhere('topic', 'Classroom / Room')['critical_score']);
        $this->assertTrue($rankings->firstWhere('topic', 'Classroom / Room')['low_evidence']);
        $this->assertSame(0.0, $rankings->firstWhere('topic', 'Teaching')['critical_score']);
    }

    private function createAnalyzedFeedback(
        int $categoryId,
        string $content,
        string $sentiment,
        float $confidence,
        array $topics,
    ): void {
        $feedback = Feedback::create([
            'category_id' => $categoryId,
            'content' => $content,
            'status' => 'analyzed',
        ]);

        $feedback->sentimentResult()->create([
            'sentiment' => $sentiment,
            'confidence' => $confidence,
            'keywords' => [],
            'concern_topics' => $topics,
            'detected_languages' => ['English'],
            'language_category' => 'English',
            'language_confidence' => 1,
        ]);
    }
}
