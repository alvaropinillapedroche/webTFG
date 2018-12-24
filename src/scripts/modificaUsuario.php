<?php
/**
 * PHP version 7.2
 * src\scripts\modificaUsuario.php
 */

use TDW18\Usuarios\Entity\Usuario;

require 'inicio.php';

if ($argc < 3) {
    $texto = <<< ______MOSTRAR_USO
    *> Empleo: {$argv[0]} <idUsuario> [username=<nuevoUsername>] [email=<nuevoEmail>] [esMaestro={0|1}]
    Modifica las propiedades del usuario indicado por <idUsuario>

______MOSTRAR_USO;
    die($texto);
}

try {
    $nombre = $argv[1];
    $entityManager = getEntityManager();
    /** @var Usuario $usuario */
    $usuario = $entityManager
        ->find(Usuario::class, $nombre);
    if (null === $usuario) {
        die('Usuario [' . $nombre . '] no existe.' .PHP_EOL);
    }

    // Modificaciones
    for ($i = 2; $i < $argc; $i++) {
        [$clave, $valor] = explode('=', $argv[$i]);
        switch ($clave) {
            case 'username':
                $usuario->setUsername($valor);
                break;
            case 'email':
                $usuario->setEmail($valor);
                break;
            case 'esMaestro':
                $usuario->setMaestro($valor);
                break;
            default:
                echo sprintf("Argumento %s desconocido", $clave);
        }
    }

    $entityManager->flush();
    echo $usuario;
} catch (\Exception $e) {
    exit('ERROR (' . $e->getCode() . '): ' . $e->getMessage());
}
