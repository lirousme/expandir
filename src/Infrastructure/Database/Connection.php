<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use App\Support\Env;
use DateTimeImmutable;
use DateTimeZone;
use PDO;
use PDOException;
use RuntimeException;

final class Connection
{
    public static function make(): PDO
    {
        $host = Env::get('DB_HOST', '127.0.0.1');
        $dbName = Env::get('DB_NAME', 'login_app');
        $dbUser = Env::get('DB_USER', 'root');
        $dbPass = Env::get('DB_PASS', '');
        $appTimezone = Env::get('APP_TIMEZONE', 'America/Sao_Paulo');
        if ($appTimezone === null || !in_array($appTimezone, timezone_identifiers_list(), true)) {
            $appTimezone = 'America/Sao_Paulo';
        }

        $calculatedOffset = (new DateTimeImmutable('now', new DateTimeZone($appTimezone)))->format('P');
        $dbTimezoneOffset = Env::get('DB_TIMEZONE_OFFSET', $calculatedOffset);

        $dsn = "mysql:host={$host};dbname={$dbName};charset=utf8mb4";

        try {
            $connection = new PDO($dsn, $dbUser, $dbPass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);

            $quotedTimezoneOffset = $connection->quote($dbTimezoneOffset);
            $connection->exec("SET time_zone = {$quotedTimezoneOffset}");

            return $connection;
        } catch (PDOException $exception) {
            throw new RuntimeException('Falha na conexão com MySQL. Verifique as credenciais do .env');
        }
    }
}
