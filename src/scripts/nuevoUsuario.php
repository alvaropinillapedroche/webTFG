<?php
/**
 * PHP version 7.2
 * src\scripts\nuevoUsuario.php
 */

use TDW18\Usuarios\Entity\Usuario;

require 'inicio.php';

try {
    $nombre = 'user-' . random_int(0, 100000);
    $entityManager = getEntityManager();
    $usuario = new Usuario($nombre, $nombre . '@example.com', random_int(0, 1));
    $entityManager->persist($usuario);
    $entityManager->flush();
    echo 'Creado usuario Id: ' . $usuario->getUsername() . PHP_EOL;
} catch (\Doctrine\ORM\ORMException $e) {
    exit('ERROR (' . $e->getCode() . '): ' . $e->getMessage());
}
