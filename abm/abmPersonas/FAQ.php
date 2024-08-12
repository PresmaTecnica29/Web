<?php
include '../funciones.php';
csrf();
if (isset($_POST['submit']) && !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
  die();
}
include "../template/header.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preguntas Frecuentes (FAQ)</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .faq-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .faq-item {
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }

        .faq-question {
            width: 100%;
            background-color: #f6f33e;
            color: #fff;
            padding: 15px;
            border: none;
            text-align: left;
            font-size: 18px;
            cursor: pointer;
            outline: none;
            transition: background-color 0.3s ease;
        }

        .faq-question:hover {
            background-color: #0056b3;
        }

        .faq-answer {
            display: none;
            padding: 15px;
            background-color: #f9f9f9;
        }
    </style>
</head>

<body>
    <div class="faq-container">
        <h1>Preguntas Frecuentes (FAQ)</h1>

        <div class="faq-item">
            <button class="faq-question" style='color: #464640'>¿Pregunta 1?</button>
            <div class="faq-answer">
                <p>Respuesta a la pregunta 1.</p>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question" style='color: #464640'>¿Pregunta 2?</button>
            <div class="faq-answer">
                <p>Respuesta a la pregunta 2.</p>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question" style='color: #464640'>¿Pregunta 3?</button>
            <div class="faq-answer">
                <p>Respuesta a la pregunta 3.</p>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const questions = document.querySelectorAll('.faq-question');

            questions.forEach(question => {
                question.addEventListener('click', () => {
                    const answer = question.nextElementSibling;

                    if (answer.style.display === 'block') {
                        answer.style.display = 'none';
                    } else {
                        answer.style.display = 'block';
                    }
                });
            });
        });
    </script>
</body>
</html>

<?php include "../template/footer.php"; ?>