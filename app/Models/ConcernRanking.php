<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConcernRanking extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'category_id',
        'keyword',
        'frequency',
        'positive_count',
        'neutral_count',
        'negative_count',
        'ranked_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'ranked_at' => 'datetime',
    ];

    /**
     * Get the category this ranking belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the dominant sentiment (the highest count).
     */
    public function getDominantSentimentAttribute(): string
    {
        $counts = [
            'positive' => $this->positive_count,
            'neutral'  => $this->neutral_count,
            'negative' => $this->negative_count,
        ];

        return array_keys($counts, max($counts))[0];
    }
}
