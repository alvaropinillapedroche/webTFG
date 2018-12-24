<?php
/**
 * PHP version 7.2
 * src\Install.php
 */

namespace TDW18\PFinal;

require_once __DIR__ . '/../bootstrap.php';

use Composer\Script\Event;
use Dotenv\Dotenv;

/**
 * Class Install
 *
 * @package TDW18\PFinal
 */
class Install
{
    use Utils;

    public static function preInstall(Event $event)
    {
        // provides access to the current ComposerIOConsoleIO
        // stream for terminal input/output
        $io = $event->getIO();
        if (!$io->isInteractive()
            || $io->askConfirmation(
                'Este comando eliminará el contenido de las tablas. ¿Desea continuar? ',
                false
            )
        ) {
            // ok, continue on to composer install
            return true;
        }
        // exit composer and terminate installation process
        exit;
    }

    /**
     * PostInstall command
     *
     * @param Event $event event
     *
     * @return bool
     */
    public static function postInstall(Event $event): bool
    {
        // Load the environment/configuration variables
        $dotenv = new Dotenv(__DIR__ . '/..', self::getEnvFileName(__DIR__. '/..'));
        $dotenv->load();
        $dotenv->required(
            [
                'DATABASE_HOST',
                'DATABASE_NAME',
                'DATABASE_USER',
                'DATABASE_PASSWD',
                'DATABASE_DRIVER',
                'ADMIN_USER_NAME',
                'ADMIN_USER_EMAIL',
                'ADMIN_USER_PASSWD'
            ]
        );

        // Create/update tables in the database
        self::updateSchema();
        $event->getIO()->write('>> Database UPDATED');

        self::loadUserData(
            $_ENV['ADMIN_USER_NAME'],
            $_ENV['ADMIN_USER_EMAIL'],
            $_ENV['ADMIN_USER_PASSWD'],
            true
        );

        return true;
    }
}
