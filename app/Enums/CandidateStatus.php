<?php

namespace App\Enums;

enum CandidateStatus: string
{
    case PENDING          = 'pendiente';
    case REJECTED         = 'rechazado';
    case ACCEPTED         = 'aceptado';
    case READY            = 'listo';
    case SCHEDULED        = 'programado';
    case ACTIVE           = 'activo';
    case GRADUATED        = 'graduado';
    case DECEASED         = 'fallecido';
    case EX_ENLAC         = 'exenlac';
    case INACTIVE         = 'inactivo';
    case LIFE_PROOF       = 'prueba_vida';
    case TEMPORARY_PERMIT = 'permiso_temporal';

    /**
     * Retorna la etiqueta legible (Label) para la interfaz.
     */
    public function label(): string
    {
        return match($this) {
            self::PENDING          => 'Pendiente',
            self::REJECTED         => 'Rechazado',
            self::ACCEPTED         => 'Pendiente de ingresar',
            self::READY            => 'Listo para ingresar',
            self::SCHEDULED        => 'Ingreso programado',
            self::ACTIVE           => 'Activo',
            self::GRADUATED        => 'Graduado',
            self::DECEASED         => 'Fallecido',
            self::EX_ENLAC         => 'Ex-Enlac',
            self::INACTIVE         => 'Inactivo',
            self::LIFE_PROOF       => 'Prueba de vida',
            self::TEMPORARY_PERMIT => 'Permiso temporal',
        };
    }

    /**
     * Opcional: Retorna un color de Tailwind o Bootstrap para cada estado.
     */
    public function color(): string
    {
        return match($this) {
            self::PENDING   => 'gray',
            self::ACCEPTED  => 'blue',
            self::ACTIVE    => 'green',
            self::REJECTED, self::DECEASED => 'red',
            default         => 'indigo',
        };
    }
}