<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <!-- CSRF Token -->
        <title> Factura </title>
        <style>
           body {
                font-family: sans-serif, serif;
                font-size: 14px;
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
            }
            table, td, th {
                border: 1px #000 solid;
            }
            td {
                font-size: 11px;
                padding: 4px;
            }
            table {
                border-collapse: collapse;
                width: 100%;                
                margin-top: 5px;
            }
            .tables {
                display:block;
            }
            .bill-info {
                width: 100%;
                clear: both;
                font-weight: bold;
            }
            .col-bill-info {
                float: left;
                width: 50%;
            }
            .total-amount {
                text-align: right;
            }
            .miscellaneus {
                font-size: 10px;
            }
            caption {
                font-weight: bold;
            }
        </style>
    </head>

    <body>
        <div class="header">
            <div class="sumatLOGO">
                <img src="{{ asset('assets/images/sumat.png') }}" height="90px" width="230px" alt="sumatlogo"/>
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
                <img src="{{ asset('assets/images/logo.png') }}" height="70px" width="130px" alt="logo" />
            </div>
        </div>
        <div class="tables">
           <table style="text-align: center">
                <caption>REPORTE DE PAGOS PROCESADOS</caption>
                <thead>
                  <tr>
                    <th width="10%">NO. FACTURA</th>
                    <th width="15%">RIF</th>
                    <th width="60%">RAZÓN SOCIAL</th>
                    <th width="15%">MONTO</th>
                  </tr>
                </thead>
                <tbody>
                @foreach($payments as $payment)
                 <tr>
                    <td>{{ $payment->num }}</td> 
                    <td>{{ $payment->receivables->first()->settlement->taxpayer->rif }}</td>   
                    <td>{{ $payment->receivables->first()->settlement->taxpayer->name }}</td>
                    <td>{{ $payment->totalAmount }}</td>
                </tr>
                @endforeach   
             </table>
            <br>
            <div class="bill-info">
                <div class="col-bill-info">
                    FECHA: {{ $dateFormat }}
                </div>
                <div class="col-bill-info">
                    <div class="total-amount">
                        MONTO TOTAL PROCESADO: {{ $total }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>