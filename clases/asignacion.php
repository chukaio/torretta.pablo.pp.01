<?php

use \Firebase\JWT\JWT;
use \Firebase\JWT\SignatureInvalidException;

class asignacion
{
    //Atributos
    private $_legajo;
    private $_id;
    private $_turno;

    //Constructor
    public function __construct($legajo, $id, $turno)
    {
        $this->_legajo = $legajo;
        $this->_id = $id;
        $this->_turno = $turno;
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
        return "Legajo: " . $this->_legajo . ", ID: " . $this->_id . ", Turno: " . $this->_turno . "<br>";
    }

    private function validateTurno(){
        $turno = $this->_turno;
        $flag = false;
        
        if($turno != ""){
            $flag = true;
        }

        return $flag;
    }

    private function validateID(){
    {
        $flag = false;
        $id = $this->_id;

        if($id > 0)
        {
            $listMaterias=Materia::GetAll();
            foreach($listMaterias as $aux){
                if($aux->_id == $id){
                    $flag = true;
                } 
            }
        }
        return $flag;
    }
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
                    $flag = true ;
                } 
            }
        }

        return $flag;
    }

    public static function GetAll()
    {
        $listAsignaciones = array();
        $path = "./archivos/materias-profesores.json";

        if(file_exists($path)) {
            $fileStream = fopen($path, "r");
            if($fileStream != false) {
                while(!feof($fileStream)) {
                    $linea = trim(fgets($fileStream));
                    if($linea != "")
                    { 
                        $auxJWT = json_decode($linea);
                        $auxObjAsignacion =new Asignacion($auxJWT->legajo, $auxJWT->id, $auxJWT->turno);;
                        array_push($listAsignaciones, $auxObjAsignacion); 
                    }
                }
                fclose($fileStream);
            }
        }

        return $listAsignaciones;
    }

    private function checkTurnoProfesorUnico(){
        $legajo = $this->_legajoProfesor;
        $id = $this->_id;
        $turno = $this->_turno;
        $listAsignaciones=array();
        $flag = true;

        $listAsignaciones=Asignacion::GetAll();
        foreach($listAsignaciones as $aux)
        {
            if($aux->_turno == $turno && $aux->_legajo == $legajo){
                $flag = false;
            }
            else if($aux->_id == $id && $aux->_legajo == $legajo){
                $flag = false;
            } 
        }
        return $flag;

    }

    public function validateAsignacion()
    {
        if ($this != null) {
            return $this->validateLegajo() && $this->validateID() && $this->validateTurno() && $this->checkTurnoProfesorUnico();
        }
    }

    public function ToJson()
    {
        $flag = new stdClass();
        if($this != null)
        {
            $flag->legajo = $this->_legajo;
            $flag->id = $this->_id;
            $flag->turno = $this->_turno;
        }
        return json_encode($flag);
    }

    public function SaveFile($path = "./archivos/materias-profesores.json")
    {    
        $path = "./archivos/materias-profesores.json";
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

    public static function listMateriasPorProfesor()
    {
        $listAsignaciones = Asignacion::GetAll();
        $listProfesores = Profesor::GetAll();
        $listMaterias = Materia::GetAll();
        $screenString = "Profesor - Materia <br>";

        foreach($listAsignaciones as $asignacion){
            foreach($listProfesores as $profesor){
                if($asignacion->_legajo == $profesor->_legajo){
                    $screenString .= "<br>" . $profesor->ToJson() . ": ";
                    foreach($listMaterias as $materia){
                        if($asignacion->_id == $materia->_id){
                            $screenString .= $materia->ToJson() . ", ";     
                        }
                    }
                }
            }
        }
        return $screenString;
    }
}
