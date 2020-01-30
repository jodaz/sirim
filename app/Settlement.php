<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Settlement extends Model
{
    protected $table = [
        'num',
        'license_id',
        'payment_id',
        'ordinance_id'
    ];

    public function license()
    {
        return $this->belongsTo(License::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function ordinance()
    {
        return $this->belongsTo(Ordinance::class);
    }
}