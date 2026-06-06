<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Analysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'product_name',
        'total_reviews',
        'positive_count',
        'negative_count',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(AnalysisReview::class)->orderBy('review_order_id');
    }

    public function reasons(): HasMany
    {
        return $this->hasMany(AnalysisReason::class)->orderByDesc('severity_score');
    }

    public function productReasons(): HasMany
    {
        return $this->hasMany(AnalysisReason::class)
            ->where('type', 'product')
            ->orderByDesc('severity_score');
    }

    public function shippingReasons(): HasMany
    {
        return $this->hasMany(AnalysisReason::class)
            ->where('type', 'shipping')
            ->orderByDesc('severity_score');
    }

    public function positivePercent(): float
    {
        if ($this->total_reviews === 0) return 0;
        return round(($this->positive_count / $this->total_reviews) * 100, 1);
    }

    public function negativePercent(): float
    {
        if ($this->total_reviews === 0) return 0;
        return round(($this->negative_count / $this->total_reviews) * 100, 1);
    }
}
