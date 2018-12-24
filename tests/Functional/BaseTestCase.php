<?php   // tests/Functional/BaseTestCase.php

namespace TDW18\Usuarios\Tests\Functional;

use PHPUnit\Framework\TestCase;
use Slim\App;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * This is an example class that shows how you could set up a method that
 * runs the application. Note that it doesn't cover all use-cases and is
 * tuned to the specifics of this skeleton app, so if your needs are
 * different, you'll need to change it.
 *
 * @covers
 */
class BaseTestCase extends TestCase
{
    private static $headers;

    /**
     * Process the application given a request method and URI
     *
     * @param string $requestMethod the request method (e.g. GET, POST, etc.)
     * @param string $requestUri the request URI
     * @param array|null $requestData the request data
     * @param array|null $requestHeaders the request headers
     *
     * @return \Psr\Http\Message\ResponseInterface|Response
     */
    public function runApp(
        string $requestMethod,
        string $requestUri,
        array $requestData = null,
        array $requestHeaders = null
    ) {

        // Create a mock environment for testing with
        $environment = Environment::mock(
            [
                'REQUEST_METHOD'    => $requestMethod,
                'REQUEST_URI'       => $requestUri,
                'HTTP_X_TOKEN'      => $requestHeaders['X-Token'] ?? null,
            ]
        );

        // Set up a request object based on the environment
        $request = Request::createFromEnvironment($environment);

        // Add request data, if it exists
        if (null !== $requestData) {
            $request = $request->withParsedBody($requestData);
        }

        // Add request headers, if it exists
        if (null !== $requestHeaders) {
            foreach ($requestHeaders as $header_name => $value) {
                $request->withHeader($header_name, $value);
            }
        }

        // Set up a response object
        $response = new Response();

        // Use the application settings
        $settings = require __DIR__ . '/../../src/settings.php';

        // Instantiate the application
        /** @noinspection PhpVariableNamingConventionInspection */
        $app = new App($settings);

        // Set up dependencies
        require __DIR__ . '/../../src/dependencies.php';

        // Register middleware
        require __DIR__ . '/../../src/middleware.php';

        // Register routes
        require __DIR__ . '/../../src/routes.php';

        // Process the application
        try {
            $response = $app->process($request, $response);
        } catch (\Slim\Exception\NotFoundException $exception) {
            die('ERROR: ' . $exception->getMessage());
        } catch (\Slim\Exception\MethodNotAllowedException $exception) {
            die('ERROR: ' . $exception->getMessage());
        } catch (\Exception $exception) {
            die('ERROR: ' . $exception->getMessage());
        }

        // Return the response
        return $response;
    }

    /**
     * Obtiene el JWT directamente de la ruta correspondiente
     * Si recibe como parámetro un nombre de usuario, obtiene un nuevo token
     * Sino, si anteriormente existe el token, lo reenvía
     *
     * @param string $username user name
     * @param string $password user password
     *
     * @return array cabeceras con el token obtenido
     */
    protected function getTokenHeaders(
        string $username = null,
        string $password = null
    ): array {
        if (null === self::$headers || null !== $username) {
            $data = [
                'username' => $username ?? $_ENV['ADMIN_USER_NAME'],
                'password' => $password ?? $_ENV['ADMIN_USER_PASSWD']
            ];
            $response = $this->runApp('POST', $_ENV['RUTA_LOGIN'], $data);
            $token = $response->getHeaderLine('X-Token');
            self::$headers = ['X-Token' => $token];
        }

        return self::$headers;
    }
}
