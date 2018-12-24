<?php
/**
 * PHP version 7.2
 * src\Entity\Usuario.php
 */

namespace TDW18\Usuarios\Tests\Entity;

use TDW18\Usuarios\Entity\Usuario;
use PHPUnit\Framework\TestCase;

/**
 * Class UserTest
 *
 * @package TDW18\Usuarios\Tests\Entity
 * @group   users
 * @coversDefaultClass \TDW18\Usuarios\Entity\Usuario
 */
class UsuarioTest extends TestCase
{
    /**
     * @var Usuario $user
     */
    protected $user;

    /**
     * Sets up the fixture.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->user = new Usuario();
    }

    /**
     * @covers ::__construct()
     */
    public function testConstructor()
    {
        self::assertSame(0, $this->user->getId());
        self::assertEmpty($this->user->getUsername());
        self::assertEmpty($this->user->getEmail());
        self::assertTrue($this->user->isEnabled());
        self::assertFalse($this->user->isMaestro());
        self::assertFalse($this->user->isAdmin());
    }

    /**
     * @covers ::getId()
     */
    public function testGetId()
    {
        self::assertSame(0, $this->user->getId());
    }

    /**
     * @covers ::setUsername()
     * @covers ::getUsername()
     * @throws \Exception
     */
    public function testGetSetUsername()
    {
        static::assertEmpty($this->user->getUsername());
        $username = 'UsEr TESt NaMe #' . random_int(0, 10000);
        $this->user->setUsername($username);
        static::assertSame($username, $this->user->getUsername());
    }

    /**
     * @covers ::getEmail()
     * @covers ::setEmail()
     * @throws \Exception
     */
    public function testGetSetEmail()
    {
        $userEmail = 'UsEr_' . random_int(0, 10000) . '@example.com';
        static::assertEmpty($this->user->getEmail());
        $this->user->setEmail($userEmail);
        static::assertSame($userEmail, $this->user->getEmail());
    }

    /**
     * @covers ::setEnabled()
     * @covers ::isEnabled()
     */
    public function testIsSetEnabled()
    {
        $this->user->setEnabled(true);
        self::assertTrue($this->user->isEnabled());

        $this->user->setEnabled(false);
        self::assertFalse($this->user->isEnabled());
    }

    /**
     * @covers ::setAdmin()
     * @covers ::isAdmin()
     */
    public function testIsSetAdmin()
    {
        $this->user->setAdmin(true);
        self::assertTrue($this->user->isAdmin());

        $this->user->setAdmin(false);
        self::assertFalse($this->user->isAdmin());
    }

    /**
     * @covers ::setMaestro()
     * @covers ::isMaestro()
     */
    public function testIsSetMaestro()
    {
        $this->user->setMaestro(true);
        self::assertTrue($this->user->isMaestro());

        $this->user->setMaestro(false);
        self::assertFalse($this->user->isMaestro());
    }

    /**
     * @covers ::getPassword()
     * @covers ::setPassword()
     * @covers ::validatePassword()
     * @throws \Exception
     */
    public function testGetSetPassword()
    {
        $password = 'UseR pa$?w0rD #' . random_int(0, 1000);
        $this->user->setPassword($password);
        self::assertTrue(password_verify($password, $this->user->getPassword()));
        self::assertTrue($this->user->validatePassword($password));
    }

    public function testGetCuestiones()
    {
        self::assertEmpty($this->user->getCuestiones());
    }

    /**
     * @covers ::__toString()
     * @throws \Exception
     */
    public function testToString()
    {
        $username = 'USer Te$t nAMe #' . random_int(0, 10000);
        $this->user->setUsername($username);
        self::assertContains($username, $this->user->__toString());
    }

    /**
     * @covers ::jsonSerialize()
     */
    public function testJsonSerialize()
    {
        $json = json_encode($this->user);
        self::assertJson($json);
    }
}
