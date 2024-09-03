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
            background-color: #0d6efd;
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
            <button class="faq-question" style='color: #e4e4e4'>¿Un usuario puede sacar varios equipos?</button>
            <div class="faq-answer">
                <p>El prestamo de computadoras esta limitado a 1 por usuario, ningun usuario puede tener mas de un prestamo activo al mismo tiempo. Si se quiere usar otra computadora diferente de la ya retirada el usuario debera devolver la computadora que posee y pedir otro prestamo.</p>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question" style='color: #e4e4e4'>¿Se puede extender el lapso del préstamo ?</button>
            <div class="faq-answer">
                <p>Si, se pueden extender los lapsos de prestamo desde la pagina de registros</p>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question" style='color: #e4e4e4'>¿Como poner una computadora en mantenimiento?</button>
            <div class="faq-answer">
                <p>Las computadoras pueden presentar una falla en su sistema que imposibilite su uso o alguno de sus componentes puede dejar de funcionar. Una computadora en estas condiciones no puede ser usada hasta ser restaurada. </p>
                    
                <p>Para deshabilitar una computadora y ponerla en estado de Mantenimiento dirigase a la pagina "Recursos" y busque la computadora que desea deshabilitar. Una vez la encuentre, haga click en el boton "Poner en Mantenimiento" ubicado en el lado derecho de la pantalla.
                Tenga en cuenta que una computadora en Mantenimiento no puede ser prestada a ningun usuario. </p>
                    
                <p>Para habilitar nuevamente una computadora y dejarla lista para ser prestada tendra que buscar la misma computadora y clickear en el boton "Habilitar" que se encuentra a un costado del visto previamente, esto lo que hara sera cambiar el estado de la computadora seleccionada a "Libre".
                </p>
            </div>
        </div>
        <div class="faq-item">
            <button class="faq-question" style='color: #e4e4e4  '>¿Qué pasa si se vence el lapso del préstamo?</button>
            <div class="faq-answer">
                <p>Como dijo Maradona:     Eeeeeeeeeeeeeeeeeeeeeeehhhhhhhhh...</p>
            </div>
        </div>
        <div class="faq-item">
            <button class="faq-question" style='color: #e4e4e4  '>¿Pregunta 5?</button>
            <div class="faq-answer">
                <p>Respuesta a la pregunta 5.</p>
            </div>
        </div>
        <div class="faq-item">
            <button class="faq-question" style='color: #e4e4e4  '>¿Pregunta 6?</button>
            <div class="faq-answer">
                <p>Respuesta a la pregunta 6.</p>
            </div>
        </div>
        <div class="faq-item">
            <button class="faq-question" style='color: #e4e4e4  '>¿Pregunta 7?</button>
            <div class="faq-answer">
                <p>Respuesta a la pregunta 7.</p>
            </div>
        </div>
        <div class="faq-item">
            <button class="faq-question" style='color: #e4e4e4  '>¿Pregunta 8?</button>
            <div class="faq-answer">
                <p>Respuesta a la pregunta 8.</p>
                <img src="https://www.google.com/imgres?q=lo%20suponia%20meme&imgurl=https%3A%2F%2Fi.pinimg.com%2F236x%2F4f%2Ffb%2Faa%2F4ffbaaff551f4713c5c7708b5ad2b768.jpg&imgrefurl=https%3A%2F%2Fes.pinterest.com%2Falexopro777zzzzzz%2Flo-suponia%2F&docid=J1HrKXzTqz8alM&tbnid=nJhbYioFkwd_-M&vet=12ahUKEwiYl5SxkveHAxXnLEQIHbMDHkEQM3oECGQQAA..i&w=236&h=386&hcb=2&ved=2ahUKEwiYl5SxkveHAxXnLEQIHbMDHkEQM3oECGQQAA" alt="Ejemplo de imagen en línea">
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