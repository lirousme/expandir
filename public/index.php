<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

$scriptName = (string) ($_SERVER['SCRIPT_NAME'] ?? '');
$basePath = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
$basePath = $basePath === '.' ? '' : $basePath;

$bibliotecaUrl = $basePath . '/biblioteca.php';
$authUrl = $basePath . '/auth.php';

if (isset($_SESSION['auth_user'])) {
    header('Location: ' . $bibliotecaUrl);
    exit;
}

require dirname(__DIR__) . '/src/Presentation/View/login.php';
