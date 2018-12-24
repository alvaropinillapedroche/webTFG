<?php
/**
 * PHP version 7.2
 * src\scripts\listadoCuestiones.php
 */

use TDW18\Usuarios\Entity\Cuestion;

require 'inicio.php';

try {
    $entityManager = getEntityManager();
    $cuestiones = $entityManager->getRepository(Cuestion::class)->findAll();
    $entityManager->close();
} catch (\Doctrine\ORM\ORMException $e) {
    exit('ERROR (' . $e->getCode() . '): ' . $e->getMessage());
}

// Salida formato JSON
if (in_array('--json', $argv)) {
    echo json_encode($cuestiones, JSON_PRETTY_PRINT);
    exit();
}

/** @var Cuestion $cuestion */
foreach ($cuestiones as $cuestion) {
    echo $cuestion . PHP_EOL;
}
