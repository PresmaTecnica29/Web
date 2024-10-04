<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect data
    $user_name = $_POST['user_name'] ?? null;
    $user_email = $_POST['user_email'] ?? null;
    $user_password_new = $_POST['user_password_new'] ?? null;
    $user_password_repeat = $_POST['user_password_repeat'] ?? null;

    // Check for missing fields
    if (!$user_name || !$user_email || !$user_password_new || !$user_password_repeat) {
        die('Faltan datos requeridos.');
    }

    // Debugging: Print POST data
    echo '<pre>';
    print_r($_POST);
    echo '</pre>';

    // Set base URL for cURL requests
    $base_url = 'http://localhost/Web/'; // Adjust based on your actual setup

    // Initialize cURL for register.php
    $ch = curl_init($base_url . 'registerobj.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($_POST));

    $response_register = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if (curl_errno($ch)) {
        die('cURL Error: ' . curl_error($ch));
    }
    curl_close($ch);

    // Output the HTTP status code and response
    echo 'HTTP Status Code for register: ' . $http_code;

    // Initialize cURL for verificar_codigo.php
    $ch = curl_init($base_url . 'views/verificacion.php'); // Use forward slashes
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'user_email' => $user_email,
    ]));

    $response_verificar_codigo = curl_exec($ch);
    if (curl_errno($ch)) {
        die('cURL Error: ' . curl_error($ch));
    }
    curl_close($ch);

    // Output the response from verificar_codigo.php
    echo '<pre>Response from verificar_codigo: ' . htmlspecialchars($response_verificar_codigo) . '</pre>';

    // Redirect
    header('Location: verificacion.php');
    exit();
}
?>