<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use App\Support\Env;
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
        $dbTimezoneOffset = '-03:00';

        $dsn = "mysql:host={$host};dbname={$dbName};charset=utf8mb4";

        try {
            $connection = new PDO($dsn, $dbUser, $dbPass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '{$dbTimezoneOffset}'",
            ]);

            $quotedTimezoneOffset = $connection->quote($dbTimezoneOffset);
            $connection->exec("SET time_zone = {$quotedTimezoneOffset}");

            return $connection;
        } catch (PDOException $exception) {
            throw new RuntimeException('Falha na conexão com MySQL. Verifique as credenciais do .env');
        }
    }
}
