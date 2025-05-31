<?php

declare(strict_types=1);

namespace App\Tests\Unit\Compiler;

use App\DependencyInjection\Compiler\RemoveDefinitionPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class RemoveDefinitionPassTest extends TestCase
{
    public function testProcessRemove()
    {
        $pass = new RemoveDefinitionPass();
        $container = new ContainerBuilder();
        $container
            ->register('test_service')
            ->addTag('container.remove', ['reason' => 'Test removal']);

        $pass->process($container);

        $this->assertFalse($container->hasDefinition('test_service'));
    }

    public function testProcessRemoveOnlyTaggedService()
    {
        $pass = new RemoveDefinitionPass();
        $container = new ContainerBuilder();
        $container
            ->register('test_service')
        ;

        $pass->process($container);

        $this->assertTrue($container->hasDefinition('test_service'));
    }
}
