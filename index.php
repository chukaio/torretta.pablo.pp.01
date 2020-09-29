<?php
require __DIR__ ."/vendor/autoload.php";
require_once "./clases/usuario.php";
//use \Firebase\JWT\JWT;

$path = $_SERVER['PATH_INFO'] ?? "";
$method = $_SERVER['REQUEST_METHOD'];

switch ($path) {
    case '/usuario':
        //01 --> Registrar a un cliente con email, clave y foto y guardarlo en el archivo users.xxx. Darle un nombre único a la imagen.
        if($method=='POST'){
            $email=$_POST['email']??"";
            $clave=$_POST['clave']??"";
            $foto=$_FILES['foto']['name']??"";
            $user=new Usuario($email, $clave, $foto);

            if($user->validateUser()){
                echo $user;
            }else{
                echo "Usuario incorrecto";
            }
        }
        break;    
    default:
        # code...
        break;
}

// $key = "example_key";
// $payload = array(
//     "iss" => "http://example.org",
//     "aud" => "http://example.com",
//     "iat" => 1356999524,
//     "nbf" => 1357000000
// );

// /**
//  * IMPORTANT:
//  * You must specify supported algorithms for your application. See
//  * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
//  * for a list of spec-compliant algorithms.
//  */
// $jwt = JWT::encode($payload, $key);
// $decoded = JWT::decode($jwt, $key, array('HS256'));

// print_r($decoded);

// /*
//  NOTE: This will now be an object instead of an associative array. To get
//  an associative array, you will need to cast it as such:
// */

//$decoded_array = (array) $decoded;