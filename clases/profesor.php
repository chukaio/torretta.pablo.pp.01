<?php

use \Firebase\JWT\JWT;
use \Firebase\JWT\SignatureInvalidException;

class profesor{
    //Atributos
    private $_nombre;
    private $_legajo;

    //Constructor
    public function __construct($nombre, $legajo)
    {
        $this->_nombre = $nombre;
        $this->_legajo = $legajo;
    }

    //Metodos
    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        } else {
            return "La propiedad \"" . $name . "\" no existe.<br/>";
        }
    }

    public function __set($name, $value)
    {
        if (property_exists($this, $name)) {
            $this->$name = $value;
        } else {
            echo "La propiedad \"" . $name . "\" no existe.<br/>";
        }
    }

    public function __toString()
    {
        return "Profesor: " . $this->_nombre . ", Legajo: " . $this->_legajo . "<br>";
    }

    private function validateNombre()
    {
        $flag = false;
        $nombre = $this->_nombre;

        if ($nombre!="") {
            $flag = true;
        }

        return $flag;
    }

    public static function GetAll()
    {
        $listProfesores = array();
        $path = "./archivos/profesores.json";

        if(file_exists($path)) {
            $fileStream = fopen($path, "r");
            if($fileStream != false) {
                while(!feof($fileStream)) {
                    $linea = trim(fgets($fileStream));
                    if($linea != "")
                    { 
                        $auxJWT = json_decode($linea);
                        $objProfesor = new profesor($auxJWT->nombre, $auxJWT->legajo);
                        array_push($listProfesores, $objProfesor); 
                    }
                }
                fclose($fileStream);
            }
        }
        return $listProfesores;
    }

    private function validateLegajo()
    {
        $flag = false;
        $legajo = $this->_legajo;

        if($legajo > 0)
        {
            $listProfesores = profesor::GetAll();
            foreach($listProfesores as $aux){
                if($aux->_legajo == $legajo){
                    return false;
                } 
            }
            $flag = true;
        }

        return $flag;
    }

    public function validateProfesor()
    {
        if ($this != null) {
            return $this->validateNombre() && $this->validateLegajo();
        }
    }

    public function ToJson()
    {
        $flag = new stdClass();
        if($this != null)
        {
            $flag->nombre = $this->_nombre;
            $flag->legajo = $this->_legajo;
        }
        return json_encode($flag);
    }

    public function saveFile() : bool
    {
        $path = "./archivos/profesores.json";
        $flag = false;
        $fileStream = fopen($path,"a");

        if($fileStream != false) 
        {
            if(fwrite($fileStream, $this->toJson()."\r\n")) {
                $flag = true;
            }
            fclose($fileStream); 
        }

        return $flag;
    }
}