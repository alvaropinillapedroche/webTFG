<?php   /** tests/tests_bootstrap.php */

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

// Settings to make all errors more obvious during testing
//error_reporting(-1);
//date_default_timezone_set('UTC');

// Load the environment/configuration variables
// from '.env.tests' or '.env.tests.dist')
$dotenv = new Dotenv(
    __DIR__,
    \TDW18\Usuarios\Utils::getEnvFileName(__DIR__, '.env.tests')
);
$dotenv->overload();    // overwrite the db name
$dotenv->required(
    [
        'DATABASE_HOST',
        'DATABASE_NAME',
        'DATABASE_USER',
        'DATABASE_PASSWD',
        'DATABASE_DRIVER',
        'JWT_SECRET',
        'ADMIN_USER_NAME',
        'ADMIN_USER_PASSWD',
        'ADMIN_USER_EMAIL'
    ]
);

require_once __DIR__ . '/../bootstrap.php';

mt_srand();

// Create/update tables in the test database
TDW18\Usuarios\Utils::updateSchema();
