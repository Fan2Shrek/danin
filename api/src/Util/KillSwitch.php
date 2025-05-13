<?php

declare(strict_types=1);

namespace App\Util;

final class KillSwitch
{
    private const FILE = '../config/features.php';

    private static array $features = [];

    public static function isEnabled(string $featureName): bool
    {
        return true === self::getFeature($featureName);
    }

    private static function getFeature(string $featureName): bool
    {
        self::loadFeatures();
        if (array_key_exists($featureName, self::$features)) {
            return self::$features[$featureName];
        }

        return false;
    }

    private static function loadFeatures(): void
    {
        if (empty(self::$features)) {
            if (file_exists(self::FILE)) {
                self::$features = require self::FILE;
            } else {
                self::$features = [];
            }
        }
    }
}
