<?php

namespace blocks\services;
/**
 * Verifica si el usuario es profesor en algún curso.
     *
     * @param int $userid ID del usuario.
     * 
     * @return bool Verdadero si el usuario es profesor en algún curso, falso en caso contrario.
 */
function is_user_student_any_course(): bool
{
    global $DB, $USER;

    static $resultado;

    if ($resultado !== null) {

        return ($resultado) ? true : false;
    }

    // Consulta SQL para verificar si el usuario tiene un rol de profesor en algún curso
    $query = "
            SELECT 1
            FROM {role_assignments} ra
            WHERE ra.userid = :userid
            AND (ra.roleid = (SELECT id FROM mdl_role where shortname = 'student'))
        ";

    // Ejecuta la consulta y devuelve true si existe al menos un registro que cumpla con las condiciones

    $resultado = $DB->record_exists_sql(sql: $query, params: ['userid' => $USER->id]);

    return ($resultado) ? true : false;
}
