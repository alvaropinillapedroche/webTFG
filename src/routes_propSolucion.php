<?php

use Slim\Http\Request;
use Slim\Http\Response;
use TDW18\PFinal\Entity\Usuario;
use TDW18\PFinal\Entity\Cuestion;
use TDW18\PFinal\Entity\propSolucion;


$app->post(
    $_ENV['RUTA_API'] . '/propuestas/solucion',
    function (Request $request, Response $response): Response {
        $req_data = json_decode($request->getBody(), true);

        $entityManager = getEntityManager();
        /** @var TDW18\PFinal\Entity\Cuestion $cuestion */
        $cuestion = $entityManager->getRepository(Cuestion::class)->findOneBy([ 'idCuestion' => $req_data['idCuestion'] ]);

        /** @var TDW18\PFinal\Entity\Usuario $creador */
        $creador = $entityManager->getRepository(Usuario::class)->findOneBy([ 'idUsuario' => $req_data['idCreador'] ]);

        // 201 - OK
        $propSolucion = new propSolucion($req_data['propuesta'], $cuestion, $creador);
        $entityManager->persist($propSolucion);
        $entityManager->flush();

        $this->logger->info(
            $request->getMethod() . ' ' . $request->getUri()->getPath(),
            [ 'uid' => $this->jwt->user_id, 'status' => 201 ]
        );

        return $response->withStatus(201);
    }
)->setName('tdw_post_propSolucion');

$app->put(
    $_ENV['RUTA_API'] . '/propuestas/solucion/{id:[0-9]+}',
    function (Request $request, Response $response, array $args): Response {
        $req_data = json_decode($request->getBody(), true);
        $entityManager = getEntityManager();
        /** @var TDW18\PFinal\Entity\PropSolucion $propSolucion */
        $propSolucion = $entityManager->getRepository(PropSolucion::class)->findOneBy([ 'idPropSolucion' => $args['id'] ]);

        if(isset($req_data['error'])) {
            $propSolucion->setCorrecta(false);
            $propSolucion->setError($req_data['error']);
        }
        $propSolucion->setCorregida(true);

        $entityManager->flush();

        $this->logger->info(
            $request->getMethod() . ' ' . $request->getUri()->getPath(),
            [ 'uid' => $this->jwt->user_id, 'status' => 209 ]
        );
        return $response->withStatus(209, 'Propuesta actualizada');
    }
)->setName('tdw_put_propSolucion');