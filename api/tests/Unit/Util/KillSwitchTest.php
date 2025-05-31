<?php

declare(strict_types=1);

namespace App\Tests\Unit\Util;

use App\Util\KillSwitch;
use PHPUnit\Framework\TestCase;

final class KillSwitchTest extends TestCase
{
    public function testIsEnableActive(): void
    {
        $this->assertTrue(DebugKillSwitch::isEnabled('true_feature'));
    }

    public function testIsEnableDeactive(): void
    {
        $this->assertFalse(DebugKillSwitch::isEnabled('false_feature'));
    }
}

class DebugKillSwitch extends KillSwitch
{
    protected const FILE = __DIR__.'/../../Resources/config/features.php';
}
