<?php

namespace App\Console\Commands;

use App\Models\Feedback;
use App\Services\SentimentService;
use Illuminate\Console\Command;

class ClassifyFeedbackLanguages extends Command
{
    protected $signature = 'feedback:classify-languages {--limit= : Maximum number of existing feedbacks to analyze}';

    protected $description = 'Analyze existing feedbacks that do not have a stored language classification';

    public function handle(SentimentService $sentimentService): int
    {
        $query = Feedback::query()
            ->where(function ($query): void {
                $query->whereDoesntHave('sentimentResult')
                    ->orWhereHas('sentimentResult', fn ($query) => $query->whereNull('language_category'));
            })
            ->orderBy('id');

        if ($this->option('limit')) {
            $query->limit(max(1, (int) $this->option('limit')));
        }

        $feedbacks = $query->get();

        if ($feedbacks->isEmpty()) {
            $this->info('No existing feedbacks need language classification.');

            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($feedbacks->count());

        foreach ($feedbacks as $feedback) {
            $sentimentService->analyze($feedback);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Existing feedback classification finished.');

        return self::SUCCESS;
    }
}
