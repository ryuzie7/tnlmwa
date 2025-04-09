<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsageHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'admin_id',
        'custodian_id',
        'purpose',
        'used_at',
        'returned_at',
        'notes',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function custodian()
    {
        return $this->belongsTo(Custodian::class);
    }

    // public function admin()
    // {
    //     return $this->belongsTo(Admin::class);
    // }
}
