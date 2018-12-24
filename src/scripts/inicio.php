<?php
/**
 * PHP version 7.2
 * doctrine_GCuest18 - inicio.php
 *
 * @author   Javier Gil <franciscojavier.gil@upm.es>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     http://www.etsisi.upm.es ETS de Ingeniería de Sistemas Informáticos
 */

require __DIR__ . '/../../vendor/autoload.php';

// Carga las variables de entorno
$dotenv = new \Dotenv\Dotenv(__DIR__ . '/../../');
$dotenv->load();
$dotenv->required(
    [
        'DATABASE_HOST',
        'DATABASE_NAME',
        'DATABASE_USER',
        'DATABASE_PASSWD',
        'DATABASE_DRIVER'
    ]
);

require_once __DIR__ . '/../../bootstrap.php';
