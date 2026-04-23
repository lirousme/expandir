<?php

declare(strict_types=1);

use App\Support\Env;

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    $baseDir = dirname(__DIR__) . '/src/';

    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (is_file($file)) {
        require_once $file;
    }
});

Env::load(dirname(__DIR__) . '/.env');

$appTimezone = Env::get('APP_TIMEZONE', 'America/Sao_Paulo');
if ($appTimezone === null || !in_array($appTimezone, timezone_identifiers_list(), true)) {
    date_default_timezone_set('America/Sao_Paulo');
} else {
    date_default_timezone_set($appTimezone);
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
