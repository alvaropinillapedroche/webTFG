<?php
/**
 * PHP version 7.2
 * src\scripts\eliminaUsuario.php
 */

use TDW18\Usuarios\Entity\Usuario;

require 'inicio.php';

if ($argc !== 2) {
    $texto = <<< ______MOSTRAR_USO
    *> Empleo: {$argv[0]} <idUsuario>
    Elimina el usuario indicado por <idUsuario>

______MOSTRAR_USO;
    die($texto);
}

try {
    $nombre = $argv[1];
    $entityManager = getEntityManager();
    $usuario = $entityManager
        ->find(Usuario::class, $nombre);
    if (null === $usuario) {
        die('Usuario [' . $nombre . '] no existe.' .PHP_EOL);
    }
    $entityManager->remove($usuario);
    $entityManager->flush();
} catch (\Doctrine\ORM\ORMException $e) {
    exit('ERROR (' . $e->getCode() . '): ' . $e->getMessage());
}
