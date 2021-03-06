<?php

use Slim\Http\Request;
use Slim\Http\Response;
use TDW18\PFinal\Entity\Usuario;
use TDW18\PFinal\Entity\Solucion;
use TDW18\PFinal\Entity\propRazonamiento;


$app->post(
    $_ENV['RUTA_API'] . '/propuestas/razonamiento',
    function (Request $request, Response $response): Response {
        $req_data = json_decode($request->getBody(), true);

        $entityManager = getEntityManager();
        /** @var TDW18\PFinal\Entity\Solucion $solucion */
        $solucion = $entityManager->getRepository(Solucion::class)->findOneBy([ 'idSolucion' => $req_data['idSolucion'] ]);

        /** @var TDW18\PFinal\Entity\Usuario $creador */
        $creador = $entityManager->getRepository(Usuario::class)->findOneBy([ 'idUsuario' => $req_data['idCreador'] ]);

        // 201 - OK
        $propRazonamiento = new propRazonamiento($req_data['propuesta'], $solucion, $creador);
        $entityManager->persist($propRazonamiento);
        $entityManager->flush();

        $this->logger->info(
            $request->getMethod() . ' ' . $request->getUri()->getPath(),
            [ 'uid' => $this->jwt->user_id, 'status' => 201 ]
        );

        return $response->withStatus(201);
    }
)->setName('tdw_post_propSolucion');

$app->put(
    $_ENV['RUTA_API'] . '/propuestas/razonamiento/{id:[0-9]+}',
    function (Request $request, Response $response, array $args): Response {
        $req_data = json_decode($request->getBody(), true);
        $entityManager = getEntityManager();
        /** @var TDW18\PFinal\Entity\PropRazonamiento $propRazonamiento */
        $propRazonamiento = $entityManager->getRepository(PropRazonamiento::class)->findOneBy([ 'idPropRazonamiento' => $args['id'] ]);

        if(isset($req_data['error'])) {
            $propRazonamiento->setJustificada(false);
            $propRazonamiento->setError($req_data['error']);
        }
        $propRazonamiento->setCorregida(true);

        $entityManager->flush();

        $this->logger->info(
            $request->getMethod() . ' ' . $request->getUri()->getPath(),
            [ 'uid' => $this->jwt->user_id, 'status' => 209 ]
        );
        return $response->withStatus(209, 'Propuesta actualizada');
    }
)->setName('tdw_put_propRazonamiento');