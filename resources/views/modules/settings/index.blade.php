@extends('layouts.template')

@section('title', 'Configuraciones')

@section('content')
	<div class="col-xl-12">
        <div class="kt-portlet kt-portlet--height-fluid">
            <div class="kt-portlet__head kt-portlet__head--lg">
                <div class="kt-portlet__head-label">
                    <span class="kt-portlet__head-icon">
                        <i class="kt-font-brand flaticon-settings-1"></i>
                    </span>
                    <h3 class="kt-portlet__head-title">
                        Configuraciones
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-toolbar">
                        <a class="btn btn-outline-dark btn-pill btn-sm btn-icon btn-icon-md" title="Abrir nuevo año" id="openNewYear">
                            <i class="flaticon2-rocket-1"></i>
                        </a> 
                    </div>
                </div>
            </div>

            <div class="kt-portlet__body">
                <div class="kt-notification">
                    <a class="kt-notification__item" href="{{ url('settings/economic-sectors') }}">
                        <div class="kt-notification__item-icon">
                            <i class="flaticon2-graphic"></i>
                        </div>
                        <div class="kt-notification__item-details">
                            <div class="kt-notification__item-title">
                                Sectores Económicos 
                            </div>
                        </div>
                    </a>
                    <a class="kt-notification__item" href="{{ url('settings/reductions') }}">
                        <div class="kt-notification__item-icon">
                            <i class="flaticon-price-tag"></i>
                        </div>
                        <div class="kt-notification__item-details">
                            <div class="kt-notification__item-title">
                                Descuentos
                            </div>
                        </div>
                    </a>
                    <a class="kt-notification__item" href="{{ url('settings/ordinances') }}">
                        <div class="kt-notification__item-icon">
                            <i class="flaticon2-file-1"></i>
                        </div>
                        <div class="kt-notification__item-details">
                            <div class="kt-notification__item-title">
                                Ordenanzas
                            </div>
                        </div>
                    </a>
                    <a class="kt-notification__item" href="{{ url('settings/tax-units') }}">
                        <div class="kt-notification__item-icon">
                            <i class="flaticon-coins"></i>
                        </div>
                        <div class="kt-notification__item-details">
                            <div class="kt-notification__item-title">
                                Unidades Tributarias
                            </div>
                        </div>
                    </a>
                    <a class="kt-notification__item" href="{{ url('settings/property-types') }}">
                        <div class="kt-notification__item-icon">
                            <i class="flaticon2-protection"></i>
                        </div>
                        <div class="kt-notification__item-details">
                            <div class="kt-notification__item-title">
                               Tipos de inmuebles
                            </div>
                        </div>
                    </a>
                    <a class="kt-notification__item" href="{{ url('settings/personal-firms') }}">
                        <div class="kt-notification__item-icon">
                            <i class="flaticon2-zig-zag-line-sign"></i>
                        </div>
                        <div class="kt-notification__item-details">
                            <div class="kt-notification__item-title">
                               Firmas personales 
                            </div>
                        </div>
                    </a>
                    <a class="kt-notification__item" href="{{ url('settings/concepts') }}">
                        <div class="kt-notification__item-icon">
                            <i class="flaticon2-shopping-cart-1"></i>
                        </div>
                        <div class="kt-notification__item-details">
                            <div class="kt-notification__item-title">
                                Conceptos de recaudación
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
