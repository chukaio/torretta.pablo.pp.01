<?php
require __DIR__ . "/vendor/autoload.php";
require_once "./clases/usuario.php";
require_once "./clases/materia.php";
require_once "./clases/profesor.php";
require_once "./clases/asignacion.php";

$path = $_SERVER['PATH_INFO'] ?? "";
$method = $_SERVER['REQUEST_METHOD'];

echo $path . " y " . $method . "<br>";

switch ($path) {
    case '/usuario':
        //01 --> Registrar a un cliente con email, clave y foto y guardarlo en el archivo users.xxx. Darle un nombre único a la imagen.
        if ($method == 'POST') {
            $email = $_POST['email'] ?? "";
            $clave = $_POST['clave'] ?? "";
            $foto = $_FILES['foto']['name'] ?? "";
            $user = new Usuario($email, $clave, $foto);

            if ($user->validateUser()) {
                $user->setPhotoName();
                $source = $_FILES["foto"]["tmp_name"] ?? "";
                $destination = "./img/" . $user->_foto;
                Usuario::movePhoto($source, $destination);
                if ($user->saveFile()) {
                    echo "Archivo con el usuario guardado exitosamente";
                } else {
                    echo "Ha ocurrido un error al intentar guardar el archivo";
                }
            } else {
                echo "Usuario incorrecto";
            }
        }
        break;
    case '/login':
        //02 --> Recibe email y clave y si son correctos devuelve un JWT, de lo contrario informar lo sucedido. La clave no se debe guardar en texto plano.
        if ($method == 'POST') {
            $email = $_POST['email'] ?? "";
            $clave = $_POST['clave'] ?? "";
            $user = new Usuario($email, $clave);
            $user = $user->getUser();

            if ($user) {
                echo $user->_clave;
            } else {
                echo "El Usuario no esta registrado";
            }
        }
        break;
    case '/materia':
        //03 --> Recibe nombre, cuatrimestre y lo guarda en el archivo materias.xxx. Agregar un id único para cada materia.
        if ($method == 'POST') {
            $nombre = $_POST['nombre'] ?? "";
            $cuatrimestre = $_POST['cuatrimestre'] ?? "";
            $materia = new materia($nombre, $cuatrimestre);

            if ($materia->validateMateria()) {
                if ($materia->saveFile()) {
                    echo "Archivo con la materia guardado exitosamente";
                } else {
                    echo "Ha ocurrido un error al intentar guardar el archivo";
                }
            } else {
                echo "Materia incorrecto";
            }
        } else if ($method == 'GET') {
            //06 --> Muestra un listado con todas las materias.
            var_dump(materia::GetAll());
        }
        break;
    case '/profesor':
        //04 --> Recibe nombre, legajo (validar que sea único) y lo guarda en el archivo profesores.xxx.
        if ($method == 'POST') {
            $nombre = $_POST['nombre'] ?? "";
            $legajo = $_POST['legajo'] ?? "";
            $profesor = new profesor($nombre, $legajo);

            if ($profesor->validateProfesor()) {
                if ($profesor->saveFile()) {
                    echo "Archivo con el profesor guardado exitosamente";
                } else {
                    echo "Ha ocurrido un error al intentar guardar el archivo";
                }
            } else {
                echo "Profesor incorrecto";
            }
        } else if ($method == 'GET') {
            //07 --> Muestra un listado con todas las profesores.
            var_dump(profesor::GetAll());
        }
        break;
    case '/asignacion':
        //05 --> Recibe legajo del profesor, id de la materia y turno (manana o noche) y lo guarda en el archivo materias-profesores. No se debe poder asignar el mismo legajo en el mismo turno y materia.
        if ($method == 'POST') {
            $legajo = $_POST['legajo'] ?? "";
            $id = $_POST['id'] ?? "";
            $turno = $_POST['turno'] ?? "";
            $asignacion = new asignacion($legajo, $id, $turno);

            if ($asignacion->validateAsignacion()) {
                if ($asignacion->saveFile()) {
                    echo "Archivo con la asignacion guardada exitosamente";
                } else {
                    echo "Ha ocurrido un error al intentar guardar el archivo";
                }
            } else {
                echo "Asignacion incorrecta";
            }
        } else if ($method == 'GET') {
            //08 --> Muestra un listado con todas las materias asignadas a cada profesor.
            echo Asignacion::listMateriasPorProfesor();
        }
        break;
    case '/usuario/email':
        //09 --> Recibe una imagen y se la asigna al usuario indicado. Guardar la imagen anterior en la carpeta backup.
        if ($method == 'POST') {
            $foto = $_FILES["foto"]['name'] ?? "";
            $email=$_POST['email'] ?? "";
            $clave=$_POST['clave'] ?? "";
            $usuario=new Usuario($email,$clave,$foto);
            $usuario=$usuario->getUser();

            if($usuario != null){
                $source=$_FILES["foto"]['tmp_name'] ?? "";
                $destination="./img/backup/" . $foto;
                if(usuario::MovePhoto($source, $destination)){
                    if($usuario->saveFile()){
                        echo "Archivo con el usuario guardado exitosamente";
                    }
                    else{
                        echo "Ha ocurrido un error al intentar guardar el archivo";
                    }
                }
            }
            else{
                echo "No existe el usuario ingresado";
            }
        }
        break;
    default:
        break;
}