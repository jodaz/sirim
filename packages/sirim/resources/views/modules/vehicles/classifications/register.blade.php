@extends('cruds.form')

@section('title', 'Nueva Clasificación de Vehículo')

@section('form')
    <div class="row">
        <div class="col-lg-12">
            <div class="kt-portlet">
                @if ($typeForm == 'create')
                    {!! Form::open(['url' => route('vehicle-classifications.store', [$vehicleClassification->id]), 'class' => 'kt-form kt-form--label-right', 'autocomplete' => 'off', 'enctype' => 'multipart/form-data', 'id' => 'form']) !!}
                @else
                    {!! Form::model($row, ['route' => ['vehicle-classifications.update', $row->id], 'method' => 'patch', 'autocomplete' => 'off', 'class' => 'kt-form kt-form--label-right', 'enctype' => 'multipart/form-data', 'id' => 'form']) !!}
                @endif
                    <div class="kt-portlet__body">
                        <div class="form-group col-lg-12">
                            <div class="kt-heading kt-heading--md">
                            Datos de la Clasificación de Vehículo
                            </div>
                         </div>
                       <div class="form-group row">
                            <div class="col-lg-6">
                                <label>Descripción <span class="text-danger">*</span></label>

                                {!! Form::text('name', old('name', @$row->name), ['class' => 'form-control', "onkeyup" => "upperCase(this);", "required"]) !!}

                                @error('name')
                                    <div class="text text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-lg-6">
                                <label>Cantidad <span class="text-danger">*</span></label>

                                {!! Form::number('amount', old('amount', @$row->amount), ['class' => 'form-control', 'pattern' => '[0-9]+([\.,][0-9]+)?', 'step' => '0.01', 'id' => 'amount']) !!}

                                @error('amount')
                                    <div class="text text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-lg-6">
                                <label>Parámetro <span class="text-danger">*</span></label>

                                {!!
                                    Form::select('vehicleParameter', $vehicleParameter, old('vehicleParameter', @$row->vehicle_parameter_id), [
                                        'class' => 'col-md-12 select2',
                                        'placeholder' => 'SELECCIONE',
                                        'id' => 'vehicle_parameter_id'
                                    ])
                                !!}

                                @error('vehicleParameter')
                                <div class="text text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-lg-6">
                                <label>Método de Pago <span class="text-danger">*</span></label>

                                {!!
                                    Form::select('chargingMethod', $chargingMethod, old('chargingMethod', @$row->charging_method_id), [
                                        'class' => 'col-md-12 select2',
                                        'placeholder' => 'SELECCIONE',
                                        'id' => 'charging_method_id'
                                    ])
                                !!}

                                @error('chargingMethod')
                                <div class="text text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                    </div>

                    <div class="kt-portlet__foot">
                        <div class="kt-form__actions">
                            <div class="row">
                                <div class="col-lg-12">
                                    <a href="{{ URL::previous() }}" class="btn btn-danger"><i class="flaticon-cancel"></i> Cancelar</a>

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