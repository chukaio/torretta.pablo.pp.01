<?php

use \Firebase\JWT\JWT;
use \Firebase\JWT\SignatureInvalidException;

class Usuario
{
    //Atributos
    private $_email;
    private $_clave;
    private $_foto;

    //Constructor
    public function __construct($email, $clave, $foto=null)
    {
        $this->_email = $email;
        $this->_clave = $clave;
        $this->_foto = $foto;
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
        return "Email: " . $this->_email . ", Clave: " . $this->_clave . ", Foto: " . $this->_foto . "<br>";
    }

    private function validateEmail()
    {
        $flag = false;
        $email = $this->_email;

        if ($email != "" && strlen($email) > 6) {
            $flag = true;
        }

        return $flag;
    }

    private function validatePassword()
    {
        $flag = false;
        $clave = $this->_clave;

        if (strlen($clave) > 7 && strlen($clave) < 29) {
            $flag = true;
        }

        return $flag;
    }

    public function validateUser()
    {
        if ($this != null) {
            return $this->validateEmail() && $this->validatePassword();
        }
    }

    public function setPhotoName()
    {
        if ($this != null) {
            $foto = $this->_foto;
            $name=explode(".",$foto);
            $extension = pathinfo($foto, PATHINFO_EXTENSION);
            $this->_foto=$name[0]."_".date("H-i-s").".".$extension;
        }        
    }

    public static function movePhoto($source, $destination)
    {
        return move_uploaded_file($source, $destination);
    }

    private function setPasswordToJWT()
    {
        $keyJWT = "pro3-parcial";
        $payload = array(
            "clave" => $this->_clave,
        );

        return JWT::encode($payload, $keyJWT); 
    }

    private function toJson()
    {
        $flag = new stdClass();
        if($this != null)
        {
            $flag->email = $this->_email;
            $flag->clave = $this->setPasswordToJWT();
            $flag->foto = $this->_foto;
        }
        return json_encode($flag);
    }

    public function saveFile() : bool
    {
        $path = "./archivos/users.json";
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

    public static function getAllUsers()
    {
        $listUsers = array();
        $path = "./archivos/users.json";

        if(file_exists($path)) { 
            $fileStream = fopen($path, "r");
            if($fileStream != false) {
                while(!feof($fileStream)) {
                    $linea = trim(fgets($fileStream)); 
                    if($linea != "") 
                    { 
                        $auxJWT = json_decode($linea);
                        $objAux = new Usuario($auxJWT->email, $auxJWT->clave, $auxJWT->foto);
                        array_push($listUsers, $objAux);  
                    }
                }
                fclose($fileStream);
            }
        }
        return $listUsers;
    }

    public function getUser()
    {
        $email = $this->_email;
        $tokenJWT = $this->setPasswordToJWT();
        $listUsers = Usuario::getAllUsers();
        
        foreach($listUsers as $aux){
            if($aux->_email == $email && $aux->_clave == $tokenJWT){
                
                return $aux;
            } 
        }
    }
}
