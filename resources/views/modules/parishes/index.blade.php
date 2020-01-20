@extends('layouts.template')

@section('title', 'Control de Parroquias')

@section('breadcrumbs')
    {{ Breadcrumbs::render('geographic-area/parishes') }}
@endsection

@section('content')

  <div class="row" style="margin-top: 20px;">
    <div class="col-lg-12">
      <div class="card card-primary card-outline">
        <div class="card-header alert alert-danger">
          <div class="row">
            <h5 class="m-0">Control de parroquias</h5>
          </div>
        </div>

        <div class="card-body">
          <table id="tParishes" class="table table-bordered table-striped datatables" style="text-align: center">
            <thead>
              <tr>
                <th width="15%">ID</th>
                <th width="70%">Nombre</th>
                <th width="15%"></th>
              </tr>
            </thead>
          </table>
        </div>

      </div>
    </div>
  </div>

@endsection
