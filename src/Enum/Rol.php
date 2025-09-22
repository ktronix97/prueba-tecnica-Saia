<?php

namespace App\Enum;

enum Rol: string
{
    case ADMIN = 'ROLE_ADMIN';
    case USUARIO = 'ROLE_USER';
    
    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Administrador',
            self::USUARIO => 'Usuario',
        };
    }
    
    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}