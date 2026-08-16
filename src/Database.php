<?php

declare(strict_types=1);

/**
 * SQLite PDO connection (singleton).
 */
final class Database
{
    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection === null) {
            $config = require __DIR__ . '/config.php';

            $dbDir = dirname($config['db_path']);
            if (!is_dir($dbDir)) {
                mkdir($dbDir, 0777, true);
            }

            $pdo = new PDO('sqlite:' . $config['db_path']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $pdo->exec('PRAGMA foreign_keys = ON');

            self::$connection = $pdo;
        }

        return self::$connection;
    }
}