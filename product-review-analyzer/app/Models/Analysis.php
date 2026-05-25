<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'product_reasons',
        'shipping_reasons',
        'reviews_data',
    ];

    protected $casts = [
        'product_reasons'  => 'array',
        'shipping_reasons' => 'array',
        'reviews_data'     => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
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
