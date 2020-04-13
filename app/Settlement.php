<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as Auditable;
use OwenIt\Auditing\Auditable as Audit;

class Settlement extends Model implements Auditable
{
    use SoftDeletes;
    use Audit;

    protected $table = 'settlements';

    protected $guarded = [];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function fine()
    {
        return $this->belongsTo(Fine::class);
    }

    public function affidavit()
    {
        return $this->belongsTo(Affidavit::class);
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function getTotalAmountAttribute($value)
    {
        return number_format($this->amount, 2, ',', '.');
    }

    public static function newNum()
    {
        $lastNum = self::withTrashed()
            ->orderBy('num','DESC')
            ->first()
            ->num;

        $newNum = str_pad($lastNum + 1, 8, '0', STR_PAD_LEFT);
        return $newNum;
    }   
}
