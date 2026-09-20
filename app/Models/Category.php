<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    public const FEEDBACK_CATEGORIES = [
        'CCIS',
        'COE',
        'CAS',
        'CBEA',
        'CHS',
        'CTE',
        'CIT',
        'CASAT',
        'CAFSD',
        'LIBRARY',
        'ADMIN',
        'TEATRO',
        'COVER COURT',
        'OVAL',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all feedbacks under this category.
     */
    public function feedbacks(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }

    /**
     * Get concern rankings for this category.
     */
    public function concernRankings(): HasMany
    {
        return $this->hasMany(ConcernRanking::class);
    }

    /**
     * Scope to active categories only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
