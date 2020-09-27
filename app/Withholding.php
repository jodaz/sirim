<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as Auditable;
use OwenIt\Auditing\Auditable as Audit;
use App\Traits\PrettyAmount;
use App\Traits\PrettyTimestamps;

class Withholding extends Model implements Auditable 
{
    use SoftDeletes, Audit, PrettyAmount, PrettyTimestamps;

    protected $table = 'withholdings';

    protected $fillable = [
        'user_id',
        'affidavit_id',
        'amount'
    ];

    protected $appends = [ 'pretty_amount' ];

    public function affidavit()
    {
        return $this->belongsTo(Affidavit::class);
    }

    public function taxpayer()
    {
        return $this->hasOneThrough(Taxpayer::class, Affidavit::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function liquidation()
    {
        return $this->hasOne(Liquidation::class);
    }

    public function payment()
    {
        return $this->belongsToMany(Payment::class, Liquidation::class);
    }

    public function NullWithholding()
    {
      return $this->hasOne(NullWithholding::class);
    }
}
