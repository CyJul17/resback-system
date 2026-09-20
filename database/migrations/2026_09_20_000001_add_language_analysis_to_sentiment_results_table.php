<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sentiment_results', function (Blueprint $table) {
            $table->json('detected_languages')->nullable()->after('keywords');
            $table->string('language_category', 64)->nullable()->index()->after('detected_languages');
            $table->float('language_confidence')->nullable()->after('language_category');
        });
    }

    public function down(): void
    {
        Schema::table('sentiment_results', function (Blueprint $table) {
            $table->dropIndex(['language_category']);
            $table->dropColumn([
                'detected_languages',
                'language_category',
                'language_confidence',
            ]);
        });
    }
};
