<?php
/**
 * PHP version 7.2
 * src\routes.php
 */

use Slim\Http\Request;
use Slim\Http\Response;
use TDW18\PFinal\Entity\Usuario;
use TDW18\PFinal\Entity\PropSolucion;
use TDW18\PFinal\Entity\PropRazonamiento;
use TDW18\PFinal\Messages;

require_once __DIR__ . '/../bootstrap.php';

require __DIR__ . '/routes_user.php';
require __DIR__ . '/routes_cuestiones.php';
require __DIR__ . '/routes_soluciones.php';
require __DIR__ . '/routes_razonamientos.php';
require __DIR__ . '/routes_propSolucion.php';
require __DIR__ . '/routes_propRazonamiento.php';


/**  @var \Slim\App $app */
/** @noinspection PhpUnusedParameterInspection */
$app->get(
    '/',
    function (Request $request, Response $response): Response {

        $this->logger->info(
            $request->getMethod() . ' ' . $request->getUri()->getPath(),
            [ 'uid' => 0, 'status' => 302 ]
        );

        return $response->withRedirect('/html/login.html');
    }
);

$app->post(
    $_ENV['RUTA_LOGIN'],
    function (Request $request, Response $response): Response {
        $req_data = $request->getParsedBody();

        /** @var TDW18\PFinal\Entity\Usuario $user */
        $user = getEntityManager()->getRepository(Usuario::class)
                ->findOneBy(['username' => $req_data['username']]);

        if ($user == null || !$user->validatePassword($req_data['pass'])) {
            $this->logger->info(
                $request->getMethod() . ' ' . $request->getUri()->getPath(),
                [ 'status' => 404 ]
            );

            if($user == null)
                $mensaje = Messages::MESSAGES['tdw_post_login_404_user'];
            else
                $mensaje = Messages::MESSAGES['tdw_post_login_404_pass'];

            return $response
                ->withJson(
                    [
                        'code' => 404,
                        'message' => $mensaje
                    ],
                    404
                );
        }

        $json_web_token = \TDW18\PFinal\Utils::getToken(
            $user->getIdUsuario(),
            $user->getUsername(),
            $user->isEsMaestro()
        );
        $this->logger->info(
            $request->getMethod() . ' ' . $request->getUri()->getPath(),
            [ 'uid' => $user->getIdUsuario(), 'status' => 200 ]
        );

        return $response
            ->withJson(
                [
                    'id' => $user->getIdUsuario(),
                    'username' => $user->getUsername(),
                    'token' => $json_web_token,
                    'esMaestro' => $user->isEsMaestro(),
                    'activo' => $user->isActivo()
                ]
            );
    }
)->setName('tdw_post_login');

$app->post(
    $_ENV['RUTA_API'] . '/register',
    function (Request $request, Response $response): Response {
        $req_data = json_decode($request->getBody(), true);

        $entityManager = getEntityManager();

        $username = $entityManager->getRepository(Usuario::class)->findOneBy([ 'username' => $req_data['username'] ]);

        // 400 - username ya existente
        if(!empty($username)){
            $this->logger->info(
                $request->getMethod() . ' ' . $request->getUri()->getPath(),
                [ 'uid' => $this->jwt->user_id, 'status' => 400 ]
            );
            return $response->withStatus(400);
        }

        // 201 - OK
        $usuario = new Usuario($req_data['username'], $req_data['pass']);

        $entityManager->persist($usuario);
        $entityManager->flush();

        $this->logger->info(
            $request->getMethod() . ' ' . $request->getUri()->getPath(),
            [ 'uid' => $usuario->getIdUsuario(), 'status' => 201 ]
        );
        $json_web_token = \TDW18\PFinal\Utils::getToken(
            $usuario->getIdUsuario(),
            $usuario->getUsername(),
            $usuario->isEsMaestro()
        );

        return $response
            ->withJson(
                [
                    'id' => $usuario->getIdUsuario(),
                    'username' => $usuario->getUsername(),
                    'token' => $json_web_token,
                ],
                201
            );
    }
)->setName('tdw_register_users');

$app->get( // GET Propuestas (de solución y razonamiento)
    $_ENV['RUTA_API'] . '/propuestas/{id:[0-9]+}',
    function (Request $request, Response $response, $args): Response {
        /** @var TDW18\PFinal\Entity\Usuario $usuario */
        $usuario = getEntityManager()->getRepository(Usuario::class)->findOneBy(['idUsuario' => $args['id']]);
        $propSolucion = $usuario->getPropSolucion();
        $propRazonamiento = $usuario->getPropRazonamiento();

        if($propSolucion->count() == 0 && $propRazonamiento->count() == 0){
            $this->logger->info(
                $request->getMethod() . ' ' . $request->getUri()->getPath(),
                ['uid' => $this->jwt->user_id, 'status' =>  404]
            );
            return $response->withJson( ['code' => 404], 404);
        }
        else{
            $propuestasSolucion = $propSolucion->map(
                function (PropSolucion $propuesta) {
                    return $propuesta->jsonGetPropuestas();
                }
            );
            $propuestasRazonamiento = $propRazonamiento->map(
                function (PropRazonamiento $propuesta) {
                    return $propuesta->jsonGetPropuestas();
                }
            );

            $this->logger->info(
                $request->getMethod() . ' ' . $request->getUri()->getPath(),
                ['uid' => $this->jwt->user_id, 'status' =>  200]
            );
            return $response->withJson(
                [
                    'code' => 200,
                    'propuestasSolucion' => $propuestasSolucion->getValues(),
                    'propuestasRazonamiento' => $propuestasRazonamiento->getValues()
                ]
            );
        }
    }
)->setName('tdw_get_propuestas');