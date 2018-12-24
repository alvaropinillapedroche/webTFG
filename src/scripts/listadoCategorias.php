<?php
/**
 * PHP version 7.2
 * src\scripts\listadoCategorias.php
 */

use TDW18\Usuarios\Entity\Categoria;

require 'inicio.php';

try {
    $entityManager = getEntityManager();
    $categorias = $entityManager->getRepository(Categoria::class)->findAll();
    $entityManager->close();
} catch (\Doctrine\ORM\ORMException $e) {
    exit('ERROR (' . $e->getCode() . '): ' . $e->getMessage());
}

// Salida formato JSON
if (in_array('--json', $argv)) {
    echo json_encode($categorias, JSON_PRETTY_PRINT);
    exit();
}

/** @var Categoria $categoria */
foreach ($categorias as $categoria) {
    echo $categoria . PHP_EOL;
}
