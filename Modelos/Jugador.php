<?php


namespace Modelos;

use Clases\Database;
use PDOException;


enum Posicion: string
{

    case DELANTERO = "DL";
    case CENTROCAMPISTA = "MD";
    case DEFENSA = "DF";
    case PORTERO = "POR";
}


final class Jugador
{
    private int $cod_jug;

    public int $codJug {
        get => $this->cod_jug;
    }
    private ?int $cod_equi;

    public ?int $codEqui {
        get => $this->cod_equi;
    }
    public private(set) string $nombre;
    public private(set) string $apellido;
    public private(set) int $dorsal;
    public private(set) string $elemento;
    public private(set) string $imagen;
    public private(set) string $posicion;


    public function __construct(string $newNombre, string $newApellido, int $newDorsal, string $newElemento, string $newPosicion, string $newImagen, int $newCodigo, ?int $newCodEqui)
    {
        $this->apellido = $newApellido;
        $this->nombre = $newNombre;
        $this->dorsal = $newDorsal;
        $this->elemento = $newElemento;
        $this->posicion = $newPosicion;
        $this->imagen = $newImagen;
        $this->cod_jug = $newCodigo;
        $this->cod_equi = $newCodEqui;
    }


    // Métodos

    /**
     * Summary of __toString
     * @return string
     */
    public function __toString(): string
    {
        return "----------------------<br>
            Nombre:     {$this->nombre} <br>
            Apellido:   {$this->apellido}<br>
            Dorsal:     {$this->dorsal}<br>
            Elemento:   {$this->elemento}<br>
            Posición:   {$this->posicion}<br>
        ";
    }

    /**
     * Summary of elementoAEmoji
     * @param string $value
     * @return string
     * 
     * Convierte un texto a un emoji pa q se vea mas wapo
     */
    public function elementoAEmoji(string $value): string
    {
        $texto = strtoupper($value);

        return match ($texto) {
            "FUEGO" => "🔥",
            "AIRE" => "🌪️",
            "MONTANIA" => "🏔️",
            "BOSQUE" => "🌳",
            default => "???"
        };
    }

    /**
     * Summary of addPlayer
     * @param string $newNombre
     * @param string $newApellido
     * @param int $newDorsal
     * @param string $newElemento
     * @param string $newPosicion
     * @param string $newImagen
     * @param mixed $newCodEqui
     * @return bool
     * 
     * Añade un equipo a la base de datos
     */
    public static function addPlayer(string $newNombre, string $newApellido, int $newDorsal, string $newElemento, string $newPosicion, string $newImagen, ?int $newCodEqui): bool
    {

        try {

            $pdo = Database::conectar();
            $sql = "INSERT INTO Jugador
            (Nombre, Apellido, Dorsal, Posicion, Imagen, Elemento, cod_equi)
            VALUES
            (:nombre, :apellido, :dorsal, :posicion, :imagen, :elemento, :equipo)
            ;";

            $stmt = $pdo->prepare($sql);

            $stmt->bindValue(":nombre", $newNombre, \PDO::PARAM_STR);
            $stmt->bindValue(":apellido", $newApellido, \PDO::PARAM_STR);
            $stmt->bindValue(":dorsal", $newDorsal, \PDO::PARAM_INT);
            $stmt->bindValue(":posicion", $newPosicion, \PDO::PARAM_STR);
            $stmt->bindValue(":imagen", $newImagen, \PDO::PARAM_STR);
            $stmt->bindValue(":elemento", $newElemento, \PDO::PARAM_STR);
            if ($newCodEqui === null) {
                $stmt->bindValue(":equipo", null, \PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(":equipo", $newCodEqui, \PDO::PARAM_INT);
            }

            return $stmt->execute();
        } catch (PDOException $pdoe) {
            die("**ERROR: " . $pdoe->getMessage());
        }
    }


    /**
     * Summary of getById
     * @param int $idJugador
     * @return Jugador|null
     * 
     * Busca un jugador a traves de un ID en la base de datos, si lo encuentra lo devuelve y si no lo encuentra devuelve null
     */
    public static function getById(int $idJugador): ?Jugador
    {
        $resultado = null;

        try {

            $pdo = Database::conectar();
            $sql = "SELECT * FROM Jugador WHERE cod_jug = :id ;";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":id", $idJugador, \PDO::PARAM_INT);
            $stmt->execute();

            $fila = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($fila) {
                $resultado = new Jugador(
                    $fila["Nombre"],
                    $fila["Apellido"],
                    $fila["Dorsal"],
                    $fila["Elemento"],
                    $fila["Posicion"],
                    $fila["Imagen"],
                    $fila["cod_jug"],
                    $fila["cod_equi"]
                );
            }
        } catch (PDOException $pdoe) {
            die("**ERROR: " . $pdoe->getMessage());
        }

        return $resultado;
    }




    /**
     * Summary of getAllWithOutTeam
     * @return array
     * 
     * Devuelve una lista de los jugadores que no tienen equipo
     */
    public static function getAllWithOutTeam(): array
    {
        $resultado = [];
        try {

            $pdo = Database::conectar();
            $sql = "SELECT * FROM Jugador WHERE cod_equi IS NULL ;";
            $stmt = $pdo->query($sql);

            while ($fila = $stmt->fetch(\PDO::FETCH_ASSOC)) {

                $jug = new Jugador(
                    $fila["Nombre"],
                    $fila["Apellido"],
                    $fila["Dorsal"],
                    $fila["Elemento"],
                    $fila["Posicion"],
                    $fila["Imagen"],
                    $fila["cod_jug"],
                    null
                );

                array_push($resultado, $jug);
            }
        } catch (PDOException $pdoe) {
            die("**ERROR: " . $pdoe->getMessage());
        }

        return $resultado;
    }


    /**
     * Summary of getAllByTeam
     * @param int $codigo
     * @return array
     * 
     * Esta función devuelve un array con los jugadores que pertenecen
     * al equipo que se le pasa por parámetro
     */
    public static function getAllByTeam(int $codigo): array
    {
        $resultado = [];

        try {

            $pdo = Database::conectar();
            $sql = "SELECT * FROM Jugador WHERE cod_equi = :codigo ;";
            $stmt = $pdo->prepare($sql);

            $stmt->bindValue(":codigo", $codigo, \PDO::PARAM_INT);
            $stmt->execute();

            while ($fila = $stmt->fetch(\PDO::FETCH_ASSOC)) {

                $jug = new Jugador(
                    $fila["Nombre"],
                    $fila["Apellido"],
                    $fila["Dorsal"],
                    $fila["Elemento"],
                    $fila["Posicion"],
                    $fila["Imagen"],
                    $fila["cod_jug"],
                    $fila["cod_equi"]
                );

                array_push($resultado, $jug);
            }
        } catch (PDOException $pdoe) {
            die("**ERROR: " . $pdoe->getMessage());
        }

        return $resultado;
    }


    /**
     * Summary of countByTeam
     * @param int $codigo
     * @return int
     * 
     * Esta función devuelve el número de jugadores que tiene un equipo
     */
    public static function countByTeam(int $codigo): int
    {
        $resultado = 0;

        try {

            $pdo = Database::conectar();
            $sql = "SELECT COUNT(*) FROM Jugador WHERE cod_equi = :idEquipo ;";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":idEquipo", $codigo, \PDO::PARAM_INT);
            $stmt->execute();

            $resultado = (int) $stmt->fetchColumn();
        } catch (PDOException $pdoe) {
            die("**ERROR " . $pdoe->getMessage());
        }


        return $resultado;
    }

    /**
     * Summary of addPlayerToTeam
     * @param int $cod_jug
     * @param int $cod_equi
     * @return bool
     * 
     * Esta función lo que hace es añadir un jugador a un equipo
     */
    public static function addPlayerToTeam(int $cod_jug, int $cod_equi): bool
    {

        try {

            $pdo = Database::conectar();

            $sql = "UPDATE Jugador
                    SET cod_equi = :equipo
                    WHERE cod_jug = :jugador ;";

            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":equipo", $cod_equi, \PDO::PARAM_INT);
            $stmt->bindValue(":jugador", $cod_jug, \PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $pdoe) {
            die("**ERROR " . $pdoe->getMessage());
        }
    }

    /**
     * Summary of expulsarJugador
     * @param int $idJugador
     * @return bool
     * 
     * Expulsa a un jugador de un equipo
     * Pone el campo codigo de equipo de un jugador a NULL
     */
    public static function expulsarJugador(int $idJugador): bool
    {

        try {

            $pdo = Database::conectar();

            $sql = "UPDATE Jugador 
                    SET cod_equi = NULL 
                    WHERE cod_jug = :id ;";

            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":id", $idJugador, \PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $pdoe) {
            die("**ERROR " . $pdoe->getMessage());
        }
    }

    /**
     * Summary of updatePlayer
     * @param int $id
     * @param string $nombre
     * @param string $apellido
     * @param int $dorsal
     * @param string $posicion
     * @param string $elemento
     * @param string $imagen
     * @return bool
     * 
     * Actualiza los datos de un jugador en la base de datos referenciado por un ID
     */
    public static function updatePlayer(int $id, string $nombre, string $apellido, int $dorsal, string $posicion, string $elemento, string $imagen): bool
    {
        try {
            $pdo = Database::conectar();

            $sql = "UPDATE Jugador
                SET nombre = :nombre,
                    apellido = :apellido,
                    dorsal = :dorsal,
                    posicion = :posicion,
                    elemento = :elemento,
                    imagen = :imagen
                WHERE cod_jug = :id";

            $stmt = $pdo->prepare($sql);

            $stmt->bindValue(":nombre", $nombre, \PDO::PARAM_STR);
            $stmt->bindValue(":apellido", $apellido, \PDO::PARAM_STR);
            $stmt->bindValue(":dorsal", $dorsal, \PDO::PARAM_INT);
            $stmt->bindValue(":posicion", $posicion, \PDO::PARAM_STR);
            $stmt->bindValue(":elemento", $elemento, \PDO::PARAM_STR);
            $stmt->bindValue(":imagen", $imagen, \PDO::PARAM_STR);
            $stmt->bindValue(":id", $id, \PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $pdoe) {
            die("**ERROR" . $pdoe->getMessage());
        }
    }

    /**
     * Summary of eliminar
     * @param int $id
     * @return bool
     * 
     * Eliminar a un jugador de la base de datos referenciado por ID
     */
    public static function deletePlayer(int $id): bool
    {

        try{

            $pdo = Database::conectar();
            $sql = "DELETE FROM Jugador WHERE cod_jug = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":id", $id, \PDO::PARAM_INT);

            return $stmt->execute();

        } catch (PDOException $pdoe) {
            die("**ERROR" . $pdoe->getMessage());
        }

    }


}
