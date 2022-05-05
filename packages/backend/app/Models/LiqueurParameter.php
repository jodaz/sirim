<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiqueurParameter extends Model
{
    use HasFactory;

    protected $table = 'liqueur_parameters';

    protected $fillable = [
        'new_registry_amount',
        'renew_registry_amount',
        'movil',
        'liqueur_classification_id',
        'liqueur_zone_id'
    ];

    public function liqueurs()
    {
        return $this->hasMany(Liqueur::class, 'liqueur_parameter_id');
    }

    public function liqueur_classification()
    {
        return $this->belongsTo(LiqueurClassification::class);
    }

    public function liqueur_zone()
    {
        return $this->belongsTo(LiqueurZone::class);
    }
}