<?php

use Slim\Http\Request;
use Slim\Http\Response;
use TDW18\PFinal\Entity\Solucion;
use TDW18\PFinal\Entity\Razonamiento;


$app->post(
    $_ENV['RUTA_API'] . '/razonamientos',
    function (Request $request, Response $response): Response {
        $req_data = json_decode($request->getBody(), true);

        $entityManager = getEntityManager();
        /** @var TDW18\PFinal\Entity\Solucion $solucion */
        $solucion = $entityManager->getRepository(Solucion::class)->findOneBy([ 'idSolucion' => $req_data['idSolucion'] ]);

        // 201 - OK
        $razonamiento = new Razonamiento($req_data['texto'], $req_data['justificado'], $req_data['error'] ?? null, $solucion);
        $entityManager->persist($razonamiento);
        $entityManager->flush();

        $this->logger->info(
            $request->getMethod() . ' ' . $request->getUri()->getPath(),
            [ 'uid' => $this->jwt->user_id, 'status' => 201 ]
        );

        return $response->withJson(
            [
                'code' => 201
            ],
            201
        );
    }
)->setName('tdw_post_razonamientos');

$app->delete(
    $_ENV['RUTA_API'] . '/razonamientos/{id:[0-9]+}',
    function (Request $request, Response $response, $args): Response {
        $entityManager = getEntityManager();
        $razonamiento = $entityManager->getRepository(Razonamiento::class)->findOneBy([ 'idRazonamiento' => $args['id'] ]);
        $this->logger->info(
            $request->getMethod() . ' ' . $request->getUri()->getPath(),
            ['uid' => $this->jwt->user_id, 'status' => 204]
        );

        $entityManager->remove($razonamiento);
        $entityManager->flush();
        return $response->withStatus(204);
    }
)->setName('tdw_delete_razonamiento');

$app->put(
    $_ENV['RUTA_API'] . '/razonamientos/{id:[0-9]+}',
    function (Request $request, Response $response, array $args): Response {
        $req_data = json_decode($request->getBody(), true);
        $entityManager = getEntityManager();
        /** @var TDW18\PFinal\Entity\Razonamiento $razonamiento */
        $razonamiento = $entityManager->getRepository(Razonamiento::class)->findOneBy([ 'idRazonamiento' => $args['id'] ]);

        $razonamiento->setTexto($req_data['texto']);
        $razonamiento->setJustificado($req_data['justificado']);

        if(isset($req_data['error']))
            $razonamiento->setError($req_data['error']);

        $entityManager->flush();

        $this->logger->info(
            $request->getMethod() . ' ' . $request->getUri()->getPath(),
            [ 'uid' => $this->jwt->user_id, 'status' => 209 ]
        );
        return $response->withStatus(209, 'Razonamiento actualizado')
            ->withJson([ 'code' => 209]);
    }
)->setName('tdw_put_razonamiento');