<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ordinance extends Model
{
    use SoftDeletes;

    protected $table = 'applications';

    protected $fillable = [
        'law',
        'value',
        'description',
        'charging_method_id',
        'ordinance_type_id'
    ];

    public function chargingMethod()
    {
        return $this->belongsTo(ChargingMethod::class);
    }

    public function ordinanceType()
    {
        return $this->belongsTo(OrdinanceType::class);
    }

    public function getCreatedAtAttribute($value)
    {
        return date('d/m/Y', strtotime($value));
    }
}