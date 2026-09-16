@extends('layouts.guest')
@section('title', 'Thank You!')

@section('content')
<div class="thankyou-card">
    <div class="thankyou-icon">✅</div>

    <h1>Thank You!</h1>
    <p>
        Your feedback has been received and submitted anonymously.<br>
        Our AI system will analyze it to help improve the CCIS campus experience.
    </p>

    <div style="display:flex;flex-direction:column;gap:.75rem;align-items:center;">
        <a href="{{ route('feedback.create') }}" class="btn btn-primary">
            📝 Submit Another Feedback
        </a>
        <span style="font-size:.8rem;color:var(--gray-400);">Your feedback makes a difference!</span>
    </div>

    {{-- What happens next --}}
    <div style="margin-top:2.5rem;text-align:left;background:var(--gray-50);border-radius:var(--radius-lg);padding:1.25rem 1.5rem;">
        <h3 style="font-size:.875rem;font-weight:600;color:var(--gray-700);margin-bottom:.875rem;">What happens next?</h3>
        <div style="display:flex;flex-direction:column;gap:.625rem;">
            <div style="display:flex;align-items:flex-start;gap:.75rem;font-size:.8125rem;color:var(--gray-600);">
                <span style="font-size:1rem;flex-shrink:0;margin-top:.05rem;">🧠</span>
                <span>Your feedback is processed by the XLM-RoBERTa AI model for sentiment classification.</span>
            </div>
            <div style="display:flex;align-items:flex-start;gap:.75rem;font-size:.8125rem;color:var(--gray-600);">
                <span style="font-size:1rem;flex-shrink:0;margin-top:.05rem;">📊</span>
                <span>Results are aggregated and ranked by frequency to highlight top campus concerns.</span>
            </div>
            <div style="display:flex;align-items:flex-start;gap:.75rem;font-size:.8125rem;color:var(--gray-600);">
                <span style="font-size:1rem;flex-shrink:0;margin-top:.05rem;">🏫</span>
                <span>Administration and faculty review the insights to take action on urgent issues.</span>
            </div>
        </div>
    </div>
</div>
@endsection
