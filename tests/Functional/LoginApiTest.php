<?php
/**
 * PHP version 7.2
 * tests\Functional\LoginApiTest.php
 */

namespace TDW18\Usuarios\Tests\Functional;

use TDW18\Usuarios\Messages;

/**
 * Class LoginApiTest
 *
 * @package TDW18\Usuarios\Tests\Functional
 */
class LoginApiTest extends BaseTestCase
{
    use \TDW18\Usuarios\Utils;

    private static $ruta_base;

    /**
     * Se ejecuta una vez al inicio de las pruebas de la clase UserApiTest
     */
    public static function setUpBeforeClass()
    {
        self::$ruta_base = $_ENV['RUTA_LOGIN'];

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
     * Test POST /login 404 NOT FOUND
     * @param array $data
     * @param int $estado
     * @param string $mensaje
     * @dataProvider proveedorUsusarios()
     */
    public function testPostLogin404(array $data, int $estado, string $mensaje)
    {
        $response = $this->runApp('POST', self::$ruta_base, $data);

        self::assertSame($estado, $response->getStatusCode());
        self::assertJson((string) $response->getBody());
        $r_body = (string) $response->getBody();
        self::assertContains('code', $r_body);
        self::assertContains('message', $r_body);
        $r_data = json_decode($r_body, true);
        self::assertSame($estado, $r_data['code']);
        self::assertSame(
            $mensaje,
            $r_data['message']
        );
    }

    /**
     * Test POST /login 200 OK
     */
    public function testPostLogin200()
    {
        $data = [
            'username' => $_ENV['ADMIN_USER_NAME'],
            'password' => $_ENV['ADMIN_USER_PASSWD']
        ];
        $response = $this->runApp('POST', self::$ruta_base, $data);

        self::assertSame(200, $response->getStatusCode());
        self::assertJson((string) $response->getBody());
        $r_body = (string) $response->getBody();
        self::assertTrue($response->hasHeader('X-Token'));
        self::assertContains('X-Token', $r_body);
        $r_data = json_decode($r_body, true);
        self::assertNotEmpty($r_data['X-Token']);
    }

    public function proveedorUsusarios()
    {
        try {
            return [
                // [datos], estado, mensaje
                'empty_user' => [[], 404, Messages::MESSAGES['tdw_post_login_404']],
                'no_password' => [[
                    'username' => $_ENV['ADMIN_USER_NAME']
                ], 404, Messages::MESSAGES['tdw_post_login_404']],
                'no_username' => [[
                    'password' => $_ENV['ADMIN_USER_PASSWD']
                ], 404, Messages::MESSAGES['tdw_post_login_404']],
                'incorrect_username' => [[
                    'username' => 'User * ' . random_int(0, 1000),
                    'password' => $_ENV['ADMIN_USER_PASSWD']
                ], 404, Messages::MESSAGES['tdw_post_login_404']],
                'incorrect_passwd' => [[
                    'username' => $_ENV['ADMIN_USER_NAME'],
                    'password' => 'Passwd * ' . random_int(0, 1000),
                ], 404, Messages::MESSAGES['tdw_post_login_404']],
            ];
        } catch (\Exception $e) {
            die('ERROR: ' . $e->getMessage());
        }
    }
}
