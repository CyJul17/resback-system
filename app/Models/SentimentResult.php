<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SentimentResult extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'feedback_id',
        'sentiment',
        'confidence',
        'keywords',
        'raw_response',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'keywords'     => 'array',
        'raw_response' => 'array',
        'confidence'   => 'float',
    ];

    /**
     * Get the feedback that this result belongs to.
     */
    public function feedback(): BelongsTo
    {
        return $this->belongsTo(Feedback::class);
    }

    /**
     * Get human-readable confidence percentage.
     */
    public function getConfidencePercentAttribute(): string
    {
        return round($this->confidence * 100, 1) . '%';
    }
}
