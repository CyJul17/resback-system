<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sentiment_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feedback_id')->constrained('feedbacks')->cascadeOnDelete();
            $table->enum('sentiment', ['positive', 'neutral', 'negative']);
            $table->float('confidence')->default(0.0)->comment('Confidence score 0.0 - 1.0');
            $table->json('keywords')->nullable()->comment('Extracted keywords from feedback');
            $table->json('raw_response')->nullable()->comment('Full response from XLM-RoBERTa model');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sentiment_results');
    }
};
