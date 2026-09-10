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
        <!-- Contenedor centrado para el logo -->
        <div style="text-align: center; width: 100%;">
            <img src="{{ storage_path('app/public/logo.png') }}" class="logo" alt="Logo">
        </div>

        <div class="header-info">
            Instituto de Entrenamiento para Niños con Lesión Cerebral y Trastornos del Aprendizaje A.C.<br>
            Blvd. Jorge Castillo Cabrera No. 2815, Entre Marte y Urano, Col. Colinas del Puerto, C.P. 31550, Cd. Cuauhtémoc, Chih.<br>
            (625)583 0037 y (625)583 0097
        </div>

        <div class="folio">Folio: {{ $folio }}</div>

        <!-- Campos de datos -->
        <div class="field">
            <span class="bold">Concepto:</span> {{ $concept }}
        </div>

        <div class="field">
            <span class="bold">Nombre:</span> {{ $payer_name }}
        </div>

        <div class="field">
            <span class="bold">Beneficiario:</span> {{ $beneficiary }}
        </div>

        <div class="field">
            <span class="bold">Cobertura:</span> {{ $period }}
        </div>

        <div class="field">
            <span class="bold">Fecha:</span> {{ $date }}
        </div>

        <div class="field">
            <span class="bold">Forma de pago:</span> {{ $payment_method }}
        </div>

        <div class="field">
            <span class="bold">Referencia:</span> {{ $ref ?: 'N/A' }}
        </div>

        <div class="amount-box">
            Monto: <span class="bold">$ {{ number_format($amount, 2) }}</span>
        </div>

        <div class="field">
            <span class="bold">Atendido por:</span> {{ $user_name }}
        </div>

        <div class="footer-msg">¡Gracias por apoyar!</div>
    </div>
</body>
</html>
