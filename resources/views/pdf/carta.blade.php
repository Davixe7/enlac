<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 1.5cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            line-height: 1.2;
            color: #333;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .logo {
            width: 120px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .text-justify {
            text-align: justify;
        }
        .bold {
            font-weight: bold;
        }
        .margin-top {
            margin-top: 30px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
        }
        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .bg-light {
            background-color: #f2f2f2;
        }
        .signature-section {
            margin-top: 50px;
            width: 100%;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 300px;
            margin: 0 auto;
            padding-top: 5px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            font-size: 9pt;
            color: #555;
            text-align: center;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <img src="{{ storage_path('app/public/carta.jpg') }}" width="100%" alt="logo" style="margin-bottom: 10px;">
    <table class="header-table">
        <tr>
            <td class="text-right">
                Cd. Cuauhtémoc, Chih., a {{$fecha}}
            </td>
        </tr>
    </table>

    <div class="margin-top">
        <span class="bold">Dirigido a: {{ $destinatario }}</span><br>
        <span class="bold">Presente</span>
    </div>

    <div class="margin-top text-justify">
        Por este conducto el Comité de Becas de Instituto ENLAC le informa el monto de
        la beca con la que se apoyará al beneficiario <span class="bold">{{$beneficiario}}</span>
        por el periodo de {{$periodo}}
    </div>

    <table class="data-table">
        <thead>
            <tr class="bg-light">
                <th colspan="2">(Nombre del Programa)</th>
                <th>Costo Mensual</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="2">{{$programa}}</td>
                <td class="text-right">$ {{$costo_mensual}}</td>
            </tr>
            <tr class="bg-light">
                <td colspan="3" class="bold">Detalle de Aportaciones:</td>
            </tr>
            <tr>
                <td>Beca ENLAC</td>
                <td class="text-center bold">{{$cuota_enlac_porcentaje}}%</td>
                <td class="text-right bold">$ {{$cuota_enlac}}.00</td>
            </tr>
            <tr>
                <td>Aportación Padrinos</td>
                <td class="text-center bold">{{ $cuota_padrinos_porcentaje }} %</td>
                <td class="text-right bold">$ {{ $cuota_padrinos}}</td>
            </tr>
            <tr>
                <td>Cuota de Recuperación de Padres de Familias</td>
                <td class="text-center bold">{{$cuota_padres_porcentaje}}%</td>
                <td class="text-right bold">$ {{$cuota_padres}}</td>
            </tr>
        </tbody>
    </table>

    <div class="text-justify">
        La familia ENLAC ha sido testigo del éxito de este programa y de los logros que se
        pueden alcanzar a través de él. Por ello, la institución hace un esfuerzo
        significativo al otorgar este apoyo al beneficiario. En respuesta, solicitamos su compromiso para cumplir
        a cabalidad con el Reglamento de Padres de Familia de nuestra institución. <br>
        El Comité de Becas evaluará periódicamente el cumplimiento de los puntos del
        reglamento por parte de los becarios. En caso de incumplimiento, se les notificará con antelación
        sobre la posible modificación o pérdida de la beca.
    </div>

    <div class="margin-top">
        Agradecemos su compromiso para mantener al beneficiario en el Instituto ENLAC.
    </div>

    <div class="margin-top text-center">
        <span class="bold">ATENTAMENTE</span> <br>
        <span class="bold">Comité de Becas de Instituto ENLAC</span>
    </div>

    <div class="signature-section text-center">
        <br><br>
        <div class="signature-line"></div>
        <div style="font-size: 10pt;">Nombre, firma y fecha de enterado</div>
    </div>

    <div class="footer" style="color: #1a3a5e;">
        Blvd. Ing. Jorge Castillo Cabrera no. 2815 Col. Colinas del Puerto, C.P. 31550<br>
        Tels. (625) 58.3.00.37, (625) 58.3.00.97 Cd. Cuauhtémoc, Chih.<br>
        www.enlac.org
    </div>

</body>
</html>
