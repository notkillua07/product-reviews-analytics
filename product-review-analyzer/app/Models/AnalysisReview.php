<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalysisReview extends Model
{
    protected $fillable = [
        'analysis_id',
        'review_order_id',
        'text',
        'label',
        'confidence',
        'confidence_level',
    ];

    protected $casts = [
        'confidence' => 'float',
    ];

    public function analysis(): BelongsTo
    {
        return $this->belongsTo(Analysis::class);
    }

    public function confidencePercent(): string
    {
        return $this->confidence !== null
            ? number_format($this->confidence * 100, 1) . '%'
            : '—';
    }
}
