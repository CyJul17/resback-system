@extends('layouts.guest')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h3 class="mb-0">NLP Sentiment Analysis Result</h3>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <p class="text-muted mb-2">Your submitted feedback:</p>
                        <blockquote class="blockquote italic text-dark fs-5">
                            "{{ $feedback->content }}"
                        </blockquote>
                    </div>

                    <hr>

                    <div class="row text-center my-4">
                        <div class="col-md-6 mb-3">
                            <div class="p-3 border rounded bg-light">
                                <p class="text-muted mb-1">Sentiment</p>
                                @php
                                    $sentiment = $feedback->sentimentResult->sentiment ?? 'unknown';
                                    $color = match($sentiment) {
                                        'positive' => 'text-success',
                                        'negative' => 'text-danger',
                                        'neutral'  => 'text-warning',
                                        default    => 'text-secondary',
                                    };
                                    $label = ucfirst($sentiment);
                                @endphp
                                <h2 class="{{ $color }} fw-bold text-uppercase">{{ $label }}</h2>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="p-3 border rounded bg-light">
                                <p class="text-muted mb-1">Confidence Score</p>
                                <h2 class="text-primary fw-bold">
                                    {{ number_format(($feedback->sentimentResult->confidence ?? 0) * 100, 1) }}%
                                </h2>
                            </div>
                        </div>
                    </div>

                    @if($feedback->status === 'failed')
                        <p class="text-center text-danger">
                            Analysis could not be completed. Please try submitting again.
                        </p>
                    @endif

                    <div class="mb-4">
                        <p class="text-muted text-center mb-2">Extracted Keywords</p>
                        <div class="d-flex flex-wrap justify-content-center gap-2">
                            @if(!empty($feedback->sentimentResult->keywords))
                                @foreach($feedback->sentimentResult->keywords as $keyword)
                                    <span class="badge bg-info text-dark px-3 py-2">#{{ $keyword }}</span>
                                @endforeach
                            @else
                                <span class="text-muted">No keywords extracted.</span>
                            @endif
                        </div>
                    </div>

                    <div class="text-center mt-5">
                        <a href="{{ route('feedback.create') }}" class="btn btn-primary btn-lg px-4">
                            Submit Another Feedback
                        </a>
                    </div>
                </div>
                <div class="card-footer text-center text-muted py-3">
                    <small>Model: {{ config('services.gemini.model') }} (Google Gemini API)</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
