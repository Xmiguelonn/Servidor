<?php

namespace Clases;

final class Database
{
    // Buscar con ambos nombres posibles de variables
    private static function getDbHost(): string
    {
        return getenv('MYSQL_HOST') ?: getenv('MYSQLHOST') ?: 'db';
    }
    
    private static function getDbUser(): string
    {
        return getenv('MYSQL_USER') ?: getenv('MYSQLUSER') ?: 'root';
    }
    
    private static function getDbPass(): string
    {
        return getenv('MYSQL_PASSWORD') ?: getenv('MYSQLPASSWORD') ?: 'root';
    }
    
    private static function getDbName(): string
    {
        return getenv('MYSQL_DATABASE') ?: 'midb';
    }
    
    private static function getDbPort(): string
    {
        return getenv('MYSQL_PORT') ?: getenv('MYSQLPORT') ?: '3306';
    }

    private static ?\PDO $conection = null;

    public function __construct() {}
    public function __clone() {}

    /**
     * Summary of conectar
     * @return \PDO|null
     * 
     * Esta función devuelve una conexión con la base de datos | Si no hay conexión la crea y la devuelve
     * y si hay una conexión existente la devuelve.
     */
    public static function conectar(): ?\PDO
    {
        if ( self::$conection === null ) {

            try{
                $host = self::getDbHost();
                $port = self::getDbPort();
                $dbname = self::getDbName();
                $user = self::getDbUser();
                $pass = self::getDbPass();

                $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8";
                self::$conection = new \PDO($dsn, $user, $pass);
                self::$conection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            } catch(\PDOException $pdoe) {
                die("**ERROR: " . $pdoe->getMessage());
            }

        }

        return self::$conection;
    }
}