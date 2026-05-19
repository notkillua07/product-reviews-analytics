<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class AnalysisApiService
{
    private string $url;

    public function __construct()
    {
        $this->url = config('services.analysis_api.url');

        if (empty($this->url)) {
            throw new RuntimeException('ANALYSIS_API_URL is not configured in your .env file.');
        }
    }

    /**
     * Send the CSV to the FastAPI backend and return structured results.
     *
     * FastAPI endpoint must accept:
     *   POST multipart/form-data
     *     - file        : UploadFile (the CSV)
     *     - product_name: str
     *
     * And return JSON:
     * {
     *   "negative_reasons": [{ "reason": str, "count": int }, ...],  // top 3
     *   "reviews":          [{ "text": str, "label": "positive"|"negative" }, ...]
     * }
     *
     * @throws RuntimeException
     */
    public function analyze(string $productName, UploadedFile $csvFile): array
    {
        try {
            $response = Http::timeout(120)
                ->attach(
                    'file',
                    file_get_contents($csvFile->getRealPath()),
                    $csvFile->getClientOriginalName()
                )
                ->post($this->url, [
                    'product_name' => $productName,
                ]);

            if ($response->failed()) {
                throw new RuntimeException(
                    'Analysis API error (HTTP ' . $response->status() . '): '
                    . ($response->json('detail') ?? $response->json('message') ?? 'Unknown error.')
                );
            }

            $data = $response->json();

            $reviews            = $data['reviews'] ?? [];
            $topNegativeReasons = array_slice($data['negative_reasons'] ?? [], 0, 3);

            $positiveCount = count(array_filter($reviews, fn ($r) => ($r['label'] ?? '') === 'positive'));
            $negativeCount = count(array_filter($reviews, fn ($r) => ($r['label'] ?? '') === 'negative'));

            return [
                'total_reviews'        => count($reviews),
                'positive_count'       => $positiveCount,
                'negative_count'       => $negativeCount,
                'top_negative_reasons' => $topNegativeReasons,
                'reviews_data'         => $reviews,
            ];

        } catch (RequestException $e) {
            throw new RuntimeException('Could not reach the analysis API: ' . $e->getMessage());
        }
    }
}
