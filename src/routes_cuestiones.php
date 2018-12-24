<?php

use Slim\Http\Request;
use Slim\Http\Response;
use TDW18\PFinal\Entity\Usuario;
use TDW18\PFinal\Entity\Cuestion;
use TDW18\PFinal\Messages;

$app->get( // GET Cuestiones (de un usuario)
    $_ENV['RUTA_API'] . '/cuestiones/{id:[0-9]+}',
    function (Request $request, Response $response, $args): Response {
        /** @var TDW18\PFinal\Entity\Usuario $usuario */
        $usuario = getEntityManager()->getRepository(Usuario::class)->findOneBy(['idUsuario' => $args['id']]);
        $cuestiones = $usuario->getCuestiones();

        if(!$cuestiones->isEmpty()) {
            $cuestionesArray = array();
            for ($i = 0; $i < $cuestiones->count(); $i++) {
                /** @var TDW18\PFinal\Entity\Cuestion $cuestion */
                $cuestion = $cuestiones[$i];
                array_push($cuestionesArray, $cuestion->jsonLigero());
            }
            $this->logger->info(
                $request->getMethod() . ' ' . $request->getUri()->getPath(),
                ['uid' => $this->jwt->user_id, 'status' =>  200]
            );
            return $response->withJson($cuestionesArray);
        }
        else{
            $this->logger->info(
                $request->getMethod() . ' ' . $request->getUri()->getPath(),
                ['uid' => $this->jwt->user_id, 'status' =>  404]
            );
            return $response
                ->withJson(
                    [
                        'code' => 404,
                        'message' => Messages::MESSAGES['tdw_get_cuestiones_404']
                    ],
                    404
                );
        }

    }
)->setName('tdw_get_cuestiones_usuario');

$app->get( // GET Cuestiones (solo disponibles)
    $_ENV['RUTA_API'] . '/cuestiones/disponibles',
    function (Request $request, Response $response): Response {

        $cuestiones = getEntityManager()->getRepository(Cuestion::class)->findBy([ 'disponible' => true ]);

        if(!empty($cuestiones)){
            $cuestionesArray = array();
            for($i = 0; $i < count($cuestiones); $i++){
                /** @var TDW18\PFinal\Entity\Cuestion $cuestion */
                $cuestion = $cuestiones[$i];
                array_push($cuestionesArray, $cuestion->jsonLigero());
            }
            $this->logger->info(
                $request->getMethod() . ' ' . $request->getUri()->getPath(),
                ['uid' => $this->jwt->user_id, 'status' =>  200]
            );
            return $response->withJson($cuestionesArray);
        }
        else{
            $this->logger->info(
                $request->getMethod() . ' ' . $request->getUri()->getPath(),
                ['uid' => $this->jwt->user_id, 'status' =>  404]
            );
            return $response
                ->withJson(
                    [
                        'code' => 404,
                        'message' => Messages::MESSAGES['tdw_get_cuestiones_404']
                    ],
                    404
                );
        }
    }
)->setName('tdw_get_cuestiones_disponibles');

$app->get( // GET Cuestion (maestro)
    $_ENV['RUTA_API'] . '/cuestiones/cuestion/maestro/{id:[0-9]+}',
    function (Request $request, Response $response, $args): Response {

        /** @var TDW18\PFinal\Entity\Cuestion $cuestion */
        $cuestion = getEntityManager()->getRepository(Cuestion::class)->findOneBy(['idCuestion' => $args['id']]);

        $this->logger->info(
            $request->getMethod() . ' ' . $request->getUri()->getPath(),
            ['uid' => $this->jwt->user_id, 'status' =>  200]
        );
        return $response->withJson($cuestion);

    }
)->setName('tdw_get_cuestion_maestro');

$app->get( // GET Cuestion (aprendiz)
    $_ENV['RUTA_API'] . '/cuestiones/cuestion/aprendiz/{id:[0-9]+}',
    function (Request $request, Response $response, $args): Response {

        /** @var TDW18\PFinal\Entity\Cuestion $cuestion */
        $cuestion = getEntityManager()->getRepository(Cuestion::class)->findOneBy(['idCuestion' => $args['id']]);

        $this->logger->info(
            $request->getMethod() . ' ' . $request->getUri()->getPath(),
            ['uid' => $this->jwt->user_id, 'status' =>  200]
        );
        return $response->withJson($cuestion->jsonSimplificado());

    }
)->setName('tdw_get_cuestion_maestro');

$app->post(
    $_ENV['RUTA_API'] . '/cuestiones',
    function (Request $request, Response $response): Response {
        $req_data = json_decode($request->getBody(), true);

        $entityManager = getEntityManager();
        /** @var TDW18\PFinal\Entity\Usuario $usuario */
        $usuario = $entityManager->getRepository(Usuario::class)->findOneBy([ 'idUsuario' => $req_data['idCreador'] ]);

        // 201 - OK
        $cuestion = new Cuestion($req_data['enunciado'], $req_data['disponible'], $usuario);
        $entityManager->persist($cuestion);
        $entityManager->flush();

        $this->logger->info(
            $request->getMethod() . ' ' . $request->getUri()->getPath(),
            [ 'uid' => $this->jwt->user_id, 'status' => 201 ]
        );

        return $response->withJson(
            [
                'code' => 201,
                'id'   => $cuestion->getIdCuestion()
            ],
            201
        );
    }
)->setName('tdw_post_cuestiones');

$app->delete( //DELETE Cuestion
    $_ENV['RUTA_API'] . '/cuestiones/{id:[0-9]+}',
    function (Request $request, Response $response, $args): Response {
        $entityManager = getEntityManager();
        $cuestion = $entityManager->getRepository(Cuestion::class)->findOneBy([ 'idCuestion' => $args['id'] ]);
        $this->logger->info(
            $request->getMethod() . ' ' . $request->getUri()->getPath(),
            ['uid' => $this->jwt->user_id, 'status' => 204]
        );

        $entityManager->remove($cuestion);
        $entityManager->flush();
        return $response->withStatus(204);
    }
)->setName('tdw_delete_cuestion');

$app->put(
    $_ENV['RUTA_API'] . '/cuestiones/{id:[0-9]+}',
    function (Request $request, Response $response, array $args): Response {
        $req_data = json_decode($request->getBody(), true);
        $entityManager = getEntityManager();
        /** @var TDW18\PFinal\Entity\Cuestion $cuestion */
        $cuestion = $entityManager->getRepository(Cuestion::class)->findOneBy([ 'idCuestion' => $args['id'] ]);

        $cuestion->setEnunciado($req_data['enunciado']);
        $cuestion->setDisponible($req_data['disponible']);

        $entityManager->flush();

        $this->logger->info(
            $request->getMethod() . ' ' . $request->getUri()->getPath(),
            [ 'uid' => $this->jwt->user_id, 'status' => 209 ]
        );
        return $response->withStatus(209, 'Cuestion actualizada')
            ->withJson([ 'code' => 209]);
    }
)->setName('tdw_put_cuestion');