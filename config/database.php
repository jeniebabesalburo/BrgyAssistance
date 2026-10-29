<?php
declare(strict_types=1);

final class Database
{
    private string $host = 'localhost';
    private string $dbName = 'barangay_resident_db';
    private string $username = 'root';
    private string $password = '';
    private ?PDO $connection = null;

    public function connect(): PDO
    {
        if ($this->connection instanceof PDO) {
            return $this->connection;
        }

        $dsn = "mysql:host={$this->host};dbname={$this->dbName};charset=utf8mb4";
        $this->connection = new PDO($dsn, $this->username, $this->password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        return $this->connection;
    }
}