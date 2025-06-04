<?php

declare(strict_types=1);

namespace App\Enum;

enum GameEnum: string
{
    case THE_BINDING_OF_ISAAC = 'tboi';

    public function getName(): string
    {
        return match ($this) {
            self::THE_BINDING_OF_ISAAC => 'The Binding of Isaac',
        };
    }
}
