<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'type',
        'acquired_at',
        'condition',
        'location',
        'custodian_id',
        'created_by',
    ];

    public function custodian()
    {
        return $this->belongsTo(Custodian::class);
    }

    public function usageHistories()
    {
        return $this->hasMany(UsageHistory::class);
    }
}
