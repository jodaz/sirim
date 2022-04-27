<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <!-- CSRF Token -->
        <title> Comprobante Liquidación </title>
        <style>
            @page {
                margin-left: 12px;
                margin-right: 12px;
                margin-top: 12px;
                margin-bottom: 12px;
            }
           body {
                font-family: 'Helvetica';
                font-size: 10px;
            }
            .header {
                width: 100%;
                font-size: 10px;
                position: relative;
                display: block;
                text-align: center;
            }
            .title {
                font-size: 10px;
                text-align: center;

            }
            .information {
                font-size: 10px;
            }
        </style>
    </head>

    <body>
        <div class="header">
               <p>
                ALCALDÍA DEL MUNICIPIO BERMÚDEZ<br>
                SUPERINTENDENCIA MUNICIPAL DE ADMINISTRACIÓN TRIBUTARIA<br>
                RIF: G-20000222-1<br>
                DIRECCIÓN: AV. CARABOBO, EDIFICIO MUNICIPAL
                </p>

        </div>
        <br>
        <p class="title">COMPROBANTE DE LA LIQUIDACIÓN</p>
        <div class="information">
        <p>
            Nº LIQUIDACIÓN: {{ $liquidation->num }}<br>
            FECHA: {{ $liquidation->created_at }}<br>
            MONTO: {{ $liquidation->pretty_amount }}<br>
            USUARIO: {{$liquidation->liquidable->user->full_name}}
        </p>
        </div>


    </body>
</html>
