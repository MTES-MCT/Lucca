<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    $appEnv = $context['APP_ENV'];
    $appDebug = (bool) $context['APP_DEBUG'];

    $trustedIps = explode(',', $_ENV['TRUSTED_IPS'] ?? $_SERVER['TRUSTED_IPS'] ?? '');
    $remoteIp = $_SERVER['REMOTE_ADDR'] ?? '';

    if ($remoteIp && in_array($remoteIp, $trustedIps, true)) {
        $_SERVER['APP_ENV'] = 'dev';
        $_ENV['APP_ENV'] = 'dev';
        $appEnv = 'dev';

        $appDebug = true;
    }

    return new Kernel($appEnv, $appDebug);
};
