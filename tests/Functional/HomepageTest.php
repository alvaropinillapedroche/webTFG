<?php
/**
 * PHP version 7.2
 * tests/Functional/HomepageTest.php
 */

namespace TDW18\Usuarios\Tests\Functional;

use TDW18\Usuarios\Messages;

/**
 * Class HomepageTest
 *
 * @package TDW18\Usuarios\Tests\Functional
 */
class HomepageTest extends BaseTestCase
{
    /**
     * Test that the index route returns a 302 status code
     *
     * @return void
     */
    public function testGetHomepage()
    {
        $response = $this->runApp('GET', '/');

        self::assertSame(302, $response->getStatusCode());
    }

    /**
     * Test that the index route won't accept a post request
     *
     * @return void
     */
    public function testPostHomepageNotAllowed()
    {
        $response = $this->runApp('POST', '/', ['test']);

        self::assertSame(405, $response->getStatusCode());
        self::assertContains(
            Messages::MESSAGES['tdw_notallowed_405'],
            (string) $response->getBody()
        );
    }

    /**
     * Implements RouteNotFound (tests notFoundHandler)
     *
     * @return void
     */
    public function testRouteNotFound()
    {
        $response = $this->runApp('PUT', '/products/abc');

        self::assertSame(404, $response->getStatusCode());
        self::assertContains(
            Messages::MESSAGES['tdw_pathnotfound_404'],
            (string) $response->getBody()
        );
    }
}
