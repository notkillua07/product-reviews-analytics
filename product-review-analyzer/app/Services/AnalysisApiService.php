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
     * Parse the CSV (review_text column only), then POST JSON to FastAPI.
     *
     * FastAPI endpoint must accept:
     *   POST application/json
     *   Headers: Content-Type: application/json, Accept: application/json,
     *            ngrok-skip-browser-warning: 69420
     * Body:
     * {
     *   "product_name": str,
     *   "reviews": [{ "id": int, "text": str }, ...]
     * }
     *
     * And return JSON:
     * {
     *   "total_reviews":    int,
     *   "positive_count":   int,
     *   "negative_count":   int,
     *   "product_reasons":  [{ "reason": str, "count": int }, ...],
     *   "shipping_reasons": [{ "reason": str, "count": int }, ...],
     *   "reviews":          [{ "id": int, "text": str, "label": "positive"|"negative" }, ...]
     * }
     *
     * @throws RuntimeException
     */
    public function analyze(string $productName, UploadedFile $csvFile, ?string $productCategory = null): array
    {
        $reviews = $this->extractReviews($csvFile);

        $payload = [
            'product_name' => $productName,
            'reviews'      => $reviews,
        ];

        if ($productCategory !== null) {
            $payload['product_category'] = $productCategory;
        }

        try {
            $response = Http::timeout(300)
                ->withoutVerifying()
                ->withHeaders([
                    'Content-Type'               => 'application/json',
                    'Accept'                     => 'application/json',
                    'ngrok-skip-browser-warning' => '69420',
                ])
                ->post($this->url, $payload);

            if ($response->failed()) {
                throw new RuntimeException(
                    'Analysis API error (HTTP ' . $response->status() . '): '
                    . ($response->json('detail') ?? $response->json('message') ?? 'Unknown error.')
                );
            }

            $data = $response->json();

            $reviewResults   = $data['reviews'] ?? [];
            $productReasons  = $data['product_reasons'] ?? [];
            $shippingReasons = $data['shipping_reasons'] ?? [];

            return [
                'total_reviews'    => $data['total_reviews']   ?? count($reviewResults),
                'positive_count'   => $data['positive_count']  ?? 0,
                'negative_count'   => $data['negative_count']  ?? 0,
                'product_reasons'  => $productReasons,
                'shipping_reasons' => $shippingReasons,
                'reviews_data'     => $reviewResults,
            ];

        } catch (RequestException $e) {
            throw new RuntimeException('Could not reach the analysis API: ' . $e->getMessage());
        }
    }

    /**
     * Open the CSV, find the "review_text" column, and return numbered rows.
     * Skips rows where review_text is blank.
     *
     * @return array<int, array{id: int, text: string}>
     * @throws RuntimeException
     */
    private function extractReviews(UploadedFile $csvFile): array
    {
        $handle = fopen($csvFile->getRealPath(), 'r');

        if ($handle === false) {
            throw new RuntimeException('Could not open the uploaded CSV file.');
        }

        $rawHeaders = fgetcsv($handle);

        if ($rawHeaders === false || $rawHeaders === null) {
            fclose($handle);
            throw new RuntimeException('The CSV file is empty or has no header row.');
        }

        // Normalise headers: trim + lowercase for matching
        $headers  = array_map(fn ($h) => strtolower(trim($h)), $rawHeaders);
        $colIndex = array_search('review_text', $headers, true);

        if ($colIndex === false) {
            fclose($handle);
            throw new RuntimeException(
                'Column "review_text" not found in the CSV. ' .
                'Please ensure your file has a header row with a "review_text" column.'
            );
        }

        $reviews = [];
        $id      = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $text = isset($row[$colIndex]) ? trim($row[$colIndex]) : '';

            if ($text === '') {
                continue;
            }

            $reviews[] = ['id' => $id++, 'text' => $text];
        }

        fclose($handle);

        if (empty($reviews)) {
            throw new RuntimeException('No reviews found in the "review_text" column.');
        }

        return $reviews;
    }
}
