<?php

namespace modelos;

use Controladores\JugadorController;
use Clases\Database;
use PDOException;

final class Equipo
{
    private int $cod_usu;

    public int $codUsu{
        get => $this->cod_usu; 
    }
    private int $cod_equi;

    public int $codEqui{
        get => $this->cod_equi;
    }
    public private(set) string $nombre;
    public private(set) string $escudo; // url

    public function __construct(string $newNombre, string $newEscudo, int $newCodUsu, int $newCodEqui)
    {
        $this->nombre = $newNombre;
        $this->escudo = $newEscudo;
        $this->cod_usu = $newCodUsu;
        $this->cod_equi = $newCodEqui;
    }

    //Métodos

    /**
     * Summary of __toString
     * @return string
     */
    public function __toString(): string
    {
        return "----------------------<br>
            Nombre: {$this->nombre} <br>
        ";
    }

    /**
     * Summary of getAll
     * @return array
     * 
     * Devuelve un array con todos los equipos
     */
    public static function getAll(): array
    {
        $resultado = [];

        try {

            $pdo = Database::conectar();
            $sql = "SELECT * FROM Equipo ;";
            $stmt = $pdo->query($sql);

            while ($fila = $stmt->fetch(\PDO::FETCH_ASSOC)) {

                $equi = new Equipo(
                    $fila["Nombre"],
                    $fila["Escudo"],
                    $fila["cod_usu"],
                    $fila["cod_equi"]
                );

                array_push($resultado, $equi);
            }
        } catch (PDOException $pdoe) {
            die("**ERROR: " . $pdoe->getMessage());
        }

        return $resultado;
    }

    /**
     * Summary of getById
     * @param int $idEquipo
     * @return Equipo|null
     * 
     * Devuelve el equipo asociado aun ID de equipo, si no existe devuelve null
     */
    public static function getById(int $idEquipo): ?Equipo
    {
        $resultado = null;

        try {
            $pdo = Database::conectar();
            $sql = "SELECT * FROM Equipo WHERE cod_equi = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":id", $idEquipo, \PDO::PARAM_INT);
            $stmt->execute();

            $fila = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($fila) {
                $resultado = new Equipo(
                    $fila["Nombre"],
                    $fila["Escudo"],
                    $fila["cod_usu"],
                    $fila["cod_equi"]
                );
            }
        } catch (PDOException $pdoe) {
            die("**ERROR: " . $pdoe->getMessage());
        }

        return $resultado;
    }

    /**
     * Summary of getByUserId
     * @param int $idUsuario
     * @return Equipo|null
     * 
     * Devuelve un equipo asociado a un usuario, si no existe devuelve null
     */
    public static function getByUserId(int $idUsuario): ?Equipo
    {

        $resultado = null;
        try {
            $pdo = Database::conectar();
            $sql = "SELECT * FROM Equipo WHERE cod_usu = :idUsuario ;";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":idUsuario", $idUsuario, \PDO::PARAM_INT);
            $stmt->execute();

            $fila = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($fila){

                $resultado = new Equipo(
                $fila["Nombre"],
                $fila["Escudo"],
                $fila["cod_usu"],
                $fila["cod_equi"]
            );
            
        }

            
        } catch (PDOException $pdoe) {
            die("**ERROR: " . $pdoe->getMessage());
        }

        return $resultado;
    }

    /**
     * Summary of insert
     * @param string $nombre
     * @param string $escudo
     * @param int $cod_usu
     * @return bool
     * 
     * Crea un equipo nuevo
     */
    public static function createTeam(string $nombre, string $escudo, int $cod_usu): bool
    {
        try {
            $pdo = Database::conectar();

            $sql = "INSERT INTO Equipo (Nombre, Escudo, cod_usu)
                    VALUES (:nombre, :escudo, :usuario) ;";

            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":nombre", $nombre, \PDO::PARAM_STR);
            $stmt->bindValue(":escudo", $escudo, \PDO::PARAM_STR);
            $stmt->bindValue(":usuario", $cod_usu, \PDO::PARAM_INT);

            return $stmt->execute();

        } catch (PDOException $pdoe) {
            die("**ERROR: " . $pdoe->getMessage());
        }
    }

    /**
     * Summary of updateTeam
     * @param string $newNombre
     * @param string $newEscudo
     * @param int $idEquipo
     * @return bool
     * 
     * Actualiza un equipo de la base de datos
     */
    public static function updateTeam(string $newNombre, string $newEscudo, int $idEquipo): bool
    {
        try{

            $pdo = Database::conectar();

            $sql = "UPDATE Equipo
            SET Nombre = :nombre,
                Escudo = :escudo
            WHERE cod_equi = :codigo
            ;";

            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":nombre", $newNombre, \PDO::PARAM_STR);
            $stmt->bindValue(":escudo", $newEscudo, \PDO::PARAM_STR);
            $stmt->bindValue(":codigo", $idEquipo, \PDO::PARAM_INT);

            return $stmt->execute();

        } catch(PDOException $pdoe){
            die("**ERROR: " . $pdoe->getMessage());
        }

    }

    /**
     * Summary of deleteTeam
     * @param int $idEquipo
     * @return bool
     * 
     * Borra un equipo de la base de datos
     */
    public static function deleteTeam(int $idEquipo): bool
    {
        try{

            $pdo = Database::conectar();

            $sql = "DELETE FROM Equipo
                    WHERE cod_equi = :codigo
            ;";

            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":codigo", $idEquipo, \PDO::PARAM_INT);

            return $stmt->execute();

        }catch(PDOException $pdoe){
            die("**ERROR: " . $pdoe->getMessage());
        }
    }


}
