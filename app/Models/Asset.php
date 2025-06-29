<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Log;
use App\Models\User;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_number',
        'code',
        'brand',
        'model',
        'type',
        'condition',
        'location',
        'building_name',
        'fund',
        'price',
        'acquired_at',
        'previous_custodian',
        'custodian',
        'latitude',
        'longitude',
    ];

    public function logs()
    {
        return $this->hasMany(Log::class);
    }

    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            Log::class,
            'asset_id',
            'id',
            'id',
            'user_id'
        )->latestOfMany();
    }

    public function getNameAttribute()
    {
        return trim($this->brand . ' ' . $this->model);
    }

    public function getConditionStatusAttribute(): string
    {
        $year = \Carbon\Carbon::parse($this->acquired_at)->year;
        $current = now()->year;
        $age = $current - $year;

        if ($age <= 3) {
            return 'Good';
        } elseif ($age <= 5) {
            return 'Fair';
        } else {
            return 'Poor';
        }
    }
}
