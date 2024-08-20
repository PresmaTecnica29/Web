<?php
require "../classes/enviar_gmails.php";
require "../config/db.php";
$conexion = conexion();

if (isset($_POST['user_email']) && isset($_POST['registerinput_verfcode'])) {
    $user_email = $_POST['user_email'];
    $verifCode = $_POST['registerinput_verfcode'];

    // Verificar los datos recibidos
    error_log("user_email: $user_email");
    error_log("verifCode: $verifCode");

    // Aquí iría la lógica para verificar el código
    echo "Código verificado correctamente.";
} else {
    // Verificar qué datos están presentes
    if (!isset($_POST['user_email'])) {
        error_log("user_email no está en la solicitud.");
    }
    if (!isset($_POST['registerinput_verfcode'])) {
        error_log("registerinput_verfcode no está en la solicitud.");
    }
    echo "Faltan datos en la solicitud.";
}
?>