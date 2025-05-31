<?php

declare(strict_types=1);

namespace App\Util;

final class FeatureManager
{
    public function __construct(
        private array $disabledFeatures = [],
    ) {
    }

    public function isEnable(string $feature): bool
    {
        if (KillSwitch::hasFeature($feature)) {
            return KillSwitch::isEnabled($feature);
        }

        return !\in_array($feature, $this->disabledFeatures, true);
    }
}
