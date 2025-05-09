<?php

declare(strict_types=1);

namespace App\Translation;

trait LocaleAwareTrait
{
    private string $locale;

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function getLocale(): string
    {
        if (!isset($this->locale)) {
            throw new \LogicException('Locale is not set.');
        }

        return $this->locale;
    }
}
