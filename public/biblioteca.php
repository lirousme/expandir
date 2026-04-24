<?php

declare(strict_types=1);

use App\Infrastructure\Database\Connection;

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

$connection = Connection::make();

$userStatement = $connection->prepare('SELECT id FROM users WHERE username = :username LIMIT 1');
$userStatement->execute(['username' => $username]);
$userId = (int) ($userStatement->fetchColumn() ?: 0);

if ($userId <= 0) {
    session_destroy();
    header('Location: ' . $homeUrl);
    exit;
}

$createError = '';
$createSuccess = '';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $nomeDoElemento = trim((string) ($_POST['nome_do_elemento'] ?? ''));

    if ($nomeDoElemento === '') {
        $createError = 'Informe o nome do elemento.';
    } else {
        try {
            $connection->beginTransaction();

            $insertElemento = $connection->prepare(
                'INSERT INTO elementos (nome_do_elemento, id_usuario) VALUES (:nome_do_elemento, :id_usuario)'
            );
            $insertElemento->execute([
                'nome_do_elemento' => $nomeDoElemento,
                'id_usuario' => $userId,
            ]);

            $elementoId = (int) $connection->lastInsertId();
            $ordemStatement = $connection->prepare(
                'SELECT COALESCE(MAX(ordem), 0) + 1 FROM biblioteca WHERE id_usuario = :id_usuario'
            );
            $ordemStatement->execute(['id_usuario' => $userId]);
            $proximaOrdem = (int) ($ordemStatement->fetchColumn() ?: 1);

            $insertBiblioteca = $connection->prepare(
                'INSERT INTO biblioteca (id_usuario, id_elemento, ordem) VALUES (:id_usuario, :id_elemento, :ordem)'
            );
            $insertBiblioteca->execute([
                'id_usuario' => $userId,
                'id_elemento' => $elementoId,
                'ordem' => $proximaOrdem,
            ]);

            $connection->commit();
            $createSuccess = 'Elemento criado e adicionado à biblioteca.';
        } catch (Throwable) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            $createError = 'Não foi possível criar o elemento agora. Tente novamente.';
        }
    }
}

$bibliotecaStatement = $connection->prepare(
    'SELECT e.id, e.nome_do_elemento, b.ordem
     FROM biblioteca b
     INNER JOIN elementos e ON e.id = b.id_elemento
     WHERE b.id_usuario = :id_usuario
     ORDER BY b.ordem ASC, b.id ASC'
);
$bibliotecaStatement->execute(['id_usuario' => $userId]);
$elementosDaBiblioteca = $bibliotecaStatement->fetchAll();

require dirname(__DIR__) . '/src/Presentation/View/biblioteca.php';
