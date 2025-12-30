<?php

namespace Controladores;

use Clases\Request;
use Clases\Sesion;
use modelos\Usuario;
use modelos\Equipo;

final class HomeController extends BaseController
{

    /**
     * Summary of index
     * @return void
     * 
     * Renderiza el home
     */
    public function index()
    {
        if (!Sesion::isLogged()) {
            Request::redirect("/");
        }

        $id = Sesion::getId();
        $usuario = Usuario::getById($id);
        $equipo = Equipo::getByUserId($id);


        $this->render("home.twig", [
            "usuario" => $usuario,
            "equipo" => $equipo
        ]);
    }


}