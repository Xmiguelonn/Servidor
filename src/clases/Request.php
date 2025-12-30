<?php
namespace Clases;

final class Request{
    
    /**
     * Summary of method
     * @param string $met
     * @return bool
     * 
     * Comprobar un método, devuelve true o false
     */
    public static function method(string $met): bool
    {
        return strtolower($met) === strtolower($_SERVER["REQUEST_METHOD"]);
    }

    /**
     * Summary of redirect
     * @param string $url
     * @return never
     * 
     * Redireccionar a una url
     */
    public static function redirect(string $url): never
    {
        header("Location: {$url}");
        exit();
    }



}