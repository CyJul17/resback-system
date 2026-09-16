<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Feedback;
use App\Models\SentimentResult;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the admin/faculty dashboard with concern rankings and sentiment data.
     */
    public function index(): View
    {
        // --- Summary Stats ---
        $totalFeedbacks  = Feedback::count();
        $analyzedCount   = Feedback::analyzed()->count();
        $pendingCount    = Feedback::pending()->count();

        // --- Sentiment Distribution ---
        $sentimentCounts = SentimentResult::selectRaw('sentiment, COUNT(*) as count')
            ->groupBy('sentiment')
            ->pluck('count', 'sentiment')
            ->toArray();

        $sentimentData = [
            'positive' => $sentimentCounts['positive'] ?? 0,
            'neutral'  => $sentimentCounts['neutral']  ?? 0,
            'negative' => $sentimentCounts['negative'] ?? 0,
        ];

        // --- Per-Category Breakdown ---
        $categories = Category::withCount('feedbacks')
            ->with(['feedbacks.sentimentResult'])
            ->active()
            ->orderByDesc('feedbacks_count')
            ->get()
            ->map(function ($category) {
                $results = $category->feedbacks->pluck('sentimentResult')->filter();
                $category->positive = $results->where('sentiment', 'positive')->count();
                $category->neutral  = $results->where('sentiment', 'neutral')->count();
                $category->negative = $results->where('sentiment', 'negative')->count();
                return $category;
            });

        // --- Recent Feedbacks (last 10) ---
        $recentFeedbacks = Feedback::with(['category', 'sentimentResult'])
            ->latest()
            ->limit(10)
            ->get();

        return view('dashboard.index', compact(
            'totalFeedbacks',
            'analyzedCount',
            'pendingCount',
            'sentimentData',
            'categories',
            'recentFeedbacks'
        ));
    }
}
