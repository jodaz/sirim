<?php

namespace App\Http\Controllers;

use App\Models\TaxpayerType;
use App\Models\TaxpayerClassification;
use App\Models\Taxpayer;
use App\Models\License;
use App\Models\State;
use App\Models\Community;
use Illuminate\Http\Request;
use App\Http\Requests\Taxpayers\TaxpayerActivitiesFormRequest;
use App\Http\Requests\Taxpayers\TaxpayersCreateRequest;
use App\Http\Requests\Taxpayers\TaxpayersUpdateFormRequest;
use Yajra\DataTables\Facades\DataTables;
use PDF;

class TaxpayerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Taxpayer::latest();
        $results = $request->perPage;

        if ($request->has('filter')) {
            $filters = $request->filter;

            if (array_key_exists('name', $filters)) {
                $query->whereLike('name', $filters['name']);
            }
            if (array_key_exists('address', $filters)) {
                $query->whereLike('address', $filters['address']);
            }
            if (array_key_exists('type', $filters)) {
                $name = $filters['type'];

                $query->whereHas('taxpayerType', function ($q) use ($name) {
                    return $query->whereLike('description', $name);
                });
            }
            if (array_key_exists('classification', $filters)) {
                $name = $filters['classification'];

                $query->whereHas('taxpayerClassification', function ($q) use ($name) {
                    return $query->whereLike('name', $name);
                });
            }
        }

        return $query->paginate($results);
    }

    public function getRepresentations(Taxpayer $taxpayer)
    {
        $query = $taxpayer->representations()->with(['representationType', 'person']);

        return response()->json($query->get());
    }

    public function economicActivities(Taxpayer $taxpayer)
    {
        $query = $taxpayer->economicActivities;

        return $query;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('modules.taxpayers.register')
            ->with('types', TaxpayerType::pluck('description', 'id'))
            ->with('classifications', TaxpayerClassification::pluck('name', 'id'))
            ->with('states', State::pluck('name', 'id'))
            ->with('communities', Community::pluck('name', 'id'))
            ->with('typeForm', 'create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TaxpayersCreateRequest $request)
    {
        $type = $request->input('taxpayer_type_id');

        if (Taxpayer::existsRif($request->input('rif'))) {
            return redirect('taxpayers/create')
                ->withInput($request->input())
                ->withError('¡El RIF '.$request->input('rif').' se encuentra registrado!');
        }

        $taxpayer = Taxpayer::create($request->input());

        return redirect()->route('taxpayers.show', $taxpayer)
            ->withSuccess('¡Contribuyente registrado!');
    }

    public function downloadDeclarations(Taxpayer $taxpayer)
    {
        $denomination = (!!$taxpayer->commercialDenomination) ? $taxpayer->commercialDenomination->name : $taxpayer->name;
        $settlements = $taxpayer->settlements;

        $pdf = PDF::LoadView('modules.taxpayers.pdf.declarations', compact(['taxpayer', 'denomination', 'settlements']));
        return $pdf->stream('Contribuyente '.$taxpayer->rif.'.pdf');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Taxpayer  $taxpayer
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Taxpayer $taxpayer)
    {
        if ($request->wantsJson()) {
            $query = $taxpayer->load('companies');

            return $response->json($query);
        }

        return view('modules.taxpayers.show')
            ->with('row', $taxpayer);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Taxpayer  $taxpayer
     * @return \Illuminate\Http\Response
     */
    public function edit(Taxpayer $taxpayer)
    {
        return view('modules.taxpayers.register')
            ->with('classifications', TaxpayerClassification::pluck('name', 'id'))
            ->with('types', TaxpayerType::pluck('description', 'id'))
            ->with('communities', Community::pluck('name', 'id'))
            ->with('typeForm', 'edit')
            ->with('row', $taxpayer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Taxpayer  $taxpayer
     * @return \Illuminate\Http\Response
     */
    public function update(TaxpayersUpdateFormRequest $request, Taxpayer $taxpayer)
    {
        $taxpayer->update($request->input());

        if ($request->has('personal_firm')) {
            if (!$taxpayer->commercialDenomination()->exists()) {
                $taxpayer->commercialDenomination()->create([
                    'name' => $request->get('personal_firm')
                ]);
            }
            $taxpayer->commercialDenomination->name = $request->get('personal_firm');
            $taxpayer->push();
        }

        return redirect('taxpayers/'.$taxpayer->id)
            ->withSuccess('¡Info. general del contribuyente actualizada!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Taxpayer  $taxpayer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Taxpayer $taxpayer)
    {
        //
    }
}
