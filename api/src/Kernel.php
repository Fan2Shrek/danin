<?php

namespace App;

use App\Service\Transport\GameTransport;
use App\Service\Transport\GameTransportInterface;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function build(ContainerBuilder $container): void
    {
        $container->setAlias(GameTransportInterface::class, GameTransport::class);
    }
}
