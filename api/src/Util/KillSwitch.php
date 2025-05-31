<?php

declare(strict_types=1);

namespace App\Util;

class KillSwitch
{
    protected const FILE = '../config/features.php';

    private static array $features = [];

    public static function isEnabled(string $featureName): bool
    {
        return true === self::getFeature($featureName);
    }

    public static function hasFeature(string $featureName): bool
    {
        self::loadFeatures();

        return \array_key_exists($featureName, self::$features);
    }

    private static function getFeature(string $featureName): bool
    {
        self::loadFeatures();
        if (\array_key_exists($featureName, self::$features)) {
            return self::$features[$featureName];
        }

        return false;
    }

    private static function loadFeatures(): void
    {
        if (empty(self::$features)) {
            if (file_exists(static::FILE)) {
                self::$features = require static::FILE;
            } else {
                self::$features = [];
            }
        }
    }
}
