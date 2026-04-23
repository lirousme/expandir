<?php

declare(strict_types=1);

use App\Application\UseCase\LoginUser;
use App\Application\UseCase\RegisterUser;
use App\Infrastructure\Database\Connection;
use App\Infrastructure\Repository\MySQLUserRepository;
use App\Presentation\Controller\AuthController;

require_once __DIR__ . '/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

$payload = json_decode(file_get_contents('php://input') ?: '{}', true);
if (!is_array($payload)) {
    $payload = [];
}

$repository = new MySQLUserRepository(Connection::make());
$controller = new AuthController(
    new RegisterUser($repository),
    new LoginUser($repository)
);

$response = $controller->handle($payload);
http_response_code($response['status']);
echo json_encode($response['body'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
