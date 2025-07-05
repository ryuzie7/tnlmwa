<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogRequest extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'asset_id',
        'user_id',
        'date',
        'usage_type',
        'notes',
        'original_location',
        'new_location',
        'brand',
        'model',
        'action',
        'original_data',
        'status',
        'requested_at',
        'reviewed_at',
        'applied_at',
        'swap_group_id',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'original_data' => 'array',
        'date' => 'date',
        'requested_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'applied_at' => 'datetime',
    ];

    /**
     * Get the user who submitted the request.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the asset related to the request.
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class)->withTrashed();
    }
}
