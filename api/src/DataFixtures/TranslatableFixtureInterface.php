<?php

declare(strict_types=1);

namespace App\DataFixtures;

interface TranslatableFixtureInterface
{
    public function getDataForLocale(string $locale): iterable;
}
