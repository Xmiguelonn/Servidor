<?php

namespace Clases;

final class Database
{
    private const DBHOST = "db";
    private const DBUSER = "root";
    private const DBPASS = "root";
    private const DBNAME = "midb";

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

                $dsn = "mysql:host=" . self::DBHOST . ";dbname=" . self::DBNAME . ";charset=utf8";
                self::$conection = new \PDO($dsn, self::DBUSER, self::DBPASS);

            } catch(\PDOException $pdoe) {

                die("**ERROR: " . $pdoe->getMessage());
            }

        }

        return self::$conection;
    }



}
