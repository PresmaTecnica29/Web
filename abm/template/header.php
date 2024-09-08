<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
  <script src="script.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Función para verificar si hay checkboxes seleccionados y habilitar/deshabilitar botones
      function checkSelectedDevolucion() {
        var checkboxes = document.querySelectorAll('input[name="notificationDevolucion[]"]:checked');
        var acceptButton = document.getElementById('acceptDevolucion');
        var denyButton = document.getElementById('denyDevolucion');

        if (checkboxes.length > 0) {
          acceptButton.disabled = false;
          denyButton.disabled = false;
        } else {
          acceptButton.disabled = true;
          denyButton.disabled = true;
        }
      }

      // Añadimos el evento a cada checkbox individualmente
      document.querySelectorAll('input[name="notificationDevolucion[]"]').forEach(function(checkbox) {
        checkbox.addEventListener('change', checkSelectedDevolucion);
      });

      // Revisa si hay checkboxes seleccionados inicialmente
      checkSelectedDevolucion();

      // Función para marcar o desmarcar todas las casillas
      function toggleCheckboxesDevolucion(checked) {
        var checkboxes = document.querySelectorAll('.checkboxDevolucion');
        checkboxes.forEach(function(checkbox) {
          checkbox.checked = checked;
        });

        // Llamamos a checkSelected para habilitar/deshabilitar los botones de aceptar y rechazar
        checkSelectedDevolucion();
      }

      // Hacemos que la función toggleCheckboxesDevolucion esté disponible globalmente
      window.toggleCheckboxesDevolucion = toggleCheckboxesDevolucion;
    });
  </script>


  <script>
    document.addEventListener('DOMContentLoaded', function() {
      function checkSelected() {
        var checkboxes = document.querySelectorAll('input[name="notifications[]"]:checked');
        var acceptButton = document.getElementById('acceptReturn');
        var denyButton = document.getElementById('denyReturn');

        if (checkboxes.length > 0) {
          acceptButton.disabled = false;
          denyButton.disabled = false;
        } else {
          acceptButton.disabled = true;
          denyButton.disabled = true;
        }
      }

      // Añadimos el evento a cada checkbox individualmente
      document.querySelectorAll('input[name="notifications[]"]').forEach(function(checkbox) {
        checkbox.addEventListener('change', checkSelected);
      });

      // Revisa si hay checkboxes seleccionados inicialmente
      checkSelected();

      // Función para marcar o desmarcar todas las casillas
      function toggleCheckboxes(checked) {
        var checkboxes = document.querySelectorAll('.checkboxNotification');
        checkboxes.forEach(function(checkbox) {
          checkbox.checked = checked;
        });

        // Llamamos a checkSelected para habilitar/deshabilitar los botones de aceptar y rechazar
        checkSelected();
      }

      // Hacemos que la función toggleCheckboxes esté disponible globalmente
      window.toggleCheckboxes = toggleCheckboxes;
    });
  </script>

  <script>
    $(document).ready(function() {

      <?php if (!empty($notification)) { ?>
        $('#returnNotificationModal').modal('show');
      <?php } ?>

      <?php if (!empty($notificacionDevolucion)) { ?>
        $('#returnDevolucionModal').modal('show');
      <?php } ?>


      $('#acceptReturn').click(function() {
        if ($('#horario').val() == null || $('#horario').val() == '') {
          alert('Por favor, elija un horario.');
        } else {
          handleReturn('accepted');
        }
      });

      $('#denyReturn').click(function() {
        handleReturn('denied');
      });
      $('#acceptDevolucion').click(function() {
        handleDevolucion('accepted');
      });

      $('#denyDevolucion').click(function() {
        handleDevolucion('denied');
      });

      let isModalOpen = false;
      let notificationId;
      let notificacionIddev;

      $('#returnNotificationModal').on('shown.bs.modal', function() {
        isModalOpen = true;
      });

      $('#returnNotificationModal').on('hidden.bs.modal', function() {
        isModalOpen = false;
      });

      $('#returnDevolucionModal').on('shown.bs.modal', function() {
        if (!isModalOpen) {
          isModalOpen = true;
        } else {
          $('#returnDevolucionModal').modal('hide');
        }
      });

      $('#returnDevolucionModal').on('hidden.bs.modal', function() {
        isModalOpen = false;
      });

      function handleReturn(status) {
        var selectedIds = Array.from(document.querySelectorAll('input[name="notifications[]"]:checked'))
          .map(checkbox => checkbox.value); // Recoge los IDs de las notificaciones seleccionadas

        $.ajax({
          url: 'handle_return.php',
          type: 'POST',
          data: {
            status: status,
            id: selectedIds, // Enviar los IDs como un array
            hora: $('#horario').val(),
            nombreNet: $('#nombreNet').val()
          },
          success: function(response) {
            const result = JSON.parse(response);
            if (result.success.length > 0) {
              alert(result.success.join("\n"));
            }
            if (result.errors.length > 0) {
              alert(result.errors.join("\n"));
            }
            $('#notificationMessage').text(result.success.join("\n") || result.errors.join("\n"));
            $('#acceptReturn, #denyReturn').hide();
          },
          error: function(error) {
            alert('Hubo un error al manejar la devolución. Por favor, inténtalo de nuevo.');
          }
        });
      }


      function handleDevolucion(status) {
        // Obtener todos los checkboxes seleccionados
        var selectedIds = Array.from(document.querySelectorAll('input[name="notificationDevolucion[]"]:checked'))
          .map(checkbox => checkbox.value); // Recoge los IDs de las notificaciones seleccionadas

        // Obtener el nombre del recurso de devolución
        var nombreNetDevo = $('#nombreNetDevo').val();

        $.ajax({
          url: 'handle_devolucion.php',
          type: 'POST',
          data: {
            status: status,
            ids: selectedIds, // Envía todos los IDs seleccionados
            nombreNetDevo: nombreNetDevo
          },
          success: function(response) {
            var result = JSON.parse(response);
            var successMessages = result.success.join("\n");
            var errorMessages = result.errors.join("\n");

            // Mostrar mensajes de éxito y error
            if (successMessages) {
              $('#devolucionMessage').text(successMessages);
            }
            if (errorMessages) {
              alert(errorMessages);
            }

            $('#acceptDevolucion, #denyDevolucion').hide();
          },
          error: function() {
            alert('Hubo un error al manejar la devolución. Por favor, inténtalo de nuevo.');
          }
        });
      }



      const source = new EventSource('actualizar.php');

      source.onmessage = function(event) {
        const alumnos = JSON.parse(event.data);
        let html = '';
        alumnos.forEach(alumno => {
          html += generarFilaDeTabla(alumno);
        });
        document.getElementById('cuerpoDeTabla').innerHTML = html;
      };

      function generarFilaDeTabla(alumno) {
        return `
  <tr>
    <td>${alumno.idregistro}</td>
    <td>${alumno.user_name}</td>
    <td>${alumno.inicio_prestamo}</td>
    <td>${alumno.fin_prestamo}</td>
    <td>${alumno.fechas_extendidas}</td>
    <td>${alumno.recurso_nombre}</td>
  </tr>
`;
      }

      let sourceModal = new EventSource('actualizarModal.php');

      sourceModal.onmessage = function(event) {
        const notificacion = JSON.parse(event.data);

        // Comprobar si el modal está abierto
        if (!isModalOpen && notificacion) {
          // Actualizar el contenido del modal
          document.getElementById('notificationMessageUser').textContent = notificacion.user_name;
          document.getElementById('notificationMessageResource').textContent = notificacion.recurso_nombre;
          document.getElementById('notificationMessageStart').textContent = notificacion.inicio_prestamo;

          notificationId = notificacion.idregistro; // Agrega esta línea para almacenar el id de la notificación

          $('#acceptReturn, #denyReturn').show();

          // Abrir el modal
          $('#returnNotificationModal').modal('show');
        }
      };
      let sourceDevolucion = new EventSource('actualizarDevolucion.php');

      sourceDevolucion.onmessage = function(event) {
        const notificacionDevolucion = JSON.parse(event.data);

        // Comprobar si el modal está abierto
        if (!isModalOpen && notificacionDevolucion) {
          // Actualizar el contenido del modal
          document.getElementById('devolucionMessageUser').textContent = notificacionDevolucion.user_name;
          document.getElementById('devolucionMessageResource').textContent = notificacionDevolucion.recurso_nombre;
          document.getElementById('devolucionMessageStart').textContent = notificacionDevolucion.inicio_prestamo;
          document.getElementById('devolucionMessageEnd').textContent = notificacionDevolucion.horario;

          notificationIddev = notificacionDevolucion.idregistro; // Agrega esta línea para almacenar el id de la notificación
          notificacionNom =
            $('#acceptDevolucion, #denyDevolucion').show();

          // Abrir el modal
          $('#returnDevolucionModal').modal('show');
        }
      };
    });
  </script>


  <style>
    .img>img {
      margin-right: 5px;
    }

    #nombreNet {
      display: none;
    }

    footer {
      width: 100%;
      position: fixed;
      bottom: 0;
    }
  </style>
  <link rel="stylesheet" href="../../views/templates/stylelog.css">
  <link rel="icon" href="../template/logofinal.png" type="image/png">
  <title>Presma</title>
</head>

<body>
  <header class="p-3 bg-dark text-white">
    <div class="container" bis_skin_checked="1">
      <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start"
        bis_skin_checked="1">
        <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
          <li><a href='../../index.php' class="nav-link px-2 text-secondary">Inicio</a></li>
          <li><a href='../netbook/abm.php' class="nav-link px-2 text-white">Prestamos</a></li>
          <?php
          if (isset($_SESSION['user_rol'])) {
            if ($_SESSION['user_rol'] == 5) {
              echo '<li><a href="../abmPersonas/abmPersonas.php" class="nav-link px-2 text-white">Usuarios</a></li>';
              echo '<li><a href="../netbook/qr.php" class="nav-link px-2 text-white">Recursos</a></li>';
            }
          } ?>
          <li><a href="../netbook/visual.php" class="nav-link px-2 text-white">Visual</a></li>

          <li><a href="/Web/index.php?logout" class="nav-link px-2 text-white">Cerrar sesion</a></li>

          <li><a href="../abmPersonas/FAQ.php" class="nav-link px-2 text-white">FAQ</a></li>

        </ul>

        <div class="contenedor" bis_skin_checked="1">
          <div class="caja-advertencia"><?php echo $_SESSION['user_name']; ?></div>
          <img class="ñiquito" src="../../views/templates/logofinal.png">
        </div>
      </div>
    </div>
  </header>