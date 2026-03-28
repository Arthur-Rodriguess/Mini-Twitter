<?php

namespace MiniTwitter\Config;

use PDO;

class Database
{
    /**
     * Estabelece a conexão com o banco de dados MySQL
     * 
     * @param void
     * @return PDO
     */
    public static function connect(): PDO
    {
        $host = "localhost";
        $db = "mini-twitter";
        $user = "root";
        $pass = "mysql292024";
        $charset = "utf8mb4";
        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        return new PDO($dsn, $user, $pass, $options);
    }
}