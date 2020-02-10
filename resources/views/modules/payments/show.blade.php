@extends('cruds.form')

@section('title', 'Pagos')

@section('form')
    <div class="row">
        <div class="col-lg-12">
            <div class="kt-portlet">
                <div class="kt-portlet__head alert alert-danger">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            Pago nº {{ @$row->num }}

                            @section('breadcrumbs')
                                {{ Breadcrumbs::render('payments/edit') }}
                            @endsection
                        </h3>
                    </div>
                </div>
                {!! Form::model($row, ['route' => ["payments".'.update', $row->id], 'method' => 'patch', 'autocomplete' => 'off', 'class' => 'kt-form kt-form--label-right', 'enctype' => 'multipart/form-data', 'id' => 'form']) !!}
                    <div class="kt-portlet__body">
                        <table class="table table-bordered table-striped datatables">
                            <tr>
                                <td>Nro. de Liquidación</td>
                                <td>Concepto</td>
                                <td>Monto</td>
                            </tr>
                            @foreach ($row->settlements as $settlement)
                                <tr>
                                    <td>{{ $settlement->num }}</td>
                                    <td>{{ $settlement->concept->description }}</td>
                                    <td>{{ $settlement->amount }}</td>
                                </tr>
                            @endforeach
                        </table>

                        <div class="kt-separator kt-separator--border-solid kt-separator--portlet-fit kt-separator--space-lg"></div>
                        <div class="form-group row">
                            <div class="col-lg-6">
                                {!! Form::select('payment_type', $paymentTypes, [], [
                                        'class' => 'form-control select2',
                                        'placeholder' => ' SELECCIONE '
                                    ])
                                !!}
                            </div>
                            <div class="col-lg-6">
                                {!! Form::select('bank_account', $bankAccounts, [], [
                                        'class' => 'form-control select2',
                                        'placeholder' => ' SELECCIONE '
                                    ])
                                !!}
                            </div>
                        </div>
                    </div>

                    <div class="kt-portlet__foot">
                        <div class="kt-form__actions">
                            <div class="row">
                                <div class="col-lg-12">
                                    <a href="{{ url('payments') }}" class="btn btn-secondary" id="cancel"><i class="fas fa-reply"></i> Regresar</a>

                                    @if($typeForm == 'update')
                                        <button type="submit" class="btn btn-primary" id="send">
                                            <i class="flaticon-refresh"></i>
                                            Actualizar
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-primary" id="send">
                                            <i class="fas fa-save"></i>
                                            Registrar
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                {!! Form::close() !!}
      <!--end::Form-->
        </div>
    <!--end::Portlet-->
    </div>
@endsection