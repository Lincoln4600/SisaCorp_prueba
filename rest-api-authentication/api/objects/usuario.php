<?php

class Usuario{
 

    private $conn;
    private $table_name = "usuarios";

    public $id;
    public $nombre;
    public $apellido;
    public $correo;
    public $contrasena;
 
    public function __construct($db){
        $this->conn = $db;
    }
 
    function create(){
 
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    nombre = :nombre,
                    apellido = :apellido,
                    correo = :correo,
                    contrasena = :contrasena";
     
        // prepare the query
        $stmt = $this->conn->prepare($query);
     
        // sanitize
        $this->nombre=htmlspecialchars(strip_tags($this->nombre));
        $this->apellido=htmlspecialchars(strip_tags($this->apellido));
        $this->correo=htmlspecialchars(strip_tags($this->correo));
        $this->contrasena=htmlspecialchars(strip_tags($this->contrasena));
     
        // bind the values
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':apellido', $this->apellido);
        $stmt->bindParam(':correo', $this->correo);
     
        // hash the password before saving to database
        $password_hash = password_hash($this->contrasena, PASSWORD_BCRYPT);
        $stmt->bindParam(':contrasena', $password_hash);
     
        // execute the query, also check if query was successful
        if($stmt->execute()){
            return true;
        }
     
        return false;
    }

    function correo_existe(){


        $query = "SELECT id, nombre, apellido, contrasena
            FROM " . $this->table_name . "
            WHERE correo = ?
            LIMIT 0,1";

        $stmt = $this->conn->prepare( $query );

        $this->correo=htmlspecialchars(strip_tags($this->correo));

        $stmt->bindParam(1, $this->correo);

        $stmt->execute();

        $num = $stmt->rowCount();



        if($num>0){
 
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
 
            $this->id = $row['id'];
            $this->nombre = $row['nombre'];
            $this->apellido = $row['apellido'];
            $this->contrasena = $row['contrasena'];

            return true;
        }

        return false;
    }

    function getInfoUsuario(){

        $query = "SELECT apellido, correo
            FROM " . $this->table_name . "
            WHERE nombre = ?
            LIMIT 0,1";

        $stmt = $this->conn->prepare( $query );

        $this->nombre=htmlspecialchars(strip_tags($this->nombre));

        $stmt->bindParam(1, $this->nombre);

        $stmt->execute();

        $num = $stmt->rowCount();

        if($num>0){
 
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->apellido = $row['apellido'];
            $this->correo = $row['correo'];
        }

    }
     
}

