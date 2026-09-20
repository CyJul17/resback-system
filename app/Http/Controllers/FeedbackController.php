<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeedbackRequest;
use App\Models\Category;
use App\Models\Feedback;
use App\Services\SentimentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    public function __construct(protected SentimentService $sentimentService) {}

    /**
     * Show the anonymous feedback submission form.
     */
    public function create(): View
    {
        $categoryOrder = array_flip(Category::FEEDBACK_CATEGORIES);
        $categories = Category::active()
            ->get()
            ->sortBy(fn (Category $category) => $categoryOrder[$category->name] ?? PHP_INT_MAX)
            ->values();

        return view('feedback.create', compact('categories'));
    }

    /**
     * Store a newly submitted feedback and trigger sentiment analysis.
     */
    public function store(StoreFeedbackRequest $request): View
    {
        // Hash IP for rate limiting — never stored as plain text
        $ipHash = hash('sha256', $request->ip() . config('app.key'));

        $feedback = Feedback::create([
            'category_id' => $request->category_id,
            'content'     => $request->content,
            'ip_hash'     => $ipHash,
            'status'      => 'pending',
        ]);

        // Trigger sentiment analysis (runs synchronously; swap for a queued job later)
        $this->sentimentService->analyze($feedback);

        return view('feedback.result', ['feedback' => $feedback->load('sentimentResult')]);
    }

    /**
     * Show the thank-you confirmation page.
     */
    public function thankyou(): View
    {
        $feedback = null;
        if (session()->has('last_feedback_id')) {
            $feedback = Feedback::with('sentimentResult')->find(session('last_feedback_id'));
        }
        return view('feedback.thankyou', compact('feedback'));
    }
}
