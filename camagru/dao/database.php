<?php

namespace dao;

use \pdo;

class Database
{
    private $_connection;

    public function load()
    {
        $DB_USER = 'root';
        $DB_PASSWORD = 'toor';
        $DB_NAME = 'camagru';
        $DB_CHARSET = 'utf8mb4';
        $DB_HOST = 'mysql';
        $DB_DSN = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset={$DB_CHARSET}";

        try {
            $this->_connection = new PDO($DB_DSN, $DB_USER , $DB_PASSWORD, array());
            $this->_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // The subject requires this mode of error handling
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        } 
    }
    public function get_connection() { return $this->_connection; }
    public function close() { $this->_connection = NULL; }
}