<?php
require "../classes/enviar_gmails.php";
if (isset($_POST['user_email']) && isset($_POST['verfcode_input'])) {
    $user_email = $_POST['user_email'];
    $verifCode = $_POST['verfcode_input'];

    // Verificar los datos recibidos
    error_log("user_email: $user_email");

    if (verifyCode($user_email, $verifCode)) {
        // If the verification code is valid, redirect to index.php
        header('Location: index.php');
        exit(); // Always call exit after a header redirect
    } else {
        // If the verification code is invalid, redirect to registerobj.php
        header('Location: registerobj.php');
        exit(); // Always call exit after a header redirect
    }
} else {
    // Verificar qué datos están presentes
    if (!isset($_POST['user_email'])) {
        echo("user_email no está en la solicitud.");
    }
    if (!isset($_POST['verfcode_input'])) {
        echo("verfcode_input no está en la solicitud.");
    }
}