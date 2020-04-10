@extends('layouts.template')

@section('subheader__title', 'Cuentas por cobrar')

@section('title', 'Cuentas por cobrar')

@section('content')
<div class="row" style="margin-top: 20px;">
    <div class="col-lg-12">
      <div class="kt-portlet">
        <div class="kt-portlet__body">
          <table id="tReceivables" class="table table-bordered table-striped datatables" style="text-align: center">
            <thead>
              <tr>
                <th width="10%">ID</th>
                <th width="10%">RIF</th>
                <th width="50%">Razón social</th>
                <th width="10%">Monto</th>
                <th width="10%">Acciones</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
</div>

@endsection