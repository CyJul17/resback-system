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
        Schema::create('concern_rankings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('keyword');
            $table->unsignedInteger('frequency')->default(0)->comment('Total occurrences of this keyword');
            $table->unsignedInteger('positive_count')->default(0);
            $table->unsignedInteger('neutral_count')->default(0);
            $table->unsignedInteger('negative_count')->default(0);
            $table->timestamp('ranked_at')->nullable()->comment('When this ranking was last computed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('concern_rankings');
    }
};
