<?php

namespace Tests\Feature;

use App\Models\Feedback;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class FeedbackResultViewTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('sentimentResults')]
    public function test_submission_result_displays_the_actual_sentiment_percentage(
        string $sentiment,
        float $confidence,
        string $expectedSummary,
    ): void
    {
        $feedback = Feedback::create([
            'content' => 'This full feedback should not be repeated on the result page.',
            'status' => 'analyzed',
        ]);
        $feedback->sentimentResult()->create([
            'sentiment' => $sentiment,
            'confidence' => $confidence,
            'keywords' => ['wifi', 'service'],
            'concern_topics' => ['Wi-Fi / Internet'],
            'detected_languages' => ['English'],
            'language_category' => 'English',
            'language_confidence' => 1,
        ]);

        $this->view('feedback.result', ['feedback' => $feedback->load('sentimentResult')])
            ->assertSee($expectedSummary)
            ->assertSee('#wifi')
            ->assertSee('#service')
            ->assertDontSee('Language Classification')
            ->assertDontSee('Google Gemini API')
            ->assertDontSee($feedback->content);
    }

    /** @return array<string, array{string, float, string}> */
    public static function sentimentResults(): array
    {
        return [
            'positive result' => ['positive', .842, '84% Positive'],
            'neutral result' => ['neutral', .506, '51% Neutral'],
            'negative result' => ['negative', .913, '91% Negative'],
        ];
    }
}
