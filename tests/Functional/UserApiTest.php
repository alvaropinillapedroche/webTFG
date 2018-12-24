<?php
/**
 * PHP version 7.2
 * tests/Functional/UserApiTest.php
 */

namespace TDW18\Usuarios\Tests\Functional;

use TDW18\Usuarios\Entity\Usuario;
use TDW18\Usuarios\Messages;

/**
 * Class UserApiTest
 *
 * @package TDW18\Usuarios\Tests\Functional
 */
class UserApiTest extends BaseTestCase
{
    use \TDW18\Usuarios\Utils;

    private static $ruta_base;

    /**
     * Se ejecuta una vez al inicio de las pruebas de la clase UserApiTest
     */
    public static function setUpBeforeClass()
    {
        self::$ruta_base = $_ENV['RUTA_API'] . '/users';

        // load user admin fixtures
        self::loadUserData(
            $_ENV['ADMIN_USER_NAME'],
            $_ENV['ADMIN_USER_EMAIL'],
            $_ENV['ADMIN_USER_PASSWD'],
            true
        );
    }

    /**
     * Called after the last test of the test case class is run
     */
    public static function tearDownAfterClass()
    {
        self::updateSchema();
    }

    /**
     * Test GET /users 401 UNAUTHORIZED
     */
    public function testCGetUser401()
    {
        $response = $this->runApp('GET', self::$ruta_base);

        self::assertSame(401, $response->getStatusCode());
        self::assertJson((string) $response->getBody());
        $r_body = (string) $response->getBody();
        self::assertContains('code', $r_body);
        self::assertContains('message', $r_body);
        $r_data = json_decode($r_body, true);
        self::assertSame(401, $r_data['code']);
        self::assertSame(
            Messages::MESSAGES['tdw_unauthorized_401'],
            $r_data['message']
        );
    }

    /**
     * Test GET /users/userId 401 UNAUTHORIZED
     */
    public function testGetUser401()
    {
        $response = $this->runApp('GET', self::$ruta_base . '/1');

        self::assertSame(401, $response->getStatusCode());
        self::assertJson((string) $response->getBody());
        $r_body = (string) $response->getBody();
        self::assertContains('code', $r_body);
        self::assertContains('message', $r_body);
        $r_data = json_decode($r_body, true);
        self::assertSame(401, $r_data['code']);
        self::assertSame(
            Messages::MESSAGES['tdw_unauthorized_401'],
            $r_data['message']
        );
    }

    /**
     * Test POST /users
     *
     * @return array user data
     * @throws \Exception
     */
    public function testPostUser201(): array
    {
        $rand_num = random_int(0, 1000000);
        $nombre = 'Nuevo UsEr POST * ' . $rand_num;
        $p_data = [
            'username'  => $nombre,
            'email'     => 'email' . $rand_num . '@example.com',
            'password'  => 'PassW0r4 UsEr POST * ñ?¿ áËì·' . $rand_num,
            'enabled'   => random_int(0, 2),
            'isAdmin'   => random_int(0, 2),
            'isMaestro' => random_int(0, 2)
        ];
        $token = $this->getTokenHeaders(
            $_ENV['ADMIN_USER_NAME'],
            $_ENV['ADMIN_USER_PASSWD']
        );
        $response = $this->runApp('POST', self::$ruta_base, $p_data, $token);

        self::assertSame(201, $response->getStatusCode());
        self::assertJson((string) $response->getBody());
        $user = json_decode((string) $response->getBody(), true);
        self::assertNotEquals($user['usuario']['id'], 0);
        self::assertSame($p_data['username'], $user['usuario']['username']);
        self::assertSame($p_data['email'], $user['usuario']['email']);
        self::assertEquals($p_data['isAdmin'], $user['usuario']['admin']);
        self::assertEquals($p_data['isMaestro'], $user['usuario']['maestro']);
        self::assertEquals($p_data['enabled'], $user['usuario']['enabled']);

        return $user['usuario'];
    }

    /**
     * Test POST /users 422
     * @throws \Exception
     */
    public function testPostUser422()
    {
        $rand_num = random_int(0, 1000000);
        $nombre = 'Nuevo UsEr POST * ' . $rand_num;
        $p_data = [
            // 'username' => $nombre,
            'email' => 'email' . $rand_num . '@example.com',
            'password' => 'PassW0r4 UsEr POST * ñ?¿' . $rand_num
        ];
        $response = $this->runApp(
            'POST',
            self::$ruta_base,
            $p_data,
            $this->getTokenHeaders()
        );

        self::assertSame(422, $response->getStatusCode());
        $r_body = (string) $response->getBody();
        self::assertJson($r_body);
        self::assertContains('code', $r_body);
        self::assertContains('message', $r_body);
        $r_data = json_decode($r_body, true);
        self::assertSame(422, $r_data['code']);
        self::assertSame(
            Messages::MESSAGES['tdw_post_users_422'],
            $r_data['message']
        );

        $p_data = [
            'username' => $nombre,
            // 'email' => 'email' . $rand_num . '@example.com',
            'password' => 'PassW0r4 UsEr POST * ñ?¿' . $rand_num
        ];
        $response = $this->runApp(
            'POST',
            self::$ruta_base,
            $p_data,
            $this->getTokenHeaders()
        );

        self::assertSame(422, $response->getStatusCode());
        $r_body = (string) $response->getBody();
        self::assertJson($r_body);
        self::assertContains('code', $r_body);
        self::assertContains('message', $r_body);
        $r_data = json_decode($r_body, true);
        self::assertSame(422, $r_data['code']);
        self::assertSame(
            Messages::MESSAGES['tdw_post_users_422'],
            $r_data['message']
        );

        $p_data = [
            'username' => $nombre,
            'email' => 'email' . $rand_num . '@example.com',
            // 'password' => 'PassW0r4 UsEr POST * ñ?¿' . $rand_num
        ];
        $response = $this->runApp(
            'POST',
            self::$ruta_base,
            $p_data,
            $this->getTokenHeaders()
        );

        self::assertSame(422, $response->getStatusCode());
        $r_body = (string) $response->getBody();
        self::assertJson($r_body);
        self::assertContains('code', $r_body);
        self::assertContains('message', $r_body);
        $r_data = json_decode($r_body, true);
        self::assertSame(422, $r_data['code']);
        self::assertSame(
            Messages::MESSAGES['tdw_post_users_422'],
            $r_data['message']
        );

        $p_data = [
            'username' => $nombre,
            // 'email' => 'email' . $rand_num . '@example.com',
            // 'password' => 'PassW0r4 UsEr POST * ñ?¿' . $rand_num
        ];
        $response = $this->runApp(
            'POST',
            self::$ruta_base,
            $p_data,
            $this->getTokenHeaders()
        );

        self::assertSame(422, $response->getStatusCode());
        $r_body = (string) $response->getBody();
        self::assertJson($r_body);
        self::assertContains('code', $r_body);
        self::assertContains('message', $r_body);
        $r_data = json_decode($r_body, true);
        self::assertSame(422, $r_data['code']);
        self::assertSame(
            Messages::MESSAGES['tdw_post_users_422'],
            $r_data['message']
        );
    }

    /**
     * Test POST /users 400
     *
     * @param array $user user returned by testPostUser201()
     *
     * @depends testPostUser201
     * @throws \Exception
     */
    public function testPostUser400(array $user)
    {
        $rand_num = random_int(0, 1000000);
        $nombre = 'Nuevo UsEr POST * ' . $rand_num;
        $p_data = [
            'username' => $user['username'],
            'email' => 'emailX' . $rand_num . '@example.com',
            'password' => 'PassW0r4 UsEr POST * ñ?¿' . $rand_num
        ];
        $response = $this->runApp(
            'POST',
            self::$ruta_base,
            $p_data,
            $this->getTokenHeaders()
        );

        self::assertSame(400, $response->getStatusCode());
        $r_body = (string) $response->getBody();
        self::assertJson($r_body);
        self::assertContains('code', $r_body);
        self::assertContains('message', $r_body);
        $r_data = json_decode($r_body, true);
        self::assertSame(400, $r_data['code']);
        self::assertSame(
            Messages::MESSAGES['tdw_post_users_400'],
            $r_data['message']
        );

        $p_data = [
            'username' => $nombre,
            'email' => $user['email'],
            'password' => 'PassW0r4 UsEr POST * ñ?¿' . $rand_num
        ];
        $response = $this->runApp(
            'POST',
            self::$ruta_base,
            $p_data,
            $this->getTokenHeaders()
        );

        self::assertSame(400, $response->getStatusCode());
        $r_body = (string) $response->getBody();
        self::assertJson($r_body);
        self::assertContains('code', $r_body);
        self::assertContains('message', $r_body);
        $r_data = json_decode($r_body, true);
        self::assertSame(400, $r_data['code']);
        self::assertSame(
            Messages::MESSAGES['tdw_post_users_400'],
            $r_data['message']
        );
    }

    /**
     * Test POST /users 401 UNAUTHORIZED
     */
    public function testPostUser401()
    {
        $response = $this->runApp('POST', self::$ruta_base);

        self::assertSame(401, $response->getStatusCode());
        self::assertJson((string) $response->getBody());
        $r_body = (string) $response->getBody();
        self::assertContains('code', $r_body);
        self::assertContains('message', $r_body);
        $r_data = json_decode($r_body, true);
        self::assertSame(401, $r_data['code']);
        self::assertSame(
            Messages::MESSAGES['tdw_unauthorized_401'],
            $r_data['message']
        );
    }

    /**
     * Test GET /users
     *
     * @depends testPostUser201
     * @throws \Exception
     */
    public function testCGetAllUsers200()
    {
        $response = $this->runApp(
            'GET',
            self::$ruta_base,
            null,
            $this->getTokenHeaders()
        );

        self::assertSame(200, $response->getStatusCode());
        $r_body = (string) $response->getBody();
        self::assertJson($r_body);
        self::assertContains('usuarios', $r_body);
        $r_data = json_decode($r_body, true);
        self::assertArrayHasKey('usuarios', $r_data);
        self::assertTrue(is_array($r_data['usuarios']));
    }

    /**
     * Test GET /users/userId
     *
     * @param array $user user returned by testPostUser201()
     *
     * @depends testPostUser201
     * @throws \Exception
     */
    public function testGetUser200(array $user)
    {
        $response = $this->runApp(
            'GET',
            self::$ruta_base . '/' . $user['id'],
            null,
            $this->getTokenHeaders()
        );

        self::assertSame(200, $response->getStatusCode(), 'Headers: ' . json_encode($this->getTokenHeaders()));
        self::assertJson((string) $response->getBody());
        $user_aux = json_decode((string) $response->getBody(), true);
        self::assertSame($user, $user_aux['usuario']);
    }

    /**
     * Test PUT /users/userId
     *
     * @param array $user user returned by testPostUser201()
     *
     * @depends testPostUser201
     *
     * @return array modified user data
     * @throws \Exception
     */
    public function testPutUser209(array $user)
    {
        $rand_num = random_int(0, 1000000);
        $p_data = [
            'username' => 'Nuevo UsEr PUT * ' . $rand_num,
            'email' => 'emailXPUT-' . $rand_num . '@example.com',
            'password' => 'PassW0r4 UsEr PUT * ñ?¿' . $rand_num,
            'enabled' => random_int(0, 2),
            'isAdmin' => random_int(0, 2),
            'isMaestro' => random_int(0, 2)
        ];
        $response = $this->runApp(
            'PUT',
            self::$ruta_base . '/' . $user['id'],
            $p_data,
            $this->getTokenHeaders()
        );

        self::assertSame(209, $response->getStatusCode());
        self::assertJson((string) $response->getBody());
        $user_aux = json_decode((string) $response->getBody(), true);
        self::assertSame($user['id'], $user_aux['usuario']['id']);
        self::assertSame($p_data['username'], $user_aux['usuario']['username']);
        self::assertSame($p_data['email'], $user_aux['usuario']['email']);
        self::assertEquals($p_data['enabled'], $user_aux['usuario']['enabled']);
        self::assertEquals($p_data['isAdmin'], $user_aux['usuario']['admin']);
        self::assertEquals($p_data['isMaestro'], $user_aux['usuario']['maestro']);

        return $user_aux['usuario'];
    }

    /**
     * Test PUT /users 400
     *
     * @param array $user user returned by testPutUser200()
     *
     * @depends testPutUser209
     * @throws \Exception
     */
    public function testPutUser400(array $user)
    {
        // username already exists
        $p_data = [
            'username' => $user['username']
        ];
        $response = $this->runApp(
            'PUT',
            self::$ruta_base . '/' . $user['id'],
            $p_data,
            $this->getTokenHeaders()
        );

        self::assertSame(400, $response->getStatusCode());
        $r_body = (string) $response->getBody();
        self::assertJson($r_body);
        self::assertContains('code', $r_body);
        self::assertContains('message', $r_body);
        $r_data = json_decode($r_body, true);
        self::assertSame(400, $r_data['code']);
        self::assertSame(
            Messages::MESSAGES['tdw_put_users_400'],
            $r_data['message']
        );

        // e-mail already exists
        $p_data = [
            'email' => $user['email']
        ];
        $response = $this->runApp(
            'PUT',
            self::$ruta_base . '/' . $user['id'],
            $p_data,
            $this->getTokenHeaders()
        );

        self::assertSame(400, $response->getStatusCode());
        $r_body = (string) $response->getBody();
        self::assertJson($r_body);
        self::assertContains('code', $r_body);
        self::assertContains('message', $r_body);
        $r_data = json_decode($r_body, true);
        self::assertSame(400, $r_data['code']);
        self::assertSame(
            Messages::MESSAGES['tdw_put_users_400'],
            $r_data['message']
        );
    }

    /**
     * Test PUT /users/userId 401 UNAUTHORIZED
     */
    public function testPutUser401()
    {
        $response = $this->runApp('PUT', self::$ruta_base . '/1');

        self::assertSame(401, $response->getStatusCode());
        self::assertJson((string) $response->getBody());
        $r_body = (string) $response->getBody();
        self::assertContains('code', $r_body);
        self::assertContains('message', $r_body);
        $r_data = json_decode($r_body, true);
        self::assertSame(401, $r_data['code']);
        self::assertSame(
            Messages::MESSAGES['tdw_unauthorized_401'],
            $r_data['message']
        );
    }

    /**
     * Test OPTIONS /users[/userId]
     * @throws \Exception
     */
    public function testOptionsUser()
    {
        /**
         * Response
         *
         * @var \Slim\Http\Response $response
         */
        $response = $this->runApp(
            'OPTIONS',
            self::$ruta_base
        );

        self::assertSame(200, $response->getStatusCode());
        self::assertNotEmpty($response->getHeader('Allow'));

        $response = $this->runApp(
            'OPTIONS',
            self::$ruta_base . '/' . random_int(0, 1000000)
        );

        self::assertSame(200, $response->getStatusCode());
        self::assertNotEmpty($response->getHeader('Allow'));
    }

    /**
     * Prueba DELETE /users/userId
     *
     * @param array $user user returned by testPostUser201()
     *
     * @depends testPostUser201
     * @depends testPostUser400
     * @depends testGetUser200
     * @depends testPutUser400
     *
     * @return int userId
     * @throws \Exception
     */
    public function testDeleteUser204(array $user)
    {
        $response = $this->runApp(
            'DELETE',
            self::$ruta_base . '/' . $user['id'],
            null,
            $this->getTokenHeaders()
        );

        self::assertSame(204, $response->getStatusCode());
        self::assertEmpty((string) $response->getBody());

        return $user['id'];
    }

    /**
     * Test DELETE /users/userId 401 UNAUTHORIZED
     */
    public function testDeleteUser401()
    {
        $response = $this->runApp('DELETE', self::$ruta_base . '/1');

        self::assertSame(401, $response->getStatusCode());
        self::assertJson((string) $response->getBody());
        $r_body = (string) $response->getBody();
        self::assertContains('code', $r_body);
        self::assertContains('message', $r_body);
        $r_data = json_decode($r_body, true);
        self::assertSame(401, $r_data['code']);
        self::assertSame(
            Messages::MESSAGES['tdw_unauthorized_401'],
            $r_data['message']
        );
    }

    /**
     * Test DELETE /users/userId 404 Not Found
     *
     * @param int $userId user id. returned by testDeleteUser204()
     *
     * @depends testDeleteUser204
     * @throws \Exception
     */
    public function testDeleteUser404(int $userId)
    {
        $response = $this->runApp(
            'DELETE',
            self::$ruta_base . '/' . $userId,
            null,
            $this->getTokenHeaders()
        );

        self::assertSame(404, $response->getStatusCode());
        $r_body = (string) $response->getBody();
        self::assertContains('code', $r_body);
        self::assertContains('message', $r_body);
        $r_data = json_decode($r_body, true);
        self::assertSame(404, $r_data['code']);
        self::assertSame(Messages::MESSAGES['tdw_delete_users_404'], $r_data['message']);
    }

    /**
     * Test GET /users/userId 404 Not Found
     *
     * @param int $userId user id. returned by testDeleteUser204()
     *
     * @depends testDeleteUser204
     * @throws \Exception
     */
    public function testGetUser404(int $userId)
    {
        $response = $this->runApp(
            'GET',
            self::$ruta_base . '/' . $userId,
            null,
            $this->getTokenHeaders()
        );

        self::assertSame(404, $response->getStatusCode());
        $r_body = (string) $response->getBody();
        self::assertContains('code', $r_body);
        self::assertContains('message', $r_body);
        $r_data = json_decode($r_body, true);
        self::assertSame(404, $r_data['code']);
        self::assertSame(Messages::MESSAGES['tdw_get_users_404'], $r_data['message']);
    }

    /**
     * Test PUT /users/userId 404 Not Found
     *
     * @param int $userId user id. returned by testDeleteUser204()
     *
     * @depends testDeleteUser204
     * @throws \Exception
     */
    public function testPutUser404(int $userId)
    {
        $response = $this->runApp(
            'PUT',
            self::$ruta_base . '/' . $userId,
            null,
            $this->getTokenHeaders()
        );

        self::assertSame(404, $response->getStatusCode());
        $r_body = (string) $response->getBody();
        self::assertContains('code', $r_body);
        self::assertContains('message', $r_body);
        $r_data = json_decode($r_body, true);
        self::assertSame(404, $r_data['code']);
        self::assertSame(Messages::MESSAGES['tdw_put_users_404'], $r_data['message']);
    }

    /**
     * Test GET /users      403 Forbidden
     *
     * @depends testPutUser404
     *
     * @return Usuario data
     * @throws \Exception
     */
    public function testCGetUser403()
    {
        // Añade un nuevo usuario NO admin
        $rand_num = random_int(0, 1000000);
        $nombre = 'Otro UsEr POST * ' . $rand_num;
        $p_data = [
            'username' => $nombre,
            'email'    => 'email' . $rand_num . '@example.com',
            'password' => 'PassW0r4 UsEr POST * ñ?¿ áËì·' . $rand_num,
            'isAdmin'  => false
        ];
        $response = $this->runApp(
            'POST',
            self::$ruta_base,
            $p_data,
            $this->getTokenHeaders()
        );
        self::assertSame(201, $response->getStatusCode());
        self::assertJson((string) $response->getBody());
        $user = json_decode((string) $response->getBody(), true);

        $response = $this->runApp(
            'GET',
            self::$ruta_base,
            null,
            $this->getTokenHeaders($user['usuario']['username'], $p_data['password'])
        );
        self::assertSame(403, $response->getStatusCode());
        self::assertJson((string)$response->getBody());
        $r_data = json_decode($response->getBody(), true);
        self::assertSame(403, $r_data['code']);
        self::assertSame(
            Messages::MESSAGES['tdw_cget_users_403'],
            $r_data['message']
        );

        return $user['usuario'];
    }

    /**
     * Test GET /users/userId      403 Forbidden
     *
     * @param array $user user returned by testCGetUser403()
     *
     * @depends testCGetUser403
     * @throws \Exception
     */
    public function testGetUser403(array $user)
    {
        $response = $this->runApp(
            'GET',
            self::$ruta_base . '/' . ($user['id'] + 1),
            null,
            $this->getTokenHeaders()
        );
        self::assertSame(403, $response->getStatusCode());
        self::assertJson((string)$response->getBody());
        $r_data = json_decode($response->getBody(), true);
        self::assertSame(403, $r_data['code']);
        self::assertSame(
            Messages::MESSAGES['tdw_get_users_403'],
            $r_data['message']
        );
    }

    /**
     * Test POST /users      403 Forbidden
     *
     * @depends testCGetUser403
     * @throws \Exception
     */
    public function testPostUser403()
    {
        $response = $this->runApp(
            'POST',
            self::$ruta_base,
            [],
            $this->getTokenHeaders()
        );
        self::assertSame(403, $response->getStatusCode());
        self::assertJson((string)$response->getBody());
        $r_data = json_decode($response->getBody(), true);
        self::assertSame(403, $r_data['code']);
        self::assertSame(
            Messages::MESSAGES['tdw_post_users_403'],
            $r_data['message']
        );
    }

    /**
     * Test PUT /users/userId      403 Forbidden
     *
     * @param array $user user returned by testCGetUser403()
     *
     * @depends testCGetUser403
     * @throws \Exception
     */
    public function testPutUser403(array $user)
    {
        $response = $this->runApp(
            'PUT',
            self::$ruta_base . '/' . ($user['id'] + 1),
            null,
            $this->getTokenHeaders()
        );
        self::assertSame(403, $response->getStatusCode());
        self::assertJson((string)$response->getBody());
        $r_data = json_decode($response->getBody(), true);
        self::assertSame(403, $r_data['code']);
        self::assertSame(
            Messages::MESSAGES['tdw_put_users_403'],
            $r_data['message']
        );
    }


    /**
     * Test DELETE /users/userId      403 Forbidden
     *
     * @param array $user user returned by testCGetUser403()
     *
     * @depends testCGetUser403
     * @throws \Exception
     */
    public function testDeleteUser403(array $user)
    {
        $response = $this->runApp(
            'DELETE',
            self::$ruta_base . '/' . ($user['id'] + 1),
            null,
            $this->getTokenHeaders()
        );
        self::assertSame(403, $response->getStatusCode());
        self::assertJson((string)$response->getBody());
        $r_data = json_decode($response->getBody(), true);
        self::assertSame(403, $r_data['code']);
        self::assertSame(
            Messages::MESSAGES['tdw_delete_users_403'],
            $r_data['message']
        );
    }
}
