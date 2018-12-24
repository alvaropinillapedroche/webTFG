<?php
/**
 * PHP version 7.2
 * src\Utils.php
 */

namespace TDW18\PFinal;

require_once __DIR__ . '/../bootstrap.php';

use Doctrine\ORM\Tools\SchemaTool;
use Firebase\JWT\JWT;
use TDW18\PFinal\Entity\Usuario;

/**
 * Trait Utils
 *
 * @package TDW18\Usuarios
 */
trait Utils
{
    /**
     * Get .env filename (.env.docker || .env || .env.dist)
     *
     * @param string $dir      directory
     * @param string $filename filename
     *
     * @return string
     */
    public static function getEnvFileName(
        string $dir,
        string $filename = '.env'
    ): string {

        if (isset($_ENV['docker'])) {
            return $filename . '.docker';
        } elseif (file_exists($dir . '/' . $filename)) {
            return $filename;
        } else {
            return $filename . '.dist';
        }
    }

    public static function loadUserData(
        string $username,
        string $pass
    ) {
        $user = new Usuario($username, $pass);
        try {
            $e_manager = getEntityManager();
            $e_manager->persist($user);
            $e_manager->flush();
        } catch (\Doctrine\ORM\ORMException $e) {
            die('ERROR: ' . $e->getCode() . ' - ' . $e->getMessage());
        }
    }

    /**
     * Update database schema
     *
     * @return void
     */
    public static function updateSchema()
    {
        $e_manager = getEntityManager();
        $metadata = $e_manager->getMetadataFactory()->getAllMetadata();
        $sch_tool = new SchemaTool($e_manager);
        $sch_tool->dropDatabase();
        $sch_tool->updateSchema($metadata, true);
    }


    public static function getToken(
        int     $userId,
        string  $username,
        bool    $esMaestro
    ): string {

        $current_time = time();
        $token = [
            'iat'       => $current_time,
            'exp'       => $current_time + 3600,    // expires in 60 minutes
            'user_id'   => $userId,                 // user id.
            'username'  => $username,               // user name
            'esMaestro' => $esMaestro,              // is Maestro?
            // 'scope' => ['read', 'write', 'delete']
        ];

        return JWT::encode($token, $_ENV['JWT_SECRET'], 'HS512');
    }
}
