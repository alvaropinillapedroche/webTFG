<?php
/**
 * PHP version 7.2
 * src\routes_user.php
 */

use Slim\Http\Request;
use Slim\Http\Response;
use TDW18\PFinal\Entity\Usuario;
use TDW18\PFinal\Messages;

$app->get(
    $_ENV['RUTA_API'] . '/users',
    function (Request $request, Response $response): Response {
        $usuarios = getEntityManager()
            ->getRepository(Usuario::class)
            ->findAll();
        $this->logger->info(
            $request->getMethod() . ' ' . $request->getUri()->getPath(),
            ['uid' => $this->jwt->user_id, 'status' => $usuarios ? 200 : 404]
        );

        return empty($usuarios)
            ? $response
                ->withJson(
                    [
                        'code'      => 404,
                        'message'   => Messages::MESSAGES['tdw_cget_users_404']
                    ],
                    404
                )
            : $response
                ->withJson(
                    [
                        'usuarios' => $usuarios
                    ],
                    200
                );
    }
)->setName('tdw_cget_users');


$app->get(
    $_ENV['RUTA_API'] . '/users/{id:[0-9]+}',
    function (Request $request, Response $response, $args): Response {

        $usuario = getEntityManager()->getRepository(Usuario::class)->findOneBy([ 'idUsuario' => $args['id'] ]);
        $this->logger->info(
            $request->getMethod() . ' ' . $request->getUri()->getPath(),
            ['uid' => $this->jwt->user_id, 'status' =>  200]
        );

        return $response
            ->withJson(
                [
                    'nombre' => $usuario->getNombre(),
                    'apellidos' => $usuario->getApellidos(),
                    'telefono' => $usuario->getTelefono(),
                    'email' => $usuario->getEmail()
                ]
            );
    }
)->setName('tdw_get_users');


$app->delete(
    $_ENV['RUTA_API'] . '/users/{id:[0-9]+}',
    function (Request $request, Response $response, $args): Response {
        $entityManager = getEntityManager();
        $usuario = $entityManager->getRepository(Usuario::class)->findOneBy([ 'idUsuario' => $args['id'] ]);
        $this->logger->info(
            $request->getMethod() . ' ' . $request->getUri()->getPath(),
            ['uid' => $this->jwt->user_id, 'status' => 204]
        );

        $entityManager->remove($usuario);
        $entityManager->flush();
        return $response->withStatus(204);
    }
)->setName('tdw_delete_users');

$app->put(
    $_ENV['RUTA_API'] . '/users/{id:[0-9]+}',
    function (Request $request, Response $response, array $args): Response {
        
        $entityManager = getEntityManager();
        $usuario = $entityManager->getRepository(Usuario::class)->findOneBy([ 'idUsuario' => $args['id'] ]);
        $req_data = json_decode($request->getBody(), true);

        // 400 - email ya existente
        if(isset($req_data['email'])) {
            $email = $entityManager->getRepository(Usuario::class)->findOneBy(['email' => $req_data['email']]);
            if (!empty($email) && $email->getIdUsuario() != $args['id']) {
                $this->logger->info(
                    $request->getMethod() . ' ' . $request->getUri()->getPath(),
                    ['uid' => $this->jwt->user_id, 'status' => 400]
                );
                return $response->withStatus(400);
            } else {
                $usuario->setEmail($req_data['email']);
            }
        }

        //compruebo si hay más atributos a modificar
        if(isset($req_data['nombre']))
            $usuario->setNombre($req_data['nombre']);
        if(isset($req_data['apellidos']))
            $usuario->setApellidos($req_data['apellidos']);
        if(isset($req_data['pass']))
            $usuario->setPassword($req_data['pass']);
        if(isset($req_data['telefono']))
            $usuario->setTelefono($req_data['telefono']);
        if(isset($req_data['activo']))
            $usuario->setActivo($req_data['activo']);
        if(isset($req_data['esMaestro']))
            $usuario->setEsMaestro($req_data['esMaestro']);
        if(isset($req_data['email']))
            $usuario->setEmail($req_data['email']);
        
        $entityManager->flush();
        
        $this->logger->info(
            $request->getMethod() . ' ' . $request->getUri()->getPath(),
            [ 'uid' => $this->jwt->user_id, 'status' => 209 ]
        );
        return $response->withStatus(209, 'Usuario actualizado')
            ->withJson(
                [
                    'activo' => $usuario->isActivo(),
                    'esMaestro' => $usuario->isEsMaestro()
                ]
            );
    }
)->setName('tdw_put_users');