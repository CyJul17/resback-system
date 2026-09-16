@extends('layouts.guest')
@section('title', 'Share Your Feedback')

@section('content')
<div class="feedback-card animate-fade-up">

    {{-- Header --}}
    <div class="feedback-card-header">
        <div class="header-icon">💬</div>
        <h1>Share Your Feedback</h1>
        <p>Help us improve CCIS by sharing your campus experience. Every voice matters.</p>
    </div>

    {{-- Body --}}
    <div class="feedback-card-body">

        {{-- Anonymous Badge --}}
        <div>
            <span class="anonymous-badge">
                🔒 Your submission is completely anonymous
            </span>
        </div>

        {{-- Validation Errors --}}
        @if($errors->any())
            <div class="alert alert-error">
                <span>⚠️</span>
                <ul style="list-style:none;padding:0;margin:0;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('feedback.store') }}" method="POST" id="feedbackForm">
            @csrf

            {{-- Category Selection --}}
            @if($categories->isNotEmpty())
            <div class="form-group">
                <label class="form-label">
                    Category <span class="optional">(optional)</span>
                </label>
                <div class="category-grid">
                    @foreach($categories as $category)
                        <div>
                            <input
                                type="radio"
                                name="category_id"
                                id="cat_{{ $category->id }}"
                                value="{{ $category->id }}"
                                class="category-option"
                                {{ old('category_id') == $category->id ? 'checked' : '' }}
                            >
                            <label for="cat_{{ $category->id }}" class="category-label">
                                <span class="cat-icon">{{ $category->icon ?? '📌' }}</span>
                                {{ $category->name }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Feedback Text --}}
            <div class="form-group">
                <label for="content" class="form-label">Your Feedback</label>
                <textarea
                    id="content"
                    name="content"
                    class="form-control {{ $errors->has('content') ? 'is-invalid' : '' }}"
                    placeholder="Describe your experience, concern, or suggestion in detail..."
                    maxlength="2000"
                    rows="6"
                    oninput="updateCounter(this)"
                >{{ old('content') }}</textarea>
                <div class="char-counter" id="charCounter">0 / 2000</div>
                @error('content')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Privacy Notice --}}
            <div style="background:var(--gray-50);border:1px solid var(--gray-200);border-radius:var(--radius-md);padding:.875rem 1rem;margin-bottom:1.5rem;font-size:.8125rem;color:var(--gray-500);line-height:1.6;">
                <strong style="color:var(--gray-700);">📋 Privacy Note:</strong>
                Your feedback is submitted anonymously. No personal information is collected.
                The content will be analyzed using AI sentiment analysis to help identify campus concerns.
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg" id="submitBtn">
                <span>🚀</span> Submit Feedback
            </button>
        </form>

    </div>
</div>
@endsection

@push('scripts')
<script>
    // Character counter
    function updateCounter(textarea) {
        const counter = document.getElementById('charCounter');
        const len = textarea.value.length;
        const max = 2000;
        counter.textContent = `${len} / ${max}`;
        counter.className = 'char-counter';
        if (len > 1800) counter.classList.add('near-limit');
        if (len >= max)  counter.classList.add('at-limit');
    }

    // Initialize counter on load
    const contentField = document.getElementById('content');
    if (contentField.value) updateCounter(contentField);

    // Prevent double-submit
    document.getElementById('feedbackForm').addEventListener('submit', function () {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<span>⏳</span> Submitting...';
    });
</script>
@endpush
