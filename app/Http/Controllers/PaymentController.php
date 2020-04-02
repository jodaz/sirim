<?php

namespace App\Http\Controllers;

use App\PaymentMethod;
use App\PaymentType;
use App\Payment;
use App\Reference;
use App\Receivable;
use App\Taxpayer;
use App\EconomicActivitySettlement;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Services\PaymentService;
use Carbon\Carbon;
use PDF;
use Auth;

class PaymentController extends Controller
{
    /**
     * Payment form type
     * @var $typeform
     */
    private $typeform = 'show';
    private $payment;

    public function __construct(PaymentService $payment)
    {
        $this->payment = $payment;
        $this->middleware('has.role:admin')->only('destroy');
        $this->middleware('has.role:liquidator|collector|admin|liquidation-chief|collection-chief')->only(['index', 'list','show']);
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

    public function listProcessed()
    { 
        $query = Payment::list() 
            ->where('status.name', '=', 'PROCESADA')
            ->whereNull('payments.deleted_at')
            ->orderBy('num', 'DESC');

        return DataTables::of($query)->toJson();
    }

    public function list()
    { 
        $query = Payment::list() 
            ->where('status.name', '=', 'PENDIENTE')
            ->whereNull('payments.deleted_at')
            ->orderBy('payments.processed_at', 'DESC');

        return DataTables::of($query)->toJson();
    }

    public function onlyNull()
    {
        $query = Payment::list()
            ->whereNotNull('payments.deleted_at')
            ->orderBy('id', 'DESC');
        
        return DataTables::of($query)->toJson();
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
        if ($payment->state->id == 1) {
            if (!Auth::user()->can('process.payments')) {
                return redirect('cashbox/payments')
                    ->withError('¡No puede procesar el pago!');
            }
            $this->typeform = 'edit';
        }

        $taxpayer = $payment->settlements->first()->taxpayer;

        return view('modules.cashbox.register-payment')
            ->with('row', $payment)
            ->with('types', PaymentType::exceptNull())
            ->with('methods', PaymentMethod::exceptNull())
            ->with('taxpayer', $taxpayer)
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
        if ($request->input('method') != '3') {
            $reference = $request->input('reference');

            if (empty($reference)){
                return redirect('payments/'.$payment->id)
                        ->withError('¡Faltan datos!');
            }

            $this->payment->makeReference($payment, $reference);
        }

        $data = Array(
            'payment_method_id' => $request->input('method'),
            'observations' => $request->input('observations')
        );
        
        $this->payment->update($payment, $data);

        return redirect('cashbox/payments/'.$payment->id)
            ->withSuccess('¡Factura procesada!');
    }

    public function download(Payment $payment)
    {
        if ($payment->state->id == 1) {
            return redirect('cashbox/payments')
                ->withError('¡La factura no ha sido procesada!');
        }

        $settlement = $payment->receivables->first()->settlement;
        $taxpayer = $settlement->taxpayer;
        $user = $settlement->user;
        $reference = (!!$payment->reference) ? $payment->reference->reference : 'S/N';
        $denomination = (!!$taxpayer->commercialDenomination) ? $taxpayer->commercialDenomination->name : $taxpayer->name;

        $vars = ['user','payment', 'reference', 'taxpayer', 'denomination'];
        
        return PDF::setOptions(['isRemoteEnabled' => true])
            ->loadView('modules.cashbox.pdf.payment', compact($vars)) 
            ->download('factura-'.$payment->id.'.pdf');
   }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payment $payment)
    {
        if (!Auth::user()->hasRole('admin')) {
            return response()->json([
                'message' => '¡Usuario no permitido!'
            ]);
        }

        // Delete receivables and payment but keep settlements
        Receivable::where('payment_id', $payment->id)->delete();
        $payment->delete();

        return redirect()->back()
            ->withSuccess('¡Pago anulado!');
    }
}
