<?php

use Slim\Http\Request;
use Slim\Http\Response;
use TDW18\PFinal\Entity\Cuestion;
use TDW18\PFinal\Entity\Solucion;
use TDW18\PFinal\Entity\Razonamiento;
use TDW18\PFinal\Messages;


$app->post(
    $_ENV['RUTA_API'] . '/soluciones',
    function (Request $request, Response $response): Response {
        $req_data = json_decode($request->getBody(), true);

        $entityManager = getEntityManager();
        /** @var TDW18\PFinal\Entity\Cuestion $cuestion */
        $cuestion = $entityManager->getRepository(Cuestion::class)->findOneBy([ 'idCuestion' => $req_data['idCuestion'] ]);

        // 201 - OK
        $solucion = new Solucion($req_data['texto'], $req_data['correcta'], $cuestion);
        $entityManager->persist($solucion);
        $entityManager->flush();

        $this->logger->info(
            $request->getMethod() . ' ' . $request->getUri()->getPath(),
            [ 'uid' => $this->jwt->user_id, 'status' => 201 ]
        );

        return $response->withJson(
            [
                'code' => 201,
                'id'   => $solucion->getIdSolucion()
            ],
            201
        );
    }
)->setName('tdw_post_soluciones');

$app->delete(
    $_ENV['RUTA_API'] . '/soluciones/{id:[0-9]+}',
    function (Request $request, Response $response, $args): Response {
        $entityManager = getEntityManager();
        $solucion = $entityManager->getRepository(Solucion::class)->findOneBy([ 'idSolucion' => $args['id'] ]);
        $this->logger->info(
            $request->getMethod() . ' ' . $request->getUri()->getPath(),
            ['uid' => $this->jwt->user_id, 'status' => 204]
        );

        $entityManager->remove($solucion);
        $entityManager->flush();
        return $response->withStatus(204);
    }
)->setName('tdw_delete_solucion');

$app->put(
    $_ENV['RUTA_API'] . '/soluciones/{id:[0-9]+}',
    function (Request $request, Response $response, array $args): Response {
        $req_data = json_decode($request->getBody(), true);
        $entityManager = getEntityManager();
        /** @var TDW18\PFinal\Entity\Solucion $solucion */
        $solucion = $entityManager->getRepository(Solucion::class)->findOneBy([ 'idSolucion' => $args['id'] ]);

        $solucion->setTexto($req_data['texto']);
        $solucion->setCorrecta($req_data['correcta']);

        $entityManager->flush();

        $this->logger->info(
            $request->getMethod() . ' ' . $request->getUri()->getPath(),
            [ 'uid' => $this->jwt->user_id, 'status' => 209 ]
        );
        return $response->withStatus(209, 'Solucion actualizada')
            ->withJson([ 'code' => 209]);
    }
)->setName('tdw_put_solucion');