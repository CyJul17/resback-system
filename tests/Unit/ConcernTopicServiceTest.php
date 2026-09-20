<?php

namespace Tests\Unit;

use App\Services\ConcernTopicService;
use PHPUnit\Framework\TestCase;

class ConcernTopicServiceTest extends TestCase
{
    public function test_it_groups_multilingual_synonyms_into_canonical_topics(): void
    {
        $topics = (new ConcernTopicService())->normalize(
            [],
            ['slow connection'],
            'Nakapsot ti wifi ken marumi ang classroom.'
        );

        $this->assertSame([
            'Wi-Fi / Internet',
            'Classroom / Room',
            'Cleanliness',
        ], $topics);
    }

    public function test_it_uses_other_concern_when_no_known_topic_matches(): void
    {
        $this->assertSame(
            ['Other Concern'],
            (new ConcernTopicService())->normalize([], ['student experience'], 'A unique concern.')
        );
    }
}
