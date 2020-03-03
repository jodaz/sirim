<?php

namespace App\Http\Controllers;

use App\PaymentMethod;
use App\PaymentType;
use App\Payment;
use App\Reference;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\Payments\PaymentsFormRequest;
use PDF;
use Auth;

class PaymentController extends Controller
{
    /**
     * Payment form type
     * @var $typeform
     */
    private $typeform = 'show';

    public function __construct()
    {
        $this->middleware('has.role:admin')->only('destroy');
        $this->middleware('has.role:liquidator|collector|admin')->only(['index', 'list','show']);
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('modules.cashbox.list-payments');
    }

    public function list()
    {
        $query = Payment::with(['state'])
            ->orderBy('created_at', 'DESC');

        return DataTables::eloquent($query)->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function show(Payment $payment)
    {
        if (Auth::user()->hasRole('collector') && $payment->state->id == 1) {
            $this->typeform = 'edit';
        }
        
        return view('modules.cashbox.register-payment')
            ->with('row', $payment)
            ->with('types', PaymentType::exceptNull())
            ->with('methods', PaymentMethod::exceptNull())
            ->with('typeForm', $this->typeform);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Payment $payment)
    {
        $payment = Payment::find($payment->id);
        $payment->state_id = 2;
        $payment->payment_type_id = 2;
        $payment->payment_method_id = $request->input('method');

        if ($request->input('method') != '3') {
            $reference = $request->input('reference');
            
            if (empty($reference)){
                return redirect('payments/'.$payment->id)
                        ->withError('¡Faltan datos!');
            }

            $reference = Reference::create([
                'reference' => $request->input('reference'),
                'account_id' => 1, // For later use, select account
                'payment_id' => $payment->id
            ]);
        }
        $payment->save();

        return redirect('cashbox/payments')
            ->withSuccess('¡Factura procesada!');
    }

    public function download(Payment $payment)
    {
        if ($payment->state->id == 1) {
            return redirect('cashbox/payments')
                ->withError('¡La factura no ha sido procesada!');
        }

        $taxpayer = $payment->receivables->first()->settlement->taxpayer;
        $billNum = str_pad($payment->id, 8, '0', STR_PAD_LEFT);
        $reference = (!!$payment->reference) ? $payment->reference->reference : 'S/N';
        
        $denomination = (!!$taxpayer->commercialDenomination) ? $taxpayer->commercialDenomination->name : $taxpayer->name;
        $pdf = PDF::LoadView('modules.cashbox.pdf.payment', compact(['payment', 'billNum', 'reference', 'taxpayer', 'denomination']));
        return $pdf->stream('Licencia '.$payment->id.'.pdf');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payment $payment)
    {
        //
    }
}
