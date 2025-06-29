<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'brand',
        'model',
        'type',
        'condition',
        'location',
        'building_name',
        'fund',
        'price',
        'property_number',
        'previous_custodian',
        'custodian',
        'notes',
        'action',
        'original_data',
        'status',
        'requested_at',
        'approved_at',
    ];

    protected $casts = [
        'original_data' => 'array',
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
