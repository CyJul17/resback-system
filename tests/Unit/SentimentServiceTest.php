<?php

namespace Tests\Unit;

use App\Services\LanguageCategoryService;
use App\Services\ConcernRankingService;
use App\Services\ConcernTopicService;
use App\Services\SentimentService;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class SentimentServiceTest extends TestCase
{
    public function test_it_parses_gemma_sentiment_and_language_results(): void
    {
        $service = new SentimentService(
            new LanguageCategoryService(),
            new ConcernTopicService(),
            new ConcernRankingService(),
        );
        $method = new ReflectionMethod($service, 'parseResult');

        $result = $method->invoke($service, [
            'candidates' => [[
                'content' => [
                    'parts' => [
                        ['text' => '', 'thought' => true],
                        ['text' => '[{"sentiment":"negative","confidence":0.96,"keywords":["wifi","slow"],"languages":["English","Ilocano"],"language_confidence":0.93}]'],
                    ],
                ],
            ]],
        ]);

        $this->assertSame('negative', $result['sentiment']);
        $this->assertSame(0.96, $result['confidence']);
        $this->assertSame(['wifi', 'slow'], $result['keywords']);
        $this->assertSame(['Wi-Fi / Internet'], $result['concern_topics']);
        $this->assertSame(['English', 'Ilocano'], $result['detected_languages']);
        $this->assertSame('Iloclish', $result['language_category']);
        $this->assertSame(0.93, $result['language_confidence']);
    }
}
