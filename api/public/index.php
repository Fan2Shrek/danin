<?php

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    $kernelClass = (bool) $context['APP_SELF_HOSTED'] ? App\SelfHostedKernel::class : App\DaninKernel::class;

    return new $kernelClass($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
