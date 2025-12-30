<?php

namespace Controladores;

use Clases\Database;
use Modelos\Jugador;
use Modelos\Usuario;
use PDOException;
use Clases\Sesion;
use Clases\Request;
use Modelos\Equipo;

final class JugadorController extends BaseController
{

    /**
     * Summary of expulsar
     * @return never
     * 
     * Esta función lo que hace es poner el campo de cod_equi de un jugador a null
     * SE EXPULSA DEL EQUIPO
     */
    public function expulsar(): void
    {

        if (!Sesion::isLogged()) {
            Request::redirect("/");
        }

        $idJugador = (int) trim($_GET["id"] ?? 0);

        if ($idJugador <= 0) {
            Request::redirect("/home");
        }

        $jugador = Jugador::getById($idJugador);

        if (!$jugador) {
            Request::redirect("/home");
        }

        if ($jugador->codEqui !== null) {
            $equipo = Equipo::getById($jugador->codEqui);

            $usuario = Usuario::getById(Sesion::getId());

            if ($equipo->codUsu !== $usuario->idUsr && $usuario->rol !== "admin") {
                Request::redirect("/home");
            }
        } else {
            Request::redirect("/home");
        }

        Jugador::expulsarJugador($idJugador);
        Request::redirect("/equipo/detalles/" . $jugador->codEqui);
    }

    /**
     * Summary of nuevo
     * @return void
     * 
     * Renderiza la página del formulario para crear un jugador nuevo
     */
    public function nuevo(): void
    {

        if (!Sesion::isLogged()) {
            Request::redirect("/");
        }

        $this->render("jugador_nuevo.twig");
    }

    /**
     * Summary of crear
     * @return never
     * 
     * Procesa los datos del formulario para crear un jugador nuevo ( jugador_nuevo.twig )
     */
    public function crear(): void
    {

        if (!Sesion::isLogged()) {
            Request::redirect("/");
        }

        $nombre = trim($_POST["nombre"] ?? "");
        $apellido = trim($_POST["apellido"] ?? "");
        $dorsal = (int) ($_POST["dorsal"] ?? 0);
        $posicion = trim($_POST["posicion"] ?? "");
        $elemento = trim($_POST["elemento"] ?? "");
        $imagen = trim($_POST["imagen"] ?? "");

        if ($nombre === "" || $apellido === "" || $dorsal <= 0 || $posicion === "" || $elemento === "") {
            Request::redirect("/jugador/nuevo");
        }


        Jugador::addPlayer($nombre, $apellido, $dorsal, $elemento, $posicion, $imagen, null);

        Request::redirect("/jugador/libres");
    }

    /**
     * Summary of libres
     * @return void
     * 
     * Renderiza la página para listar todos los jugadores que no tienen equipo
     */
    public function libres(): void
    {
        if (!Sesion::isLogged()) {
            Request::redirect("/");
        }

        $jugadoresLibres = Jugador::getAllWithOutTeam();
        $totalLibres = count($jugadoresLibres);

        $this->render("jugadores_libres.twig", [
            "jugadores" => $jugadoresLibres,
            "totalLibres" => $totalLibres,
            "rol" => Usuario::getById(Sesion::getId())->rol
        ]);
    }

    /**
     * Summary of fichar
     * @return never
     * 
     * Funcionalidad para fichar a un jugador (añadirlo a tu equipo)
     */
    public function fichar(): never
    {
        if (!Sesion::isLogged()) {
            Request::redirect("/");
        }

        $codJug = $_GET["id"] ?? null;

        if (!$codJug) {
            Request::redirect("/jugador/libres");
        }

        $equipo = Equipo::getByUserId(Sesion::getId());

        if (!$equipo) {
            Request::redirect("/home");
        }

        Jugador::addPlayerToTeam($codJug, $equipo->codEqui);

        Request::redirect("/jugador/libres");
    }

    /**
     * Summary of editar
     * @return void
     * 
     * Renderiza la página para el formulario de editar un jugador
     */
    public function editar()
    {
        if (!Sesion::isLogged()) {
            Request::redirect("/");
        }

        $id = $_GET["id"] ?? null;

        if (!$id) {
            Request::redirect("/home");
        }

        $jugador = Jugador::getById($id);

        if (!$jugador) {
            Request::redirect("/home");
        }

        $equipoJugador = $jugador->codEqui ? Equipo::getById($jugador->codEqui) : null;
        $usuario = Usuario::getById(Sesion::getId());

        if ($equipoJugador && $equipoJugador->codUsu !== $usuario->idUsr && $usuario->rol !== "admin") {
            Request::redirect("/home");
        }

        $this->render("jugador_editar.twig", [
            "jugador" => $jugador
        ]);
    }

    /**
     * Summary of actualizar
     * @return never
     * 
     * Procesa los datos del formulario para actualizar los datos de un jugador (jugador_editar.twig)
     */
    public function actualizar(): never
    {

        if (!Sesion::isLogged()) {
            Request::redirect("/");
        }

        $id = (int) ($_POST["id"] ?? 0);

        if ($id <= 0) {
            Request::redirect("/home");
        }

        $jugador = Jugador::getById($id);

        if (!$jugador) {
            Request::redirect("/home");
        }

        $usuario = Usuario::getById(Sesion::getId());
        $equipoJugador = $jugador->codEqui ? Equipo::getById($jugador->codEqui) : null;

        if ($equipoJugador && $equipoJugador->codUsu !== $usuario->idUsr && $usuario->rol !== "admin") {
            Request::redirect("/home");
        }


        $nombre = trim($_POST["nombre"]);
        $apellido = trim($_POST["apellido"]);
        $dorsal = (int) trim($_POST["dorsal"]);
        $posicion = trim($_POST["posicion"]);
        $elemento = trim($_POST["elemento"]);
        $imagen = trim($_POST["imagen"]);


        Jugador::updatePlayer($id, $nombre, $apellido, $dorsal, $posicion, $elemento, $imagen);

        if ($equipoJugador) {
            Request::redirect("/equipo/detalles/" . $equipoJugador->codEqui);
        } else {
            Request::redirect("/jugador/libres");
        }
    }

    public function eliminar()
    {
        if (!Sesion::isLogged()) {
            Request::redirect("/");
        }

        $id = (int) ($_GET["id"] ?? 0);

        if ($id <= 0) {
            Request::redirect("/home");
        }

        $jugador = Jugador::getById($id);

        if (!$jugador) {
            Request::redirect("/home");
        }

        $usuario = Usuario::getById(Sesion::getId());


        if ($jugador->codEqui === null) {

            if ($usuario->rol !== 'admin') {
                Request::redirect("/home");
            }

            Jugador::deletePlayer($jugador->codJug);
            Request::redirect("/home");
        }


        $equipo = Equipo::getById($jugador->codEqui);

        if (!$equipo) {
            Request::redirect("/home");
        }

        if ($equipo->codUsu !== $usuario->idUsr && $usuario->rol !== 'admin') {
            Request::redirect("/home");
        }

        Jugador::deletePlayer($jugador->codJug);
        Request::redirect("/home");
    }
}
