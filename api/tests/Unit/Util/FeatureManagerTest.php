<?php

declare(strict_types=1);

namespace App\Tests\Unit\Util;

use App\Util\FeatureManager;
use PHPUnit\Framework\TestCase;

final class FeatureManagerTest extends TestCase
{
    public function testIsEnableActive(): void
    {
        $featureManager = new FeatureManager();
        $this->assertTrue($featureManager->isEnable('true_feature'));
    }

    public function testIsEnableDeactive(): void
    {
        $featureManager = new FeatureManager([
            'false_feature',
        ]);
        $this->assertFalse($featureManager->isEnable('false_feature'));
    }
}
