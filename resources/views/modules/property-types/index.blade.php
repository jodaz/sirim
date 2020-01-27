@extends('layouts.template')

@section('title', 'Control de Tipos de Inmuebles')

@section('breadcrumbs')
    {{ Breadcrumbs::render('settings/property-types') }}
@endsection

@section('content')

  <div class="row" style="margin-top: 20px;">
    <div class="col-lg-12">
      <div class="card card-primary card-outline">
        <div class="card-header alert alert-danger">
          <div class="row">
            <h5 class="m-0">Control de Tipos de Inmuebles <b>(</b> <a href="{{ Route("property-types".'.create') }}" title="Registrar comunidad">
                <span>Registrar</span>
              </a><b>)</b></h5>
          </div>
        </div>

        <div class="card-body">
          <table id="tPropertyTypes" class="table table-bordered table-striped datatables" style="text-align: center">
            <thead>
              <tr>
                <th width="30%">Clasificación</th>
                <th width="30%">Denominación</th>
                <th width="20%">Monto</th>
                <th width="10%">Método de cobro</th>
                <th width="10%">Acciones</th>
              </tr>
            </thead>
          </table>
        </div>

      </div>
    </div>
  </div>

@endsection
