<?php

namespace App\Http\Controllers;

use App\Models\Analysis;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Services\AnalysisApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class AnalysisController extends Controller
{
    public function __construct(private AnalysisApiService $apiService) {}

    public function index(Request $request)
    {
        $user = Auth::user();

        // Unfiltered totals for stats
        $allAnalyses     = Analysis::where('user_id', $user->id)->get();
        $totalAnalyses   = $allAnalyses->count();
        $totalReviews    = $allAnalyses->sum('total_reviews');
        $avgPositiveRate = $totalReviews > 0
            ? round(($allAnalyses->sum('positive_count') / $totalReviews) * 100, 1)
            : 0;

        // Filtered analyses for the history table
        $query = Analysis::where('user_id', $user->id)
            ->with('product.category')
            ->latest();

        if ($q = $request->get('q')) {
            $query->where('product_name', 'like', "%{$q}%");
        }

        if ($categoryId = $request->get('category')) {
            $query->whereHas('product', fn ($q) => $q->where('category_id', $categoryId));
        }

        $analyses   = $query->get();
        $products   = Product::where('user_id', $user->id)
            ->with('category')
            ->withCount('analyses')
            ->latest()
            ->get();
        $categories = ProductCategory::where('user_id', $user->id)->latest()->get();

        return view('home', compact(
            'analyses', 'totalAnalyses', 'totalReviews', 'avgPositiveRate',
            'products', 'categories'
        ));
    }

    public function create()
    {
        $products   = Product::where('user_id', Auth::id())->with('category')->latest()->get();
        $categories = ProductCategory::where('user_id', Auth::id())->latest()->get();

        return view('analysis.create', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $isAjax = $request->ajax() || $request->expectsJson();

        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'csv_file'   => ['required', 'file', 'mimes:csv,txt', 'max:20480'],
        ]);

        $product = Product::find($request->product_id);
        abort_if($product->user_id !== Auth::id(), 403);

        $product->load('category');

        set_time_limit(300);

        try {
            $result = $this->apiService->analyze(
                $product->name,
                $request->file('csv_file'),
                $product->category?->name,
            );
        } catch (Throwable $e) {
            if ($isAjax) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 422);
            }
            return back()->withInput()->withErrors(['api' => $e->getMessage()]);
        }

        DB::transaction(function () use ($result, $product) {
            $analysis = Analysis::create([
                'user_id'       => Auth::id(),
                'product_id'    => $product->id,
                'product_name'  => $product->name,
                'total_reviews' => $result['total_reviews'],
                'positive_count'=> $result['positive_count'],
                'negative_count'=> $result['negative_count'],
            ]);

            foreach ($result['reviews_data'] as $r) {
                $analysis->reviews()->create([
                    'review_order_id'  => $r['id'],
                    'text'             => $r['text'],
                    'label'            => $r['label'],
                    'confidence'       => $r['confidence'] ?? null,
                    'confidence_level' => $r['confidence_level'] ?? null,
                ]);
            }

            foreach ($result['product_reasons'] as $r) {
                $analysis->reasons()->create([
                    'type'                 => 'product',
                    'reason'               => $r['reason'],
                    'count'                => $r['count'] ?? 0,
                    'severity'             => $r['severity'] ?? null,
                    'severity_score'       => $r['severity_score'] ?? null,
                    'severity_explanation' => $r['severity_explanation'] ?? null,
                    'review_ids'           => $r['review_ids'] ?? null,
                ]);
            }

            foreach ($result['shipping_reasons'] as $r) {
                $analysis->reasons()->create([
                    'type'                 => 'shipping',
                    'reason'               => $r['reason'],
                    'count'                => $r['count'] ?? 0,
                    'severity'             => $r['severity'] ?? null,
                    'severity_score'       => $r['severity_score'] ?? null,
                    'severity_explanation' => $r['severity_explanation'] ?? null,
                    'review_ids'           => $r['review_ids'] ?? null,
                ]);
            }
        });

        if ($isAjax) {
            return response()->json([
                'success'  => true,
                'redirect' => route('home'),
                'message'  => 'Analysis complete!',
            ]);
        }

        return redirect()->route('home')->with('success', 'Analysis complete!');
    }

    public function show(Analysis $analysis)
    {
        abort_if($analysis->user_id !== Auth::id(), 403);

        $analysis->load(['product.category', 'productReasons', 'shippingReasons', 'reviews']);

        return view('analysis.show', compact('analysis'));
    }

    public function destroy(Analysis $analysis)
    {
        abort_if($analysis->user_id !== Auth::id(), 403);

        $analysis->delete();

        return redirect()->route('home')->with('success', 'Analysis deleted.');
    }
}
