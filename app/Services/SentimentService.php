<?php

namespace App\Services;

use App\Models\Feedback;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SentimentService
{
    /**
     * Send feedback content to the Gemma model via Google Gemini API and store the result.
     */
    public function analyze(Feedback $feedback): void
    {
        $apiKey = env('GEMINI_API_KEY');
        $model = env('GEMINI_MODEL', 'gemma-2-9b-it'); // Adjust model name based on actual availability

        if (empty($apiKey)) {
            Log::error("Sentiment analysis failed: GEMINI_API_KEY is not set.");
            $feedback->update(['status' => 'failed']);
            return;
        }

        // Standard endpoint for Gemini / Generative Language API
        $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $prompt = <<<PROMPT
You are an expert in Ilocano NLP and Sentiment Analysis.
Analyze the following student feedback text.
Return ONLY a valid JSON object with EXACTLY these three keys:
- "sentiment": A string, exactly one of "positive", "neutral", or "negative".
- "confidence": A float between 0.0 and 1.0 representing how confident you are.
- "keywords": An array of 1 to 5 important string keywords from the text.

Do not include markdown blocks like ```json or any other explanation. Just the raw JSON.

Feedback Text:
"{$feedback->content}"
PROMPT;

        try {
            $response = Http::timeout(30)->withHeaders([
                'Content-Type' => 'application/json',
            ])->post($apiUrl, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.1,
                    'topK' => 1,
                    'topP' => 1,
                    'maxOutputTokens' => 200,
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                $responseText = $data['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
                
                // Clean up potential markdown formatting if the model still returns it
                $responseText = Str::replaceFirst('```json', '', $responseText);
                $responseText = Str::replaceLast('```', '', $responseText);
                $responseText = trim($responseText);

                $parsedData = json_decode($responseText, true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    $feedback->sentimentResult()->create([
                        'sentiment'    => $parsedData['sentiment']   ?? 'neutral',
                        'confidence'   => (float) ($parsedData['confidence']  ?? 0.0),
                        'keywords'     => $parsedData['keywords']    ?? [],
                        'raw_response' => $data, // Store full Gemini response for debugging
                    ]);

                    $feedback->update(['status' => 'analyzed']);
                } else {
                    Log::warning("Sentiment API returned invalid JSON for feedback #{$feedback->id}", [
                        'response_text' => $responseText,
                        'json_error' => json_last_error_msg(),
                    ]);
                    $feedback->update(['status' => 'failed']);
                }

            } else {
                Log::warning("Sentiment API returned non-success for feedback #{$feedback->id}", [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                $feedback->update(['status' => 'failed']);
            }
        } catch (\Throwable $e) {
            Log::error("Sentiment analysis failed for feedback #{$feedback->id}: " . $e->getMessage());
            $feedback->update(['status' => 'failed']);
        }
    }
}
