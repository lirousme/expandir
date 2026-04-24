<?php

declare(strict_types=1);

use App\Infrastructure\Database\Connection;

require_once __DIR__ . '/bootstrap.php';

$scriptName = (string) ($_SERVER['SCRIPT_NAME'] ?? '');
$basePath = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
$basePath = $basePath === '.' ? '' : $basePath;

$homeUrl = $basePath === '' ? '/' : $basePath . '/';
$bibliotecaUrl = $basePath . '/biblioteca.php';

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

if ((string) ($_GET['action'] ?? '') === 'sugestoes_elementos') {
    header('Content-Type: application/json; charset=utf-8');

    $query = trim((string) ($_GET['q'] ?? ''));
    if ($query === '') {
        echo json_encode(['items' => []], JSON_THROW_ON_ERROR);
        exit;
    }

    $suggestionStatement = $connection->prepare(
        'SELECT id, nome_do_elemento
         FROM elementos
         WHERE nome_do_elemento LIKE :query
         ORDER BY nome_do_elemento ASC
         LIMIT 8'
    );
    $suggestionStatement->execute(['query' => '%' . $query . '%']);
    $items = $suggestionStatement->fetchAll();

    echo json_encode(['items' => $items], JSON_THROW_ON_ERROR);
    exit;
}

$idElementoAtual = (int) ($_GET['id_elemento'] ?? 0);
if ($idElementoAtual <= 0) {
    header('Location: ' . $bibliotecaUrl);
    exit;
}

$elementoAtualStatement = $connection->prepare(
    'SELECT e.id, e.nome_do_elemento
     FROM biblioteca b
     INNER JOIN elementos e ON e.id = b.id_elemento
     WHERE b.id_usuario = :id_usuario AND e.id = :id_elemento
     LIMIT 1'
);
$elementoAtualStatement->execute([
    'id_usuario' => $userId,
    'id_elemento' => $idElementoAtual,
]);
$elementoAtual = $elementoAtualStatement->fetch();

if (!is_array($elementoAtual)) {
    header('Location: ' . $bibliotecaUrl);
    exit;
}

$createError = '';
$createSuccess = '';
$slideInformacoes = [];

if (isset($_SESSION['area_de_expansao_flash']) && is_array($_SESSION['area_de_expansao_flash'])) {
    $createError = (string) ($_SESSION['area_de_expansao_flash']['error'] ?? '');
    $createSuccess = (string) ($_SESSION['area_de_expansao_flash']['success'] ?? '');
    unset($_SESSION['area_de_expansao_flash']);
}

/**
 * Busca a primeira informação (nível 1) para um elemento de referência.
 */
$buscarPrimeiraInformacaoPorElemento = static function (PDO $connection, int $idUsuario, int $idElementoReferencia): ?array {
    $statement = $connection->prepare(
        'SELECT i.id, i.texto_ptbr, i.nivel
         FROM informacoes i
         INNER JOIN elementos_informacoes ei ON ei.id_informacao = i.id
         WHERE i.id_usuario = :id_usuario
           AND i.nivel = 1
           AND ei.id_elemento = :id_elemento
           AND ei.main = 1
         ORDER BY i.id ASC
         LIMIT 1'
    );
    $statement->execute([
        'id_usuario' => $idUsuario,
        'id_elemento' => $idElementoReferencia,
    ]);
    $resultado = $statement->fetch();

    return is_array($resultado) ? $resultado : null;
};

/**
 * Busca o próximo elemento de referência partindo de uma informação.
 */
$buscarProximoElementoReferencia = static function (PDO $connection, int $idInformacao): int {
    $statement = $connection->prepare(
        'SELECT id_elemento
         FROM elementos_informacoes
         WHERE id_informacao = :id_informacao
           AND main = 2
         ORDER BY id ASC
         LIMIT 1'
    );
    $statement->execute(['id_informacao' => $idInformacao]);

    return (int) ($statement->fetchColumn() ?: 0);
};

$primeiraInformacao = $buscarPrimeiraInformacaoPorElemento($connection, $userId, $idElementoAtual);
if ($primeiraInformacao !== null) {
    $slideInformacoes[] = $primeiraInformacao;

    $segundoElementoReferencia = $buscarProximoElementoReferencia($connection, (int) $primeiraInformacao['id']);
    if ($segundoElementoReferencia > 0) {
        $segundaInformacao = $buscarPrimeiraInformacaoPorElemento($connection, $userId, $segundoElementoReferencia);
        if ($segundaInformacao !== null) {
            $slideInformacoes[] = $segundaInformacao;

            $terceiroElementoReferencia = $buscarProximoElementoReferencia($connection, (int) $segundaInformacao['id']);
            if ($terceiroElementoReferencia > 0) {
                $terceiraInformacao = $buscarPrimeiraInformacaoPorElemento($connection, $userId, $terceiroElementoReferencia);
                if ($terceiraInformacao !== null) {
                    $slideInformacoes[] = $terceiraInformacao;
                }
            }
        }
    }
}

while (count($slideInformacoes) < 3) {
    $slideInformacoes[] = [
        'id' => 0,
        'texto_ptbr' => 'Informação não encontrada para esta etapa da trilha.',
        'nivel' => 1,
    ];
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && (string) ($_POST['action'] ?? '') === 'criar_informacao') {
    $textoInformacao = trim((string) ($_POST['texto_ptbr'] ?? ''));
    $nivel = (int) ($_POST['nivel'] ?? 0);
    $pessoal = isset($_POST['pessoal']) ? 1 : 2;
    $tagsPayload = trim((string) ($_POST['tags_payload'] ?? '[]'));

    if ($textoInformacao === '') {
        $createError = 'Digite o texto da informação.';
    } elseif (!in_array($nivel, [1, 2, 3, 4], true)) {
        $createError = 'Selecione um nível válido.';
    } else {
        try {
            /** @var mixed $decodedPayload */
            $decodedPayload = json_decode($tagsPayload, true, 512, JSON_THROW_ON_ERROR);
            $tags = is_array($decodedPayload) ? $decodedPayload : [];
        } catch (Throwable) {
            $tags = [];
        }

        $mainIndex = null;
        foreach ($tags as $index => $tag) {
            if (!is_array($tag)) {
                continue;
            }
            if ((int) ($tag['main'] ?? 2) === 1) {
                $mainIndex = $index;
                break;
            }
        }
        if ($mainIndex === null) {
            foreach ($tags as $index => $tag) {
                if (is_array($tag)) {
                    $tags[$index]['main'] = 1;
                    $mainIndex = $index;
                    break;
                }
            }
        }

        try {
            $connection->beginTransaction();

            $insertInformacao = $connection->prepare(
                'INSERT INTO informacoes (texto_ptbr, nivel, pessoal, id_usuario)
                 VALUES (:texto_ptbr, :nivel, :pessoal, :id_usuario)'
            );
            $insertInformacao->execute([
                'texto_ptbr' => $textoInformacao,
                'nivel' => $nivel,
                'pessoal' => $pessoal,
                'id_usuario' => $userId,
            ]);
            $informacaoId = (int) $connection->lastInsertId();

            $elementosRelacionados = [];

            foreach ($tags as $tag) {
                if (!is_array($tag)) {
                    continue;
                }

                $tagId = (int) ($tag['id'] ?? 0);
                $tagNome = trim((string) ($tag['nome'] ?? ''));
                $tagNovo = (bool) ($tag['novo'] ?? false);

                $tagMain = (int) ($tag['main'] ?? 2) === 1 ? 1 : 2;

                if ($tagNovo && $tagNome !== '') {
                    $insertNovoElemento = $connection->prepare(
                        'INSERT INTO elementos (nome_do_elemento, id_usuario, pessoal)
                         VALUES (:nome_do_elemento, :id_usuario, 2)'
                    );
                    $insertNovoElemento->execute([
                        'nome_do_elemento' => $tagNome,
                        'id_usuario' => $userId,
                    ]);
                    $novoElementoId = (int) $connection->lastInsertId();
                    $elementosRelacionados[$novoElementoId] = $tagMain;
                    continue;
                }

                if ($tagId > 0) {
                    $validarElementoStatement = $connection->prepare(
                        'SELECT id FROM elementos WHERE id = :id LIMIT 1'
                    );
                    $validarElementoStatement->execute(['id' => $tagId]);
                    $elementoExistenteId = (int) ($validarElementoStatement->fetchColumn() ?: 0);
                    if ($elementoExistenteId > 0) {
                        $mainAtual = $elementosRelacionados[$elementoExistenteId] ?? 2;
                        $elementosRelacionados[$elementoExistenteId] = min($mainAtual, $tagMain);
                    }
                }
            }

            if ($elementosRelacionados !== []) {
                $insertRelacionamento = $connection->prepare(
                    'INSERT INTO elementos_informacoes (id_elemento, id_informacao, id_usuario, main)
                     VALUES (:id_elemento, :id_informacao, :id_usuario, :main)'
                );

                foreach ($elementosRelacionados as $idElementoTag => $mainTag) {
                    $insertRelacionamento->execute([
                        'id_elemento' => $idElementoTag,
                        'id_informacao' => $informacaoId,
                        'id_usuario' => $userId,
                        'main' => $mainTag,
                    ]);
                }
            }

            $connection->commit();
            $createSuccess = 'Informação adicionada com sucesso.';
        } catch (Throwable) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }
            $createError = 'Não foi possível salvar a informação agora. Tente novamente.';
        }
    }

    $_SESSION['area_de_expansao_flash'] = [
        'error' => $createError,
        'success' => $createSuccess,
    ];
    header('Location: ' . $basePath . '/area_de_expansao.php?id_elemento=' . $idElementoAtual);
    exit;
}

require dirname(__DIR__) . '/src/Presentation/View/area_de_expansao.php';
