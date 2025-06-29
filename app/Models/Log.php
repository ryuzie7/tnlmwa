<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Log extends Model
{
    protected $fillable = [
        'action',
        'model_type',
        'model_id',
        'changes',
        'user_id',
        'asset_id',
        'date',
        'usage_type',
        'notes',
        'status',
        'applied_at'
    ];

    protected $casts = [
        'changes' => 'array',
        'date' => 'date',
        'applied_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    // Optional: model referenced by polymorphic log
    public function model()
    {
        return $this->morphTo();
    }
}
