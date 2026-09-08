<?php

namespace App\Enums;

use Illuminate\Validation\Rules\Enum;

enum ProcurationActivityType: string
{
    case ALCANCIA = 'Alcancía';
    case ALIANZA = 'Alianza';
    case BOTEO = 'Boteo';
    case DONATIVOS_VARIOS = 'Donativos Varios';
    case FUNDACIONES = 'Fundaciones';
    case NATACION = 'Natación';
    case OBSEQUIO_ENTRE_AMIGOS = 'Obsequio entre Amigos';
    case ORGANISMOS_DE_GOBIERNO = 'Organismos de Gobierno';
    case PADRINOS_GENERALES = 'Padrinos Generales';
    case PROGRAMA_DE_VERANO = 'Programa de Verano';
    case PROYECTO_INTERNO = 'Proyecto Interno';
    case RADIOMARATON = 'Radiomaratón';

    public function label(): string
    {
        return $this->value;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return array_map(fn ($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ], self::cases());
    }
}
