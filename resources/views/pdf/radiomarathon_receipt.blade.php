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
        <!-- Logo Centrado -->
        <div style="text-align: center; width: 100%;">
            <img src="{{ storage_path('app/public/logo.png') }}" class="logo" alt="Logo">
        </div>

        <div class="header-info">
            Instituto de Entrenamiento para Niños con Lesión Cerebral y Trastornos del Aprendizaje A.C.<br>
            Blvd. Jorge Castillo Cabrera No. 2815, Entre Marte y Urano, Col. Colinas del Puerto, C.P. 31550, Cd. Cuauhtémoc, Chih.
            (625)583 0037 y (625)583 0097
        </div>

        <div class="folio">Folio: {{ $donation->folio_number }}</div>

        <!-- ========================================== -->
        <!-- CASO 1: BOTEO                              -->
        <!-- ========================================== -->
        @if($donation->source === 'boteo')

            <div class="field"><span class="bold">No. Bote:</span> {{ $donation->boteo_can_number ?? 'N/A' }}</div>
            <div class="field">
                <span class="bold">Donante:</span>
                {{ $donation->donor_name ?? ($donation->full_name ?? ($donation->donor->full_name ?? ($donation->sponsor->name ?? 'Público General'))) }}
            </div>
            <div class="field"><span class="bold">Responsable del Bote:</span> {{ $donation->boteo_responsible_name ?? 'N/A' }}</div>
            <div class="field"><span class="bold">Fuente:</span> Boteo</div>
            <div class="field"><span class="bold">Tipo de Donativo:</span> {{ $donation->donation_type ?? 'N/A' }}</div>

            <div class="amount-box">
                Monto: <span class="bold">$ {{ number_format($donation->amount, 2) }}</span>
            </div>

            <div class="field"><span class="bold">Fecha de Pago:</span> {{ \Carbon\Carbon::parse($donation->payment_date)->format('d/m/Y') }}</div>
            <div class="field"><span class="bold">Forma de Pago:</span> {{ $donation->payment_method ?? 'N/A' }}</div>
            <div class="field"><span class="bold">Moneda:</span> {{ $donation->currency ?? 'MXN' }}</div>

            <div class="field">
                <span class="bold">Recibí Dólares:</span>
                {{ ($donation->currency === 'DLLS' || $donation->currency === 'USD') ? 'Sí' : 'No' }}
            </div>

            <div class="field">
                <span class="bold">Clave Radiomaratón:</span>
                @if($donation->radiomarathonKey)
                    {{ $donation->radiomarathonKey->code }} - {{ $donation->radiomarathonKey->concept }}
                @else
                    {{ $donation->radiomarathon_key_id ?? 'N/A' }}
                @endif
            </div>
            <div class="field"><span class="bold">Referencia:</span> {{ $donation->reference ?? 'N/A' }}</div>

        <!-- ========================================== -->
        <!-- CASO 2: LLAMADAS, OTROS Y GENERAL          -->
        <!-- ========================================== -->
        @else

            <div class="field">
                <span class="bold">Donante:</span>
                {{ $donation->donor_name ?? ($donation->full_name ?? ($donation->donor->full_name ?? ($donation->sponsor->name ?? 'Público General'))) }}
            </div>

            <div class="field">
                <span class="bold">Empresa:</span>
                {{ $donation->sponsor->company_name ?? ($donation->donor->company_name ?? '') }}
            </div>

            <div class="field">
                <span class="bold">Fuente:</span>
                {{ ucfirst($donation->source ?? 'N/A') }}
            </div>

            <div class="field">
                <span class="bold">Tipo de Donativo:</span>
                {{ $donation->donation_type ?? 'N/A' }}
            </div>

            <div class="amount-box">
                Monto: <span class="bold">$ {{ number_format($donation->amount, 2) }}</span>
            </div>

            <div class="field"><span class="bold">Fecha de Pago:</span> {{ \Carbon\Carbon::parse($donation->payment_date)->format('d/m/Y') }}</div>
            <div class="field"><span class="bold">Forma de Pago:</span> {{ $donation->payment_method ?? 'N/A' }}</div>
            <div class="field"><span class="bold">Moneda:</span> {{ $donation->currency ?? 'MXN' }}</div>

            @if(($donation->currency ?? 'MXN') === 'MXN')
                <div class="field"><span class="bold">Tipo de cambio:</span> N/A</div>
            @else
                <div class="field"><span class="bold">Tipo de cambio:</span> {{ $donation->exchange_rate }}</div>
                <div class="field"><span class="bold">Equivalencia en Pesos:</span> $ {{ number_format($donation->equivalent_amount_mxn, 2) }}</div>
            @endif

            <div class="field">
                <span class="bold">Clave Radiomaratón:</span>
                @if($donation->radiomarathonKey)
                    {{ $donation->radiomarathonKey->code }} - {{ $donation->radiomarathonKey->concept }}
                @else
                    {{ $donation->radiomarathon_key_id ?? 'N/A' }}
                @endif
            </div>

            <div class="field"><span class="bold">Referencia:</span> {{ $donation->reference ?? 'N/A' }}</div>

            <div class="field">
                <span class="bold">Recibo Deducible:</span>
                {{ !empty($donation->has_tax_receipt) ? 'Sí' : 'No' }}
            </div>

        @endif

        <div class="footer-msg">¡Gracias por apoyar!</div>
    </div>
</body>
</html>
