<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario
    $user_name = isset($_POST['user_name']) ? $_POST['user_name'] : '';
    $user_email = isset($_POST['user_email']) ? $_POST['user_email'] : '';
    $user_password_new = isset($_POST['user_password_new']) ? $_POST['user_password_new'] : '';
    $user_password_repeat = isset($_POST['user_password_repeat']) ? $_POST['user_password_repeat'] : '';

    // Configuración de cURL para el archivo que recibe todos los datos
    $ch1 = curl_init();
    curl_setopt($ch1, CURLOPT_URL, "../register.php"); // URL del archivo que recibe todos los datos
    curl_setopt($ch1, CURLOPT_POST, 1); // Solicitud POST
    curl_setopt($ch1, CURLOPT_POSTFIELDS, http_build_query([
        'user_name' => $user_name,
        'user_email' => $user_email,
        'user_password_new' => $user_password_new,
        'user_password_repeat' => $user_password_repeat
    ])); // Convertir el array a una cadena de consulta
    curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true); // Devolver la respuesta como cadena

    // Configuración de cURL para el archivo que recibe solo uno de los datos
    $ch2 = curl_init();
    curl_setopt($ch2, CURLOPT_URL, "verificar_codigo.php"); // URL del archivo que recibe solo el correo electrónico
    curl_setopt($ch2, CURLOPT_POST, 1); // Solicitud POST
    curl_setopt($ch2, CURLOPT_POSTFIELDS, http_build_query([
        'user_email' => $user_email // Solo el correo electrónico
    ])); // Convertir el array a una cadena de consulta
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true); // Devolver la respuesta como cadena

    // Ejecutar las solicitudes
    $response2 = curl_exec($ch2);

    // Verificar si hubo errores en la solicitud cURL
    if (curl_errno($ch1)) {
        echo 'Error en cURL (register.php): ' . curl_error($ch1);
    } else {
        echo 'Respuesta de register.php: ' . $response1;
    }

    if (curl_errno($ch2)) {
        echo 'Error en cURL (verificar_codigo.php): ' . curl_error($ch2);
    } else {
        echo 'Respuesta de verificar_codigo.php: ' . $response2;
    }

    // Cerrar las sesiones cURL
    curl_close($ch1);
    curl_close($ch2);
    header("Location: ../register.php");
    exit();
}
?>