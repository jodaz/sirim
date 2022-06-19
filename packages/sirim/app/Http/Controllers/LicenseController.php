<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\Eloquent\Builder;
use PDF;
use Auth;
use App\Models\License;
use App\Models\Liquidation;
use App\Models\Payment;
use App\Models\Liqueur;
use App\Models\LiqueurAnnex;
use App\Models\LiqueurParameter;
use App\Models\LiqueurClassification;
use App\Models\AnnexedLiqueur;
use App\Models\Correlative;
use App\Models\CorrelativeNumber;
use App\Models\CorrelativeType;
use App\Models\Year;
use App\Models\Concept;
use App\Models\PetroPrice;
use App\Models\Ordinance;
use App\Models\Taxpayer;
use App\Models\Dismissal;
use App\Models\App\Modelslication;
use App\Models\Requirement;
use App\Models\RequirementTaxpayer;
use Carbon\Carbon;
use App\Models\Signature;

class LicenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = License::orderBy('active', 'ASC');

        // Return responses
        if ($request->wantsJson()) {
            $query->with(['taxpayer', 'ordinance']);

            return DataTables::eloquent($query)->toJson();
        }

        return view('modules.licenses.index');
    }

    public function listBytaxpayer(Taxpayer $taxpayer)
    {
        $query = License::whereTaxpayerId($taxpayer->id);

    	return DataTables::eloquent($query)->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Taxpayer $taxpayer, Request $request)
    {
        if ($request->wantsJson()) {
            $query = License::whereTaxpayerId($taxpayer->id)->where('ordinance_id', '1');;

            return DataTables::eloquent($query)->toJson();
        }

        $correlatives = [
            1 => 'INSTALAR LICENCIA',
            2 => 'RENOVAR LICENCIA'
        ];

        return view('modules.taxpayers.economic-activity-licenses.index')
            ->with('taxpayer', $taxpayer)
            ->with('correlatives', $correlatives);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Taxpayer $taxpayer)
    {
        $correlative = CorrelativeType::find($request->input('correlative'));

        $validator = $this->validateStore($taxpayer, $correlative);

        if ($validator['error']) {
            return redirect()->back()->withError($validator['msg']);
        }

        $this->makeLicense($correlative, $taxpayer);

        return redirect('taxpayers/'.$taxpayer->id.'/economic-activity-licenses')
            ->withSuccess('¡Licencia de actividad económica creada!');
    }

    public function makeLicense(CorrelativeType $type, Taxpayer $taxpayer)
    {
        $currYear = Year::where('year', Carbon::now()->year)->first();
        $correlativeNum = CorrelativeNumber::getNum();
        // Maybe for other kind of licenses, I would inject
        // Ordinances in this method and make licences without searching for
        // a model
        $ordinance = Ordinance::whereDescription('ACTIVIDADES ECONÓMICAS')->first();
        $emissionDate = Carbon::now();
        $expirationDate = $emissionDate->copy()->addYears(1);

        $correlativeNumber = CorrelativeNumber::create([
            'num' => $correlativeNum
        ]);

        $correlative = Correlative::create([
            'year_id' => $currYear->id,
            'correlative_type_id' => $type->id,
            'correlative_number_id' => $correlativeNumber->id
        ]);

        $license = License::create([
            'num' => $correlative->num,
            'emission_date' => $emissionDate,
            'expiration_date' => $expirationDate,
            'ordinance_id' => $ordinance->id,
            'correlative_id' => $correlative->id,
            'taxpayer_id' => $taxpayer->id,
            'representation_id' => $taxpayer->president()->first()->id,
            'user_id' => Auth::user()->id
        ]);

        // Sync economic activities
        $act = $taxpayer->economicActivities;
        $license->economicActivities()->sync($act);
    }

    public function renovate(License $license)
    {
        $currYear = Year::where('year', Carbon::now()->year)->first();
        $ordinance = Ordinance::whereDescription('ACTIVIDADES ECONÓMICAS')->first();
        $emissionDate = Carbon::now();
        $expirationDate = Carbon::now()->endOfYear();

        $correlative = $license->correlative;
        $correlativeNumber = $correlative->correlativeNumber;
        $newCorrelative = Correlative::create([
            'correlative_type_id' => 2,
            'correlative_number_id' => $correlativeNumber->id,
            'year_id' => $currYear->id
        ]);

        $newLicense = License::create([
            'num' => $newCorrelative->num,
            'emission_date' => $emissionDate,
            'expiration_date' => $expirationDate,
            'ordinance_id' => $ordinance->id,
            'correlative_id' => $newCorrelative->id,
            'taxpayer_id' => $license->taxpayer->id,
            'representation_id' => $license->taxpayer->president()->first()->id,
            'user_id' => Auth::user()->id
        ]);
        // Sync economic activities
        $act = $newLicense->taxpayer->economicActivities;
        $newLicense->economicActivities()->sync($act);

        $license->delete();

        return response()->json($newLicense);
    }

    public function dismiss(License $license)
    {
        $dismissedAt = Carbon::now();

        $dismissal = Dismissal::create([
            'user_id' => Auth::user()->id,
            'taxpayer_id' => $license->taxpayer_id,
            'license_id' => $license->id,
            'dismissed_at' => $dismissedAt
        ]);

        $license->taxpayer->delete();
        $license->delete();

        return response()->json($dismissal, 200);
    }

    public function validateStore(Taxpayer $taxpayer, $correlativeType)
    {
        $isValid = [
            'error' => false,
            'msg' => ''
        ];

        if (!$taxpayer->economicActivities->count()) {
           $isValid['error'] = true;
           $isValid['msg'] = '¡El contribuyente no tiene actividades económicas!';
        }

        if (!$taxpayer->president()->count()) {
            $isValid['error'] = true;
            $isValid['msg'] = '¡El contribuyente no tiene un representante (PRESIDENTE) registrado!';
        }

        if ($taxpayer->licenses()->exists()) {
            $isValid['error'] = true;
            $isValid['msg'] = '¡El contribuyente tiene una licencia activa!';
        }

        return $isValid;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Taxpayer $taxpayer
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, License $license)
    {
        if ($request->wantsJson()) {
            $data = $license->load(
                'user',
                'taxpayer',
                'representation.person',
                'ordinance',
            );

            if ($license->ordinance->description == 'ACTIVIDADES ECONÓMICAS') {
                $data->load('economicActivities');
            }

            return response()->json($data);
        }
        return view('modules.licenses.show')
            ->with('row', $license);
    }

    public function download(License $license)
    {
        $taxpayer = $license->taxpayer;
        $num = preg_replace("/[^0-9]/", "", $taxpayer->rif);
        $correlative = $license->correlative;
        $licenseCorrelative = $correlative->correlativeType->description.
                             $correlative->year->year.'-'
                             .$correlative->correlativeNumber->num;

        $representation = $license->representation->person->name;
        $signature = Signature::latest()->first();
        $qrLicenseString = 'Nº: '.$license->num.', Registro: '.$num.', Empresa:'.$taxpayer->name;

        $vars = ['license', 'taxpayer', 'num', 'representation', 'licenseCorrelative', 'signature', 'qrLicenseString'];
        $license->update(['downloaded_at' => Carbon::now()]);

        return PDF::loadView('modules.licenses.pdf.economic-activity-license', compact($vars))
            ->stream('Licencia '.$taxpayer->rif.'.pdf');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\License  $License
     * @return \Illuminate\Http\Response
     */
    public function edit(License $License)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\License  $License
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, License $License)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\License  $License
     * @return \Illuminate\Http\Response
     */
    public function destroy(License $License)
    {
        //
    }





    /**
     * Liqueur Licenses Functions.
     */


    public function listLicenseLiqueur(Request $request)
    {
        $query = License::where('ordinance_id', '6');


        // Return responses
        if ($request->wantsJson()) {
            $query->with(['taxpayer', 'ordinance']);

            return DataTables::eloquent($query)->toJson();
        }

        return view('modules.liqueur-licenses.index');
    }

    public function showLicenseLiqueur(Request $request, License $license)
    {

        $liqueur = Liqueur::whereLicenseId($license->id)->first();

        return view('modules.liqueur-licenses.show')
            ->with('row', $license)
            ->with('liqueur', $liqueur);
    }


    public function createLicenceLiqueur(Taxpayer $taxpayer, Request $request)
    {
        if ($request->wantsJson()) {

            $licenses = License::whereTaxpayerId($taxpayer->id)->where('ordinance_id', '6')->with("liqueurs")->get();

            foreach($licenses as $license){

               $liqueur = Liqueur::whereLicenseId($license->id)->first();

                if($liqueur){

                   $liquidation = $liqueur->liquidations->first();

                   if ($license->active == false) {               

                       if($liquidation->status_id == 2 && $liquidation->liquidation_type_id == 1 ){

                            $license->update([
                                'active' => true
                            ]);
                        }
                    }
                }
            }

            $query = License::whereTaxpayerId($taxpayer->id)->where('ordinance_id', '6')->where('active', true);

            return DataTables::eloquent($query)->toJson();
        }


        $requirement = RequirementTaxpayer::whereTaxpayerId($taxpayer->id)->where('requirement_id', '1')->first();

        //dd($requirement);

        $correlatives = [
            1 => 'INSTALAR LICENCIA',
            2 => 'RENOVAR LICENCIA'
        ];


        $hours = [
            '07:00 AM' => '07:00 AM',
            '08:00 AM' => '08:00 AM',
            '09:00 AM' => '09:00 AM',
            '10:00 AM' => '10:00 AM',
            '11:00 AM' => '11:00 AM',
            '12:00 M' => '12:00 M',
            '01:00 PM' => '01:00 PM',
            '02:00 PM' => '02:00 PM',
            '03:00 PM' => '03:00 PM',
            '04:00 PM' => '04:00 PM',
            '05:00 PM' => '05:00 PM',
            '06:00 PM' => '06:00 PM',
            '07:00 PM' => '07:00 PM',
            '08:00 PM' => '08:00 PM',
            '09:00 PM' => '09:00 PM'
        ];

        $days = [
            'Lunes' => 'Lunes',
            'Martes' => 'Martes',
            'Miércoles' => 'Miércoles',
            'Jueves' => 'Jueves',
            'Viernes' => 'Viernes',
            'Sábado' => 'Sábado',
            'Domingo' => 'Domingo'
        ];

        $boolean = [
            true => 'Si',
            false => 'No'
        ];

        return view('modules.taxpayers.liqueur-licenses.index')
            ->with('taxpayer', $taxpayer)
            ->with('correlatives', $correlatives)
            ->with('requirement', $requirement)
            ->with('hours', $hours)
            ->with('days', $days)
            ->with('boolean', $boolean)
            ->with('liqueurParameters', LiqueurParameter::pluck('description', 'id'))
            ->with('liqueurAnnexes', AnnexedLiqueur::pluck('name', 'id'));
    }



    public function storeLiqueurLicense(Request $request, Taxpayer $taxpayer)
    {
        $correlative = CorrelativeType::find($request->input('correlative'));

        /*$validator = $this->validateStoreLiqueurLicense($taxpayer, $correlative);

        if ($validator['error']) {
            return redirect()->back()->withError($validator['msg']);
        }*/

        $this->makeLiqueurLicense($request, $correlative, $taxpayer);

        return redirect('taxpayers/'.$taxpayer->id.'/liqueur-licenses')
            ->withSuccess('¡Licencia de expendio creada!');
    }








    /*public function validateStoreLiqueurLicense(Taxpayer $taxpayer, $correlativeType)
    {
        $isValid = [
            'error' => false,
            'msg' => ''
        ];

        if (!$taxpayer->economicActivities->count()) {
           $isValid['error'] = true;
           $isValid['msg'] = '¡El contribuyente no tiene actividades económicas!';
        }

        if (!$taxpayer->president()->count()) {
            $isValid['error'] = true;
            $isValid['msg'] = '¡El contribuyente no tiene un representante (PRESIDENTE) registrado!';
        }

        if ($taxpayer->licenses()->exists()) {
            $isValid['error'] = true;
            $isValid['msg'] = '¡El contribuyente tiene una licencia activa!';
        }

        return $isValid;
    }*/


    public function makeLiqueurLicense($request, CorrelativeType $type, Taxpayer $taxpayer)
    {
        $currYear = Year::where('year', Carbon::now()->year)->first();
        $correlativeNum = CorrelativeNumber::getNum();
        // Maybe for other kind of licenses, I would inject
        // Ordinances in this method and make licences without searching for
        // a model
        $ordinance = Ordinance::whereDescription('BEBIDAS ALCOHÓLICAS')->first();
        $emissionDate = Carbon::now();
        $expirationDate = $emissionDate->copy()->addYears(1);

        $concept = Concept::whereCode('21')->first();

        $petro = PetroPrice::latest()->first()->value;

        $idParameter = $request->input('liqueurParameter');

        $liqueur_parameter = LiqueurParameter::whereId($idParameter)->first();

        $liqueurClassification= LiqueurClassification::whereId($liqueur_parameter->liqueur_classification_id)->first();

        $liqueurAbbreviature = $liqueurClassification->abbreviature;

        $amount = $petro*$liqueur_parameter->authorization_registry_amount;

        $correlativeNumber = CorrelativeNumber::create([
            'num' => $correlativeNum
        ]);

        $correlative = Correlative::create([
            'year_id' => $currYear->id,
            'correlative_type_id' => $type->id,
            'correlative_number_id' => $correlativeNumber->id
        ]);


        $license = License::create([
            'num' => $liqueurAbbreviature.'-'.License::getNewNum().'-BERM',
            'emission_date' => $emissionDate,
            'expiration_date' => $expirationDate,
            'ordinance_id' => $ordinance->id,
            'correlative_id' => $correlative->id,
            'taxpayer_id' => $taxpayer->id,
            'representation_id' => $taxpayer->president()->first()->id,
            'user_id' => Auth::user()->id,
            'active' => false
        ]);

        $liquidation = Liquidation::create([
            'num' => Liquidation::getNewNum(),
            'object_payment' =>  $concept->name.' - AÑO '.$currYear->year,
            'amount' => $amount,
            'liquidable_type' => Liquidation::class,
            'concept_id' => $concept->id,
            'liquidation_type_id' => $concept->liquidation_type_id,
            'status_id' => 1,
            'taxpayer_id' => $taxpayer->id
        ]);

        $payment = Payment::create([
            'status_id' => 1,
            'user_id' => Auth::user()->id,
            'amount' => $amount,
            'payment_method_id' => 1,
            'payment_type_id' => 1,
            'taxpayer_id' => $taxpayer->id
        ]);

        $payment->liquidations()->sync($liquidation);

        $hourtring = 'De '.$request->input('start-day').' a '.$request->input('finish-day').' desde '.$request->input('start-hour').' hasta '.$request->input('finish-hour');

        $liqueur = Liqueur::create([
            'work_hours' => $hourtring,
            'is_mobile' => $request->input('is_mobile'),
            'liqueur_parameter_id' =>  $request->input('liqueurParameter'),
            'representation_id' => $taxpayer->president()->first()->id,
            'license_id' => $license->id
        ]);

        $liqueurannex = LiqueurAnnex::create([
            'annex_id' => $request->input('liqueurAnnex'),
            'liqueur_id' => $liqueur->id
        ]);

        $liqueur->liquidations()->sync($liquidation);
    }



    public function renovateLiqueurLicense(License $license)
    {
        $currYear = Year::where('year', Carbon::now()->year)->first();
        $ordinance = Ordinance::whereDescription('BEBIDAS ALCOHÓLICAS')->first();
        $emissionDate = Carbon::now();
        $expirationDate = Carbon::now()->endOfYear();

        $correlative = $license->correlative;
        $correlativeNumber = $correlative->correlativeNumber;
        $newCorrelative = Correlative::create([
            'correlative_type_id' => 2,
            'correlative_number_id' => $correlativeNumber->id,
            'year_id' => $currYear->id
        ]);

        $newLicense = License::create([
            'num' => $newCorrelative->num,
            'emission_date' => $emissionDate,
            'expiration_date' => $expirationDate,
            'ordinance_id' => $ordinance->id,
            'correlative_id' => $newCorrelative->id,
            'taxpayer_id' => $license->taxpayer->id,
            'representation_id' => $license->taxpayer->president()->first()->id,
            'user_id' => Auth::user()->id
        ]);
        // Sync economic activities

        /*$act = $newLicense->taxpayer->economicActivities;
        $newLicense->economicActivities()->sync($act);*/

        $license->delete();

        return response()->json($newLicense);
    }

    public function downloadLiqueurLicense(License $license)
    {
        $taxpayer = $license->taxpayer;
        $num = preg_replace("/[^0-9]/", "", $taxpayer->rif);
        $correlative = $license->correlative;
        $licenseCorrelative = $correlative->correlativeType->description.
                             $correlative->year->year.'-'
                             .$correlative->correlativeNumber->num;

        $representation = $license->representation->person->name;
        $signature = Signature::latest()->first();

        $liqueur = Liqueur::whereLicenseId($license->id)->first();

        $liqueurAnnex = LiqueurAnnex::whereLiqueurId($liqueur->id)->first();

        $annexLiqueur = AnnexedLiqueur::whereId($liqueurAnnex->annex_id)->first();

        $qrLicenseString = 'Nº: '.$license->num.', Registro: '.$num.', Empresa:'.$taxpayer->name;

        $vars = ['license', 'taxpayer', 'num', 'representation', 'licenseCorrelative', 'signature', 'qrLicenseString', 'liqueur', 'annexLiqueur'];
        $license->update(['downloaded_at' => Carbon::now()]);

        return PDF::loadView('modules.liqueur-licenses.pdf.liqueur-license', compact($vars))
            ->stream('Licencia '.$license->num.'.pdf');
    }


}
