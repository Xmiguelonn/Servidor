<?php

namespace Clases;

final class Sesion
{
    /**
     * Summary of start
     * @return void
     * 
     * Comprueba si la sesión está iniciada, si no lo está la inicia
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        } 
    }


    /**
     * Summary of login
     * @param int $IDUsuario
     * @return void
     * 
     * Inicia la sesión con el ID del usuario
     */
    public static function initSesion(int $IDUsuario): void
    {
        self::start();
        $_SESSION["IDUsuario"] = $IDUsuario;
    }



    /**
     * Summary of get
     * @param string $clave
     * @return mixed
     * 
     * Devuelve el valor guardado en la posición clave y si no existe devuelve null
     */
    public static function get(string $clave): mixed
    {
        self::start();
        return $_SESSION[$clave] ?? null;
    }

    public static function getId(): ?int
    {
        self::start();
        return $_SESSION["IDUsuario"] ?? null;
    }


    /**
     * Summary of isLogged
     * @return bool
     * 
     * Comprueba si hay un usuario logueado
     */
    public static function isLogged(): bool
    {
        self::start();
        return isset($_SESSION["IDUsuario"]);
    }


    /**
     * Summary of logout
     * @return void
     * 
     * Cierra la sesión
     */
    public static function closeSesion(): void
    {
        self::start();
        $_SESSION = [];
        session_destroy();
    }
}
