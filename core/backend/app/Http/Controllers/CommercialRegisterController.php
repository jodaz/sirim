<?php

namespace App\Http\Controllers;

use App\CommercialRegister;
use App\Http\Requests\CommercialRegisters\CommercialRegistersCreateFormRequest;
use App\Taxpayer;
use Illuminate\Http\Request;

class CommercialRegisterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Taxpayer $taxpayer)
    {
        return view('modules.commercial-registers.register')
            ->with('taxpayer', $taxpayer)
            ->with('typeForm', 'create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CommercialRegistersCreateFormRequest $request, Taxpayer $taxpayer)
    {
        $data = [
            'num' => $request->input('num'),
            'volume' => $request->input('volume'),
            'case_file' => $request->input('case_file'),
            'start_date' => $request->input('start_date'),
        ];
        
        $taxpayer->commercialRegister()
            ->create($data);

        return redirect('taxpayers/'.$taxpayer->id)
            ->withSuccess('¡Registro comercial añadido!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CommercialRegister  $commercialRegister
     * @return \Illuminate\Http\Response
     */
    public function show(CommercialRegister $commercialRegister)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CommercialRegister  $commercialRegister
     * @return \Illuminate\Http\Response
     */
    public function edit(CommercialRegister $commercialRegister)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CommercialRegister  $commercialRegister
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CommercialRegister $commercialRegister)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CommercialRegister  $commercialRegister
     * @return \Illuminate\Http\Response
     */
    public function destroy(CommercialRegister $commercialRegister)
    {
        //
    }
}