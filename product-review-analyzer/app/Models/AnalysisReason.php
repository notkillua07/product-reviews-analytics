<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalysisReason extends Model
{
    protected $fillable = [
        'analysis_id',
        'type',
        'reason',
        'count',
        'severity',
        'severity_score',
        'severity_explanation',
        'review_ids',
    ];

    protected $casts = [
        'review_ids'     => 'array',
        'severity_score' => 'integer',
        'count'          => 'integer',
    ];

    public function analysis(): BelongsTo
    {
        return $this->belongsTo(Analysis::class);
    }
}
