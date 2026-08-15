<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            size: 8cm 13cm;
            margin: 0.5cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 8pt;
            line-height: 1.1;
            color: #000;
        }
        .container { width: 100%; }
        .logo { width: 60px; display: block; margin: 0 auto 5px auto; }
        .header-info { text-align: center; font-size: 7pt; margin-bottom: 10px; }
        .folio { font-weight: bold; text-align: right; margin-bottom: 10px; }
        .field { margin-bottom: 4px; }
        .bold { font-weight: bold; }
        .address-box {
            font-size: 8pt;
            white-space: pre-wrap;
            word-wrap: break-word;
            margin-bottom: 6px;
            padding-left: 2px;
        }
        .footer-msg { text-align: center; font-weight: bold; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Logo Centrado -->
        <div style="text-align: center; width: 100%;">
            <img src="{{ storage_path('app/public/logo.png') }}" class="logo" alt="Logo">
        </div>

        <div class="header-info">
            Instituto de Entrenamiento para Niños con Lesión Cerebral y Trastornos del Aprendizaje A.C.<br>
            Blvd. Jorge Castillo Cabrera No. 2815, Entre Marte y Urano, Col. Colinas del Puerto, C.P. 31550, Cd. Cuauhtémoc, Chih.<br>
            (625)583 0037 y (625)583 0097
        </div>

        <div class="folio">Folio Llamada: {{ $call->id }}</div>

        <div class="field">
            <span class="bold">Donante:</span> {{ $call->donor_name ?? 'N/A' }}
        </div>

        <div class="field"><span class="bold">Dirección / Señas particulares:</span></div>
        <div class="address-box">{{ $call->address ?? 'N/A' }}</div>

        <div class="field">
            <span class="bold">Cobrador Designado:</span> {{ $call->collector ?? 'N/A' }}
        </div>

        <div class="field">
            <span class="bold">Tipo de Donativo:</span> {{ $call->donation_type ?? 'N/A' }}
        </div>

        <div class="field">
            <span class="bold">Recibo Deducible:</span> {{ !empty($call->has_tax_receipt) ? 'Sí' : 'No' }}
        </div>

        <div class="field" style="margin-top: 6px;">
            <span class="bold">Monto o Descripción del Donativo:</span><br>
            {{ $call->amount ?? 'N/A' }}
        </div>

        <div class="footer-msg">¡Gracias por apoyar!</div>
    </div>
</body>
</html>
