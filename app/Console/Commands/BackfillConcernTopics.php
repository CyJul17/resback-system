<?php

namespace App\Console\Commands;

use App\Models\SentimentResult;
use App\Services\ConcernRankingService;
use App\Services\ConcernTopicService;
use Illuminate\Console\Command;

class BackfillConcernTopics extends Command
{
    protected $signature = 'feedback:backfill-concerns {--force : Rebuild topics that are already populated}';

    protected $description = 'Normalize concern topics for existing feedback and rebuild critical-concern rankings';

    public function handle(
        ConcernTopicService $topicService,
        ConcernRankingService $rankingService,
    ): int {
        $query = SentimentResult::query()->with('feedback')->orderBy('id');

        if (! $this->option('force')) {
            $query->whereNull('concern_topics');
        }

        $results = $query->get();
        $bar = $this->output->createProgressBar($results->count());

        foreach ($results as $result) {
            $result->update([
                'concern_topics' => $topicService->normalize(
                    [],
                    $result->keywords ?? [],
                    $result->feedback?->content ?? ''
                ),
            ]);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $rankingService->rebuildAll();
        $this->info("Concern topics updated for {$results->count()} sentiment results; rankings rebuilt.");

        return self::SUCCESS;
    }
}
