<?php
// app/core/Database.php
// Database provides a shared PDO connection to the Supabase Postgres instance.

namespace App\core;

use PDO;
use PDOException;
use PDOStatement;

class Database
{
    // Singleton instance
    private static ?self $instance = null;

    // Underlying PDO connection
    private PDO $pdo;

    // Private constructor to enforce singleton
    private function __construct()
    {
        $this->pdo = $this->createPdo();
    }

    // Returns the singleton instance
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    // Backwards-compatible static helper used by models/repositories: Database::connection()
    public static function connection(): PDO
    {
        return self::getInstance()->pdo();
    }

    // Returns the underlying PDO connection
    public function pdo(): PDO
    {
        return $this->pdo;
    }

    // Helper for quick prepared queries on the instance
    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);

        if (!$stmt->execute($params)) {
            throw new \RuntimeException('Database query failed.');
        }

        return $stmt;
    }

    // Creates a PDO connection using .env settings and sets search_path
    private function createPdo(): PDO
    {
        $dsn  = $_ENV['DB_DSN_POOLED'] ?? '';
        $host = $_ENV['DB_HOST'] ?? '';
        $port = $_ENV['DB_PORT'] ?? '';
        $name = $_ENV['DB_NAME'] ?? 'postgres';
        $user = $_ENV['DB_USER'] ?? 'postgres';
        $pass = $_ENV['DB_PASSWORD'] ?? '';
        $ssl  = $_ENV['DB_SSLMODE'] ?? 'prefer';

        if ($dsn === '') {
            $dsn = "pgsql:host={$host};port={$port};dbname={$name};sslmode={$ssl}";
        }

        try {
            $pdo = new PDO(
                $dsn,
                $user,
                $pass,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        } catch (PDOException $e) {
            throw new \RuntimeException(
                'Failed to connect to database: ' . $e->getMessage(),
                0,
                $e
            );
        }

        // Set search_path to DB_SCHEMA so we can query "users" instead of "workflows.users"
        $schema = $_ENV['DB_SCHEMA'] ?? null;

        if ($schema && preg_match('/^[A-Za-z0-9_]+$/', $schema)) {
            $pdo->exec("set search_path to {$schema}");
        }

        return $pdo;
    }
}
