<?php

namespace Controladores;

use Clases\Database;
use modelos\Equipo;
use PDOException;
use Clases\Sesion;
use Clases\Request;
use modelos\Jugador;
use modelos\Usuario;
use Twig\Node\Expression\Filter\RawFilter;

final class EquipoController extends BaseController
{

    /**
     * Summary of crear
     * @return void
     * 
     * Renderiza el formulario de creación de equipo
     */
    public function crear(): void
    {
        if (!Sesion::isLogged()) {
            Request::redirect("/");
        }

        $usuario = Usuario::getById(Sesion::getId());
        $equipo = Equipo::getByUserId(Sesion::getId());

        if ($equipo) {
            Request::redirect("/equipo/detalles/" . $equipo->codEqui);
        }

        $this->render("crear_equipo.twig", [
            "usuario" => $usuario,
            "rol" => $usuario->rol
        ]);
    }

    /**
     * Summary of guardar
     * @return never
     * 
     * Procesa los dtos del formulario para la creación de un nuevo equipo ( crear_equipo.twig )
     */
    public function guardar(): void
    {
        if (!Sesion::isLogged()) {
            Request::redirect("/");
        }

        $usuario = Usuario::getById(Sesion::getId());

        if (Equipo::getByUserId($usuario->idUsr)) {
            Request::redirect("/home");
        }

        $nombre = trim($_POST["nombre"]);
        $escudo = trim($_POST["escudo"]);

        Equipo::createTeam($nombre, $escudo, $usuario->idUsr);

        Request::redirect("/home");
    }


    /**
     * Summary of detalles
     * @return void
     * 
     * Renderiza la página de de los detalles de un equipo
     */
    public function detalles(): void
    {
        if (!Sesion::isLogged()) {
            Request::redirect("/");
        }

        $idEquipo = $_GET["id"] ?? null;

        if (!$idEquipo) {
            Request::redirect("/home");
        }

        $equipo = Equipo::getById($idEquipo);
        if (!$equipo) {
            Request::redirect("/home");
        }

        $jugadores = Jugador::getAllByTeam($equipo->codEqui);



        $this->render("detalles_equipo.twig", [
            "equipo" => $equipo,
            "jugadores" => $jugadores,
            "usuarioID" => Sesion::getId(),
            "usuarioNombre" => Usuario::getById($equipo->codUsu)->nombre,
            "rol" => Usuario::getById(Sesion::getId())->rol
        ]);
    }

    /**
     * Summary of editar
     * @return void
     * 
     * Renderiza el formulario de edición de un equipo
     */
    public function editar(): void
    {
        if (!Sesion::isLogged()) {
            Request::redirect("/");
        }

        $idEquipo = $_GET["id"] ?? null;

        if (!$idEquipo) {
            Request::redirect("/home");
        }

        $equipo = Equipo::getById($idEquipo);

        if (!$equipo) {
            Request::redirect("/home");
        }

        $usuario = Usuario::getById(Sesion::getId());

        if ($equipo->codUsu !== $usuario->idUsr && $usuario->rol !== "admin") {
            Request::redirect("/home");
        }

        $this->render("editar_equipo.twig", [
            "equipo" => $equipo,
            "rol" => $usuario->rol
        ]);
    }

    /**
     * Summary of actualizar
     * @return never
     * 
     * Procesa los datos del formulario de edición de equipo ( editar_equipo.twig )
     */
    public function actualizar(): never
    {
        if (!Sesion::isLogged()) {
            Request::redirect("/");
        }

        $nombre = trim($_POST["nombre"]);
        $escudo = trim($_POST["escudo"]);
        $idEquipo = (int) trim($_POST["id_equipo"]);

        $equipo = Equipo::getById($idEquipo);

        if (!$equipo) {
            Request::redirect("/home");
        }

        $usuario = Usuario::getById(Sesion::getId());

        if ($equipo->codUsu !== $usuario->idUsr && $usuario->rol !== "admin") {
            Request::redirect("/home");
        }

        Equipo::updateTeam($nombre, $escudo, $idEquipo);

        Request::redirect("/equipo/detalles/" . $idEquipo);
    }

    /**
     * Summary of eliminar
     * @return never
     * 
     * Funcionalidad de eliminar a un equipo de la base de datos
     */
    public function eliminar(): never
    {
        if (!Sesion::isLogged()) {
            Request::redirect("/");
        }

        $idEquipo = (int) trim($_GET["id"]);

        $equipo = Equipo::getById($idEquipo);

        if (!$equipo) {
            Request::redirect("/home");
        }

        $usuario = Usuario::getById(Sesion::getId());

        if ($equipo->codUsu !== $usuario->idUsr && $usuario->rol !== "admin") {
            Request::redirect("/home");
        }

        Equipo::deleteTeam($equipo->codEqui);

        Request::redirect("/home");
    }

    /**
     * Summary of listar
     * @return void
     * 
     * Lista todos los equipos de la base de datos
     */
    public function listar()
    {

        if (!Sesion::isLogged()) {
            Request::redirect("/");
        }

        $equipos = Equipo::getAll();

        $this->render("equipos_listado.twig", [
            "equipos" => $equipos
        ]);

    }

    
}
