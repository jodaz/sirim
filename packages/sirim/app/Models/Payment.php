<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as Auditable;
use OwenIt\Auditing\Auditable as Audit;
use Carbon\Carbon;
use App\Traits\NewValue;
use App\Traits\PrettyAmount;
use App\Traits\PrettyTimestamps;
use App\Traits\CheckDelinquencyStatus;
use App\Traits\PaymentUtils;
use App\Fine;

class Payment extends Model implements Auditable
{
    use Audit, SoftDeletes, NewValue, PrettyAmount, PrettyTimestamps, CheckDelinquencyStatus, PaymentUtils;

    protected $table = 'payments';

    protected $guarded = [];

    protected $casts = [ 'amount' => 'float' ];

    public function updateAmount()
    {
        $amount = $this->liquidations->sum('amount');

        return $this->update([ 'amount' => $amount ]);
    }

    public function cancellations()
    {
        return $this->morphMany(Cancellation::class, 'cancellable');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function references()
    {
        return $this->hasMany(Reference::class);
    }

    public function taxpayer()
    {
        return $this->belongsTo(Taxpayer::class)->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function affidavit()
    {
        return $this->liquidations()
            ->whereLiquidationTypeId(3);
    }

    public function liquidations()
    {
        return $this->belongsToMany(Liquidation::class, 'payment_liquidation');
    }

    public function fines()
    {
        return $this->belongsToMany(Fine::class, Liquidation::class);
    }

    public function movements()
    {
        return $this->hasMany(Movement::class);
    }

    public function credits()
    {
        return $this->hasMany(Credit::class)->withTrashed();;
    }

    public function deductions()
    {
        return $this->hasMany(Deduction::class)->withTrashed();;
    }
}
