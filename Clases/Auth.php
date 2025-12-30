<?php

namespace Clases;

use Clases\Sesion;
use Modelos\Usuario;
final class Auth{

    /**
     * Summary of login
     * @param string $email
     * @param string $passwd
     * @return bool
     * 
     * Iniciar sesión con el usuario
     */
    public static function login(string $email, string $passwd)
    {
        $resultado = false;

        $usr = Usuario::getByEmailAndPassword($email, $passwd);

        if ($usr) {

            Sesion::initSesion($usr->idUsr) ;
            $resultado = true;
        }

        return $resultado;

    }

    





}