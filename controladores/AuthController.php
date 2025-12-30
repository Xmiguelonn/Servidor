<?php

namespace Controladores;

use Clases\Request;
use Clases\Sesion;

final class AuthController extends BaseController{
    
    /**
     * Summary of index
     * @return void
     * 
     * Renderizar la página de registro ( No se usa nunca (CREO) )
     */
    public function index() 
    {
        if (Request::method("get")) {
            $this->render("registro.twig");
        }
    }


    /**
     * Summary of logout
     * @return never
     * 
     * Cierra la sesión y redirige al formulario de registro
     */
    public function logout(): void
    {

        Sesion::closeSesion();
        Request::redirect("/");
    }



}