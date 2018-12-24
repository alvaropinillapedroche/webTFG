<?php
/**
 * PHP version 7.2
 * src\dependencies.php
 * DIC configuration
 */

use Interop\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/** @var ContainerInterface $container */
$container = $app->getContainer();

// monolog
$container['logger'] = function (ContainerInterface $c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);

    $rotating = new Monolog\Handler\RotatingFileHandler(
        $settings['path'],
        $settings['maxfiles'],
        $settings['level']
    );
    $logger->pushHandler($rotating);

    return $logger;
};

// notFoundHandler
$container['notFoundHandler'] = function (ContainerInterface $c) {

    return function (Request $request, Response $response) use ($c) {

        return $c['response']
            ->withJson(
                [
                    'code'      => 404,
                    'message'   => 'Path not found'
                ],
                404
            );
    };
};

/** @noinspection PhpUnusedParameterInspection */
$container['jwt'] = function (ContainerInterface $container) {
    return new StdClass;
};
