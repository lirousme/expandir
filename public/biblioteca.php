<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

$scriptName = (string) ($_SERVER['SCRIPT_NAME'] ?? '');
$basePath = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
$basePath = $basePath === '.' ? '' : $basePath;

$homeUrl = $basePath === '' ? '/' : $basePath . '/';
$logoutUrl = $basePath . '/biblioteca.php?logout=1';

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . $homeUrl);
    exit;
}

$username = (string) ($_SESSION['auth_user'] ?? '');
if ($username === '') {
    header('Location: ' . $homeUrl);
    exit;
}

require dirname(__DIR__) . '/src/Presentation/View/biblioteca.php';
