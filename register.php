<?php

/**
 * A simple, clean and secure PHP Login Script / MINIMAL VERSION
 *
 * Uses PHP SESSIONS, modern password-hashing and salting and gives the basic functions a proper login system needs.
 *
 * @author Panique
 * @link https://github.com/panique/php-login-minimal/
 * @license http://opensource.org/licenses/MIT MIT License
 */

// checking for minimum PHP version
if (version_compare(PHP_VERSION, '5.3.7', '<')) {
    exit("Sorry, Simple PHP Login does not run on a PHP version smaller than 5.3.7 !");
} else if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    // if you are using PHP 5.3 or PHP 5.4 you have to include the password_api_compatibility_library.php
    // (this library adds the PHP 5.5 password hashing functions to older versions of PHP)
    require_once("libraries/password_compatibility_library.php");
}

// include the configs / constants for the database connection
require_once("config/db.php");

// load the registration class
require_once("classes/Registration.php");

// create the registration object. when this object is created, it will do all registration stuff automatically
// so this single line handles the entire registration process.
$registration = new Registration();

// show the register view (with the registration form, and messages/errors)
include("views/register.php");


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario
    $user_name = isset($_POST['user_name']) ? $_POST['user_name'] : '';
    $user_email = isset($_POST['user_email']) ? $_POST['user_email'] : '';
    $user_password_new = isset($_POST['user_password_new']) ? $_POST['user_password_new'] : '';
    $user_password_repeat = isset($_POST['user_password_repeat']) ? $_POST['user_password_repeat'] : '';

       // Mostrar datos para depuración
       echo '<pre>';
       print_r($_POST);
       echo '</pre>';
   
    // Configuración de cURL para el archivo que recibe todos los datos
    $ch1 = curl_init();
    curl_setopt($ch1, CURLOPT_URL, "views/verificar_codigo.php"); // URL del archivo que recibe todos los datos
    curl_setopt($ch1, CURLOPT_POST, 1); // Solicitud POST
    curl_setopt($ch1, CURLOPT_POSTFIELDS, http_build_query([
        'user_name' => $user_name,
        'user_email' => $user_email,
        'user_password_new' => $user_password_new,
        'user_password_repeat' => $user_password_repeat
    ])); // Convertir el array a una cadena de consulta
    curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true); // Devolver la respuesta como cadena
    // Ejecutar las solicitudes
    $response1 = curl_exec($ch1);
    // Verificar si hubo errores en la solicitud cURL
    if (curl_errno($ch1)) {
        echo 'Error en cURL (register.php): ' . curl_error($ch1);
    } else {
        echo 'Respuesta de register.php: ' . $response1;
    }


    // Cerrar las sesiones cURL
    curl_close($ch1);
}
