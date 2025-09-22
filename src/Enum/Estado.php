<?php

namespace App\Enum;

enum Estado: string {
    case PENDIENTE = 'pendiente';
    case EN_PROGRESO = 'en progreso';
    case COMPLETADA = 'completada';
}
