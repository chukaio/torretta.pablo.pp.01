<?php
class Usuario
{
    //Atributos
    private $_email;
    private $_clave;
    private $_foto;

    //Constructor
    public function __construct($email, $clave, $foto)
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
        return "Email: ".$this->_clave.", CLave: ".$this->_clave.", Foto: ".$this->_foto."<br>";
    }

    private function validateEmail(){
        $flag = false;
        $email=$this->_email;

        if($email!="" && strlen($email)>6){
            $flag = true;
        }

        return $flag;
    }

    private function validatePassword(){
        $flag = false;
        $clave=$this->_clave;

        if(strlen($clave)>7 && strlen($clave)<29){
            $flag = true;
        }

        return $flag;
    }

    public function validateUser(){
        if($this!=null){
            return $this->validateEmail() && $this->validatePassword();
        }
    }

    public function setPhotoName(){

    }
}
