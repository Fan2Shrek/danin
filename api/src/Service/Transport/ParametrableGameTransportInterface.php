<?php

declare(strict_types=1);

namespace App\Service\Transport;

interface ParametrableGameTransportInterface extends GameTransportInterface
{
    /**
     * @return array<string>
     */
    public function getTransportSettings(): array;
}
