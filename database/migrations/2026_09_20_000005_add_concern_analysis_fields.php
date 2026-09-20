<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sentiment_results', function (Blueprint $table) {
            $table->json('concern_topics')->nullable()->after('keywords');
        });

        Schema::table('concern_rankings', function (Blueprint $table) {
            $table->decimal('critical_score', 5, 2)->default(0)->after('negative_count');
            $table->decimal('negative_ratio', 5, 4)->default(0)->after('critical_score');
            $table->decimal('average_negative_confidence', 5, 4)->default(0)->after('negative_ratio');
            $table->decimal('recency_score', 5, 4)->default(0)->after('average_negative_confidence');
            $table->timestamp('latest_feedback_at')->nullable()->after('recency_score');
            $table->unique(['category_id', 'keyword']);
        });
    }

    public function down(): void
    {
        Schema::table('concern_rankings', function (Blueprint $table) {
            $table->dropUnique(['category_id', 'keyword']);
            $table->dropColumn([
                'critical_score',
                'negative_ratio',
                'average_negative_confidence',
                'recency_score',
                'latest_feedback_at',
            ]);
        });

        Schema::table('sentiment_results', function (Blueprint $table) {
            $table->dropColumn('concern_topics');
        });
    }
};
