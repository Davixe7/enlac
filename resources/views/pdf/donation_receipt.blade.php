<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            /* Definimos el tamaño exacto solicitado */
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
        .field { margin-bottom: 3px; }
        .bold { font-weight: bold; }
        .amount-box {
            font-size: 10pt;
            margin: 10px 0;
            padding: 5px;
            text-align: center;
        }
        .footer-msg { text-align: center; font-weight: bold; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Contenedor centrado para el logo -->
        <div style="text-align: center; width: 100%;">
            <img src="{{ storage_path('app/public/logo.png') }}" class="logo" alt="Logo">
        </div>

        <div class="header-info">
            Instituto de Entrenamiento para Niños con Lesión Cerebral y Trastornos del Aprendizaje A.C.<br>
            Blvd. Jorge Castillo Cabrera No. 2815, Entre Marte y Urano, Col. Colinas del Puerto, C.P. 31550, Cd. Cuauhtémoc, Chih.
            (625)583 0037 y (625)583 0097
        </div>

        <div class="folio">Folio: {{ $donation->folio_number }}</div>

        <!-- Campos de datos -->
        <div class="field">
            <span class="bold">Donante:</span>
            {{ $donation->donor->full_name ?? ($donation->sponsor->name ?? 'N/A') }}
        </div>

        <div class="field">
            <span class="bold">Razón Social:</span>
            {{ $donation->fiscalRecord->tax_name ?? ($donation->sponsor->company_name ?? 'N/A') }}
        </div>

        <div class="field"><span class="bold">Tipo de Actividad:</span> {{ $donation->activity_type }}</div>
        <div class="field"><span class="bold">Actividad:</span> {{ $donation->procurationActivity->name ?? 'N/A' }}</div>
        <div class="field"><span class="bold">Concepto:</span> {{ $donation->concept }}</div>
        <div class="field"><span class="bold">Fecha:</span> {{ \Carbon\Carbon::parse($donation->payment_date)->format('d/m/Y') }}</div>
        <div class="field"><span class="bold">Forma de pago:</span> {{ $donation->payment_method }}</div>
        <div class="field"><span class="bold">Referencia:</span> {{ $donation->reference }}</div>

        <div class="amount-box">
            Monto: <span class="bold">$ {{ number_format($donation->amount, 2) }}</span>
        </div>

        <div class="field"><span class="bold">Moneda:</span> {{ $donation->currency }}</div>

        <!-- Lógica condicional para Tipo de Cambio y Equivalencia -->
        @if($donation->currency === 'MXN')
            <div class="field"><span class="bold">Tipo de cambio:</span> N/A</div>
        @else
            <div class="field"><span class="bold">Tipo de cambio:</span> {{ $donation->exchange_rate }}</div>
            <div class="field"><span class="bold">Equivalencia en pesos:</span> $ {{ number_format($donation->equivalent_amount_mxn, 2) }}</div>
        @endif

        <div class="field"><span class="bold">No. Recibo Deducible:</span> {{ $donation->tax_receipt_number ?? 'N/A' }}</div>

        <div class="footer-msg">¡Gracias por apoyar!</div>
    </div>
</body>
</html>
