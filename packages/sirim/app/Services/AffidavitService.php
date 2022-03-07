<?php

namespace App\Services;

use App\Models\Affidavit;
use App\Models\EconomicActivityAffidavit;
use App\Models\TaxUnit;
use App\Models\PetroPrice;
use Carbon\Carbon;
use App\Models\Month;

class AffidavitService
{
    public function update(Affidavit $affidavit, array $amounts)
    {
        $month = $affidavit->month;
        $affidavits = $affidavit->economicActivityAffidavits;
        $bruteAmounts = $amounts;
        $totalAmounts = Array();

        foreach($affidavits as $affidavit) {
            $amount = array_shift($bruteAmounts);

            if (($affidavits->count() > 2) && ($amount == 0.00)) {
                $updateSettlement = $this->calculateTax($month, $affidavit, $amount);
            } else {
                $updateSettlement = $this->calculateTax($month, $affidavit, $amount, true);
            }

            array_push($totalAmounts, $updateSettlement->amount);
        }

        return array_sum($totalAmounts);
    }

    public function updateByGroup(Affidavit $affidavit, float $amount)
    {
        $month = $affidavit->month;
        $affidavits = $affidavit->economicActivityAffidavits;
        $maxDeclaration = $affidavits->first();

        if ($amount == 0.00) {
            foreach ($affidavits as $affidavit) {
                if ($affidavit->economicActivity->min_tax > $maxDeclaration->economicActivity->min_tax) {
                    $maxDeclaration = $affidavit;
                }
            }
        } else {
            foreach ($affidavits as $affidavit) {
                if ($affidavit->economicActivity->aliquote > $maxDeclaration->economicActivity->aliquote) {
                    $maxDeclaration = $affidavit;
                }
            }
        }

        return $this->calculateTax($month, $maxDeclaration, $amount, true)->amount;
    }

    public function calculateTax(Month $month, EconomicActivityAffidavit $affidavit, $amount, $update = false)
    {
        $total = 0.00;
        $activity = $affidavit->economicActivity;

        if ($activity->code == '123456' && $amount != 0.00) {
            $total = $amount * $activity->aliquote / 100;
        } else {
            if ($update) {
                $unit = ($activity->charging_method_id == 1) 
                    ? TaxUnit::latest()->first() : $this->getPetroPrice($month);
                $total = $activity->aliquote * $amount / 100;
                $minTax = $unit->value * $activity->min_tax;

                if ($total < $minTax || $amount == 0.00) {
                    $total = $minTax;
                }
            }
        }

        $affidavit->update([
            'amount' => $total,
            'brute_amount' => $amount
        ]);

        return $affidavit;
    }

    protected function getPetroPrice($month)
    {
        $date = Carbon::parse($month->start_period_at)->subDays(1)->format('Y-m');
        $rate = PetroPrice::whereLike('created_at', $date)->latest()->first();

        if (!$rate) {
            $rate = PetroPrice::latest()->first();
        }

        return $rate;
    }
}
