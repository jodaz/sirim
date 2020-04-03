<?php

namespace App\Services;

use App\Affidavit;
use App\Settlement;
use App\TaxUnit;

class AffidavitService
{
    /**
     * Create economic activity settlements for every activity
     * @param $settlement
     */
    public function make(Settlement $settlement)
    {
        $activities = $settlement->taxpayer->economicActivities;
        $data = Array();
        
        foreach($activities as $activity) {
            array_push($data, Array(
                'amount' => 0.00,
                'brute_amount' => 0.00,
                'settlement_id' => $settlement->id,
                'economic_activity_id' => $activity->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ));
        }

        Affidavit::insert($data);
    }

    public function update(Settlement $settlement, array $amounts)
    {
        $settlements = $settlement->affidavits;
        $bruteAmounts = $amounts;
        $totalAmounts = Array();

        foreach($settlements as $settlement) {
            $amount = array_shift($bruteAmounts);

            if (($settlements->count() > 2) && ($amount == 0.00)) {
                $updateSettlement = $this->calculateTax($settlement, $amount);
            } else {
                $updateSettlement = $this->calculateTax($settlement, $amount, true);
            } 

            array_push($totalAmounts, $updateSettlement->amount);
        }
        
        return array_sum($totalAmounts);
    }

    public function updateByGroup(Settlement $settlement, float $amount)
    {
        $settlements = $settlement->affidavits;
        $maxDeclaration = $settlements->first();

        if ($amount == 0.00) {
            foreach ($settlements as $settlement) {
                if ($settlement->economicActivity->min_tax > $maxDeclaration->economicActivity->min_tax) {
                    $maxDeclaration = $settlement;
                }
            }
        } else {
            foreach ($settlements as $settlement) {
                if ($settlement->economicActivity->aliquote > $maxDeclaration->economicActivity->aliquote) {
                    $maxDeclaration = $settlement;
                }
            }
        }

        return $this->calculateTax($maxDeclaration, $amount, true)->amount;
    }

    public function calculateTax(Affidavit $affidavit, $amount, $update = false)
    {
        $total = 0.00;
        $activity = $affidavit->economicActivity;
        
        if ($activity->code == '123456') {
            $total = $amount * $activity->aliquote / 100;
        } else {
            if ($update) {
                $taxUnit = TaxUnit::latest()->first();
                $total = $activity->aliquote * $amount / 100;
                $minTax = $taxUnit->value * $activity->min_tax;
                
                if ($total < $minTax || $amount == 0.00) {
                    $total = $minTax;
                }
            }
        }
        
        $settlement = Affidavit::find($affidavit->id);
        $settlement->update([
            'amount' => $total,
            'brute_amount' => $amount
        ]);
        return $settlement;
    }
}