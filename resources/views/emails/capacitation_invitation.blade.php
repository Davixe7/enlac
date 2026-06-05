<p>Buen día,</p>
<p>Te notificamos que has sido invitado a la próxima Capacitación ENLAC, a continuación los detalles:</p>

<ul>
    <li><strong>Capacitación:</strong> {{ $capacitation->name }}</li>
    <li><strong>Fecha:</strong> {{ $capacitation->date }}</li>
    <li><strong>Hora Inicio:</strong> {{ $capacitation->start_time }}</li>
    <li><strong>Hora Fin:</strong> {{ $capacitation->end_time }}</li>
    <li><strong>Lugar:</strong> {{ $capacitation->location }}</li>
    @if($capacitation->description)
        <li><strong>Descripción:</strong> {{ $capacitation->description }}</li>
    @endif
</ul>

<p>¡Te esperamos!</p>
