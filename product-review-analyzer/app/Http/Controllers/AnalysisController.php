<?php

namespace App\Http\Controllers;

use App\Models\Analysis;
use App\Services\AnalysisApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class AnalysisController extends Controller
{
    public function __construct(private AnalysisApiService $apiService) {}

    public function index()
    {
        $user = Auth::user();

        $analyses = Analysis::where('user_id', $user->id)
            ->latest()
            ->get();

        $totalAnalyses   = $analyses->count();
        $totalReviews    = $analyses->sum('total_reviews');
        $avgPositiveRate = $totalReviews > 0
            ? round(($analyses->sum('positive_count') / $totalReviews) * 100, 1)
            : 0;

        return view('home', compact('analyses', 'totalAnalyses', 'totalReviews', 'avgPositiveRate'));
    }

    public function create()
    {
        return view('analysis.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_name' => ['required', 'string', 'max:255'],
            'csv_file'     => ['required', 'file', 'mimes:csv,txt', 'max:20480'],
        ]);

        try {
            $result = $this->apiService->analyze(
                $request->input('product_name'),
                $request->file('csv_file')
            );
        } catch (Throwable $e) {
            return back()
                ->withInput()
                ->withErrors(['api' => $e->getMessage()]);
        }

        Analysis::create([
            'user_id'              => Auth::id(),
            'product_name'         => $request->input('product_name'),
            'total_reviews'        => $result['total_reviews'],
            'positive_count'       => $result['positive_count'],
            'negative_count'       => $result['negative_count'],
            'top_negative_reasons' => $result['top_negative_reasons'],
            'reviews_data'         => $result['reviews_data'],
        ]);

        return redirect()->route('home')->with('success', 'Analysis complete!');
    }

    public function show(Analysis $analysis)
    {
        abort_if($analysis->user_id !== Auth::id(), 403);

        return view('analysis.show', compact('analysis'));
    }

    public function destroy(Analysis $analysis)
    {
        abort_if($analysis->user_id !== Auth::id(), 403);

        $analysis->delete();

        return redirect()->route('home')->with('success', 'Analysis deleted.');
    }
}
