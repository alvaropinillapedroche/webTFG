<?php
/**
 * PHP version 7.2
 * src\Messages.php
 */

namespace TDW18\PFinal;

class Messages
{
    const MESSAGES = [
        'tdw_unauthorized_401'
        => 'UNAUTHORIZED: invalid X-Token header',
        'tdw_pathnotfound_404'
        => 'Path not found',
        'tdw_notallowed_405'
        => 'Method not allowed',

        // login
        'tdw_post_login_404_user'
        => 'El nombre de usuario no existe',
        'tdw_post_login_404_pass'
        => 'La contraseña es incorrecta',

        // users
        'tdw_cget_users_404'
        => 'User object not found',

        //cuestiones
        'tdw_get_cuestiones_404'
        => 'Resource not found',

        //soluciones
        'tdw_put_solucion_reiniciar_todo'
        => 'Aviso: se han eliminado los razonamientos y las propuestas de razonamiento de la solución',

        'tdw_put_solucion_reiniciar_razonamientos'
        => 'Aviso: se han eliminado los razonamientos de la solución',

        'tdw_put_solucion_reiniciar_propRazonamiento'
        => 'Aviso: se han eliminado las propuestas de razonamiento de la solución'
    ];
}
