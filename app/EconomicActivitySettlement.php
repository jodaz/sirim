<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EconomicActivitySettlement extends Model
{
    protected $table = 'economic_activity_settlements';

    protected $fillable = [
        'economic_activity_id',
        'settlement_id',
        'month_id'
    ];

    public function economicActivity()
    {
        return $this->belongsTo(EconomicActivity::class);
    }

    public function month()
    {
        return $this->belongsTo(Month::class);
    }

    public function settlement()
    {
        return $this->belongsTo(Settlement::class);
    }
}