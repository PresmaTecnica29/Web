<?php




if (isset($_POST['user_email']) && isset($_POST['verfcode_input'])) {
    $user_email = $_POST['user_email'];
    $verifCode = $_POST['verfcode_input'];

    // Verificar los datos recibidos
    error_log("user_email: $user_email");
    error_log("verifCode: $verifCode");

    // Aquí iría la lógica para verificar el código
    echo "Código verificado correctamente.";
} else {
    // Verificar qué datos están presentes
    if (!isset($_POST['user_email'])) {
        echo("user_email no está en la solicitud.");
    }
    if (!isset($_POST['verfcode_input'])) {
        echo("verfcode_input no está en la solicitud.");
    }
}