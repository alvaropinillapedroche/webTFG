<?php
/**
 * PHP version 7.2
 * src\scripts\nuevaCuestion.php
 */

use TDW18\Usuarios\Entity\Cuestion;
use TDW18\Usuarios\Entity\Usuario;

require 'inicio.php';

if ($argc < 2) {
    $texto = <<< ______MOSTRAR_USO
    *> Empleo: {$argv[0]} <descripcion> <maestro> [<disponible=0>]
    Crea una nueva cuestión

______MOSTRAR_USO;
    die($texto);
}

try {
    $entityManager = getEntityManager();
    $usuario = $entityManager
        ->getRepository(Usuario::class)
        ->findOneBy([ 'username' => ($argv[2] ?? null) ]);
    if (isset($argv[2]) && null === $usuario) {
        throw new \Doctrine\Common\CommonException('Maestro no encontrado');
    }
    $cuestion = new Cuestion($argv[1], $usuario, $argv[3] ?? false);
    $entityManager->persist($cuestion);
    $entityManager->flush();
    echo 'Creada cuestión Id: ' . $cuestion->getIdCuestion() . PHP_EOL;
} catch (\Exception $e) {
    exit('ERROR (' . $e->getCode() . '): ' . $e->getMessage());
}
