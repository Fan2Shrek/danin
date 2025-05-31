<?php

declare(strict_types=1);

namespace App;

use App\DependencyInjection\Compiler\RemoveDefinitionPass;
use App\Domain\Command\Security\CheckTOTPHandler;
use App\Domain\Command\User\EnableTOTPHandler;
use App\Security\TOTPFactory;
use App\Util\FeatureManager;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * This class is the kernel for self-hosted environments.
 *
 * This kernel remove all dependencies on non self usable fonctionalities.
 *
 * @author Pierre Ambroise<pierre27.ambroise@gmail.com>
 */
class SelfHostedKernel extends DaninKernel implements CompilerPassInterface
{
    private const array SERVICE_IDS_REMOVE = [
        TOTPFactory::class,
        EnableTOTPHandler::class,
        CheckTOTPHandler::class,
    ];

    protected function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new RemoveDefinitionPass(), PassConfig::TYPE_REMOVE);
        parent::build($container);

        $container->getDefinition(FeatureManager::class)
            ->setArgument('$disabledFeatures', $this->getDisabledFeatures())
            ->setAutowired(false)
        ;
    }

    public function process(ContainerBuilder $container): void
    {
        foreach (static::SERVICE_IDS_REMOVE as $serviceId) {
            $container->getDefinition($serviceId)->addTag('container.remove', [
                'reason' => 'This service is not usable in self-hosted environments.',
            ]);
        }
    }

    protected function getDisabledFeatures(): array
    {
        return [
            'totp',
        ];
    }
}
