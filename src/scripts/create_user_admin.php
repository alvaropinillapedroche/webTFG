<?php
/**
 * PHP version 7.2
 * src\scripts\create_user_admin.php
 */

require_once __DIR__ . '/../../bootstrap.php';

use TDW18\PFinal\Entity\Usuario;

// Carga las variables de entorno
$dotenv = new \Dotenv\Dotenv(__DIR__ . '/../..', \TDW18\PFinal\Utils::getEnvFileName(__DIR__ . '/../..'));
$dotenv->load();
$dotenv->required([
    'DATABASE_HOST',
    'DATABASE_NAME',
    'DATABASE_USER',
    'DATABASE_PASSWD',
    'DATABASE_DRIVER',
    'ADMIN_USER_NAME',
    'ADMIN_USER_EMAIL',
    'ADMIN_USER_PASSWD'
]);

$user = new Usuario();
$user->setUsername($_ENV['ADMIN_USER_NAME']);
$user->setPassword($_ENV['ADMIN_USER_PASSWD']);
$user->setEsMaestro(true);
$user->setActivo(true);

try {
    $em = getEntityManager();
    $em->persist($user);
    $em->flush();
} catch (\Doctrine\ORM\ORMException $e) {
    die('ERROR: ' . $e->getMessage());
}
