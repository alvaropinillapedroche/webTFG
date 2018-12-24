<?php
/**
 * PHP version 7.2
 * src\scripts\asignaCuestion_a_Categoria.php
 */

require __DIR__ . '/inicio.php';

use TDW18\Usuarios\Entity\Cuestion;
use TDW18\Usuarios\Entity\Categoria;

if ($argc < 3) {
    die($argv[0] . ' <idCuestion> <idCategoria>' . PHP_EOL);
}
[$comando, $idCuestion, $idCategoria] = $argv;

// recuperamos la categoría y la cuestión
try {
    $entityManager = getEntityManager();
    /** @var Cuestion $cuestion */
    $cuestion = $entityManager->find(Cuestion::class, intval($idCuestion));
    /** @var Categoria $categoria */
    $categoria = $entityManager->find(Categoria::class, intval($idCategoria));

    if (null === $categoria) {
        die('ERROR: Categoría no encontrada' . PHP_EOL);
    }
    if (null === $cuestion) {
        die('ERROR: Cuestión no encontrada' . PHP_EOL);
    }

    $categoria->addCuestion($cuestion);
    $entityManager->persist($categoria);
    $entityManager->flush();
    echo 'Cuestión ' . $cuestion->getIdCuestion() . ' asignada a la categoría ' . $categoria->getIdCategoria();
} catch (\Exception $e) {
    die('ERROR: ' . $e->getMessage());
}
