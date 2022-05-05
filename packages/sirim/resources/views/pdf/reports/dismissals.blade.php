<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <style>
            body {
                font-family: 'Helvetica';
                font-size: 15px;
            }
            .header {
                width: 100%;
                font-size: 9px;
                position: relative;
                display: block;
            }
            .header div {
                display: inline-block;
            }
            #mayorLOGO {
                float: right;
                margin-top: -10px;
            }
            table, td, th {
                border: 1px #000 solid;
            }
            td {
                font-size: 12px;
                padding: 2px 1px;
            }
            table {
                border-collapse: collapse;
                width: 100%;
                margin-top: 5px;
            }
            .details td {
                text-align: center;
            }
            .titulo{
                display: block;
                align: center;
                text-align: center;
            }

            #fecha-pdf{
                display: block;
                align: right;
                text-align: right;

            }

            .miscellaneus {
                font-size: 12px;
            }
            caption {
                font-weight: bold;
            }
            th {
                font-size: 10px;
                padding: 3px 1px;
            }
            .row {
                display: block;
                padding-left: 10px;
            }
            .text-center {
                text-align: center;
            }
            .bottom {
                width: 80%;
                /* height: 170px; */
                /* z-index: 1000; */
                text-transform: uppercase;
                font-weight: 700;
                font-size: 12px;
                margin: auto;
                margin-top: 20%;
                /* position: absolute; */
                left: 10%;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <div class="sumatLOGO">
                    <img src="{{ asset('/assets/images/logo_sumat.png') }}" height="90px" width="230px" alt="sumatlogo"/>
                </div>
                <div class="description">
                <p>
                    REPÚBLICA BOLIVARIANA DE VENEZUELA<br>
                    ESTADO SUCRE<br>
                    ALCALDÍA DEL MUNICIPIO BERMÚDEZ<br>
                    SUPERINTENDENCIA MUNICIPAL DE ADMINISTRACIÓN TRIBUTARIA<br>
                    RIF: G-20000222-1<br>
                    DIRECCIÓN: AV. CARABOBO, EDIFICIO MUNICIPAL
                    </p>
                </div>
                <div id="mayorLOGO">
                    <img src="{{ asset('/assets/images/logo_alcaldia.jpg') }}" height="80px" width="130px" alt="logo" />
                </div>
            </div>
            <br>
            <div class="row">
                <p class="titulo">CONSTANCIA DE CESE DE ACTIVIDAD ECONÓMICA</p>
            </div>
            <br>
            <div class="row">
                <p id="fecha-pdf" >{{ date('d-m-Y') }}</p>
            </div>
            <br>
            <div class="row">
            Se hace constar que la Actividad Economica de la Licencia {{ $license->num }}
                cuya Denominacion {{ $taxpayer->name }} y que pertenece a la Razon Social {{$taxpayer->name}} de RIF {{$taxpayer->rif}}.
                Ha Cesado sus actividades a la fecha {{ $dismissal->pretty('dismissed_at', 'd-m-Y') }}.
            </div>

            <div class="bottom text-center">
                <span class="row">{{ $signature->title }}</span>
                <span class="row">superintendente de administración tributaria</span>
                <span class="row">{{ $signature->decree }}</span>
                <span class="row">GACETA MUNICIPAL EXTRAORDINARIA Nº 378 DE FECHA 30-11-2021</span>
            </div>
        </div>
    </body>
</html>