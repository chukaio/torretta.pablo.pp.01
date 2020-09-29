<?php

use \Firebase\JWT\JWT;
use \Firebase\JWT\SignatureInvalidException;

class materia{
    //Atributos
    private $_id;
    private $_nombre;
    private $_cuatrimestre;

    //Constructor
    public function __construct($nombre, $cuatrimestre, $id=null)
    {
        $this->_id = $id != null ? $id : date("H-i-s");
        $this->_nombre = $nombre;
        $this->_cuatrimestre = $cuatrimestre;
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
        return "Id: ".$this->_id .", Materia: " . $this->_nombre . ", Cuatrimestre: " . $this->_cuatrimestre . "<br>";
    }

    private function validateNombre()
    {
        $flag = false;
        $nombre = $this->_nombre;

        if (strlen($nombre) > 0) {
            $flag = true;
        }

        return $flag;
    }

    private function validateCuatrimestre()
    {
        $flag = false;
        $cuatrimestre = $this->_cuatrimestre;

        if (strlen($cuatrimestre) > 0) {
            $flag = true;
        }

        return $flag;
    }

    public function validateMateria()
    {
        if ($this != null) {
            return $this->validateNombre() && $this->validateCuatrimestre();
        }
    }

    public function toJson()
    {
        $flag = new stdClass();
        if($this != null)
        {
            $flag->id = $this->_id;
            $flag->nombre = $this->_nombre;
            $flag->cuatrimestre = $this->_cuatrimestre;
        }
        return json_encode($flag);
    }

    public function saveFile() : bool
    {
        $path = "./archivos/materias.json";
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

    public static function GetAll()
    {
        $listMaterias = array();
        $path = "./archivos/materias.json";

        if(file_exists($path)) { 
            $fileStream = fopen($path, "r"); 
            if($fileStream != false) {
                while(!feof($fileStream)) {
                    $linea = trim(fgets($fileStream)); 
                    if($linea != "") 
                    { 
                        $auxJWT = json_decode($linea); 
                        $auxObjMateria = new Materia($auxJWT->nombre, $auxJWT->cuatrimestre, $auxJWT->id);
                        array_push($listMaterias, $auxObjMateria);
                    }
                }
                fclose($fileStream);
            }
        }
        return $listMaterias;
    }

    
}