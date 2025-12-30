<?php
// app/core/Model.php
// Model is a lightweight base class for simple Active Record style models.

namespace App\core;

use PDO;

abstract class Model
{
    // Child classes should override these
    protected static string $table = '';
    protected static string $primaryKey = 'id';

    // Returns the PDO instance
    protected static function db(): PDO
    {
        return Database::getInstance()->pdo();
    }

    // Finds a row by primary key
    public static function find($id): ?array
    {
        $table = static::$table;
        $pk    = static::$primaryKey;

        $sql  = "select * from {$table} where {$pk} = :id limit 1";
        $stmt = static::db()->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    // Returns all rows with optional conditions
    public static function all(string $where = '', array $params = []): array
    {
        $table = static::$table;
        $sql   = "select * from {$table}";
        if ($where !== '') {
            $sql .= " where {$where}";
        }
        $stmt = static::db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Inserts a row and returns the inserted ID
    public static function create(array $data): int
    {
        $table = static::$table;
        $cols  = array_keys($data);
        $fields = implode(', ', $cols);
        $placeholders = ':' . implode(', :', $cols);

        $sql  = "insert into {$table} ({$fields}) values ({$placeholders})";
        $stmt = static::db()->prepare($sql);
        $stmt->execute($data);

        $id = static::db()->lastInsertId();
        return (int)$id;
    }

    // Updates a row by primary key
    public static function update($id, array $data): void
    {
        $table = static::$table;
        $pk    = static::$primaryKey;

        $sets = [];
        foreach ($data as $col => $value) {
            $sets[] = "{$col} = :{$col}";
        }
        $setClause = implode(', ', $sets);

        $sql  = "update {$table} set {$setClause} where {$pk} = :id";
        $data['id'] = $id;

        $stmt = static::db()->prepare($sql);
        $stmt->execute($data);
    }

    // Deletes a row by primary key
    public static function delete($id): void
    {
        $table = static::$table;
        $pk    = static::$primaryKey;

        $sql  = "delete from {$table} where {$pk} = :id";
        $stmt = static::db()->prepare($sql);
        $stmt->execute(['id' => $id]);
    }
}
