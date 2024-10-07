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
    function selectAllTimesWithSelected() {
      // Obtener el valor seleccionado del menú desplegable
      const selectedHorario = document.getElementById('selectHorarioTodos').value;

      if (!selectedHorario) {
        alert('Por favor, selecciona un horario primero.');
        return;
      }

      // Seleccionar todos los elementos select de horario
      const selects = document.querySelectorAll('select[name^="horario"]');

      // Recorre cada select y selecciona el horario seleccionado en el menú desplegable
      selects.forEach(select => {
        for (let i = 0; i < select.options.length; i++) {
          if (select.options[i].value == selectedHorario) {
            select.selectedIndex = i; // Selecciona la opción que coincide
            break;
          }
        }
      });
    }

    // Función para marcar o desmarcar todas las notificaciones
    function toggleCheckboxes(check) {
      const checkboxes = document.querySelectorAll('.checkboxNotification');
      checkboxes.forEach(checkbox => checkbox.checked = check);
    }
  </script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Seleccionar el checkbox principal que marcará o desmarcará todas las casillas
      var selectAllDevolucionCheckbox = document.getElementById('selectAllDevolucionCheckbox');
      var checkboxes = document.querySelectorAll('input[name="notificationDevolucion[]"]');
      var acceptButton = document.getElementById('acceptDevolucion');
      var denyButton = document.getElementById('denyDevolucion');

      // Función para verificar si hay casillas seleccionadas y habilitar/deshabilitar botones
      function checkSelected() {
        var selectedCheckboxes = document.querySelectorAll('input[name="notificationDevolucion[]"]:checked');

        // Si hay al menos una casilla marcada, habilitamos los botones
        if (selectedCheckboxes.length > 0) {
          acceptButton.disabled = false;
          denyButton.disabled = false;
        } else {
          // Si no hay ninguna casilla marcada, deshabilitamos los botones
          acceptButton.disabled = true;
          denyButton.disabled = true;
        }
      }

      // Evento para marcar/desmarcar todas las casillas cuando se usa el checkbox principal
      selectAllDevolucionCheckbox.addEventListener('change', function() {
        var isChecked = selectAllDevolucionCheckbox.checked;

        // Marcar o desmarcar todas las casillas
        checkboxes.forEach(function(checkbox) {
          checkbox.checked = isChecked;
        });

        // Llamar a la función para habilitar/deshabilitar los botones
        checkSelected();
      });

      // Evento para cada checkbox individual, para verificar si se deben habilitar/deshabilitar los botones
      checkboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
          // Llamar a la función para habilitar/deshabilitar los botones
          checkSelected();

          // Si todos los checkboxes están seleccionados, marcar el checkbox principal
          if (document.querySelectorAll('input[name="notificationDevolucion[]"]:checked').length === checkboxes.length) {
            selectAllDevolucionCheckbox.checked = true;
          } else {
            selectAllDevolucionCheckbox.checked = false;
          }
        });
      });

      // Llamar a la función inicialmente para asegurarse de que los botones estén en el estado correcto
      checkSelected();
    });
  </script>



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
    document.addEventListener('DOMContentLoaded', function() {
      // Seleccionar el checkbox principal que marcará o desmarcará todas las casillas
      var selectAllCheckbox = document.getElementById('selectAllCheckbox');
      var checkboxes = document.querySelectorAll('input[name="notifications[]"]');
      var acceptButton = document.getElementById('acceptReturn');
      var denyButton = document.getElementById('denyReturn');

      // Función para verificar si hay casillas seleccionadas y habilitar/deshabilitar botones
      function checkSelected() {
        var selectedCheckboxes = document.querySelectorAll('input[name="notifications[]"]:checked');

        // Si hay al menos una casilla marcada, habilitamos los botones
        if (selectedCheckboxes.length > 0) {
          acceptButton.disabled = false;
          denyButton.disabled = false;
        } else {
          // Si no hay ninguna casilla marcada, deshabilitamos los botones
          acceptButton.disabled = true;
          denyButton.disabled = true;
        }
      }

      // Evento para marcar/desmarcar todas las casillas cuando se usa el checkbox principal
      selectAllCheckbox.addEventListener('change', function() {
        var isChecked = selectAllCheckbox.checked;

        // Marcar o desmarcar todas las casillas
        checkboxes.forEach(function(checkbox) {
          checkbox.checked = isChecked;
        });

        // Llamar a la función para habilitar/deshabilitar los botones
        checkSelected();
      });

      // Evento para cada checkbox individual, para verificar si se deben habilitar/deshabilitar los botones
      checkboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
          // Llamar a la función para habilitar/deshabilitar los botones
          checkSelected();

          // Si todos los checkboxes están seleccionados, marcar el checkbox principal
          if (document.querySelectorAll('input[name="notifications[]"]:checked').length === checkboxes.length) {
            selectAllCheckbox.checked = true;
          } else {
            selectAllCheckbox.checked = false;
          }
        });
      });

      // Llamar a la función inicialmente para asegurarse de que los botones estén en el estado correcto
      checkSelected();
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

        var horarios = {};
        var fechasDevolucion = {};
        var nombreNet = {}; // Nuevo objeto para almacenar nombresNet específicos

        // Recolectar los horarios, fechas de devolución y nombresNet para cada notificación seleccionada
        selectedIds.forEach(function(id) {
          horarios[id] = $('select[name="horario[' + id + ']"]').val(); // Obtener el horario seleccionado
          fechasDevolucion[id] = $('input[name="fin_prestamo_fecha[' + id + ']"]').val(); // Obtener la fecha de devolución
          nombreNet[id] = $('select[name="nombreNet[' + id + ']"]').val(); // Obtener el valor específico de nombreNet
        });

        $.ajax({
          url: 'handle_return.php',
          type: 'POST',
          data: {
            status: status,
            id: selectedIds, // Enviar los IDs como un array
            horarios: horarios, // Enviar los horarios seleccionados
            fechasDevolucion: fechasDevolucion, // Enviar las fechas de devolución
            nombreNet: nombreNet
          },
          success: function(response) {
    console.log(response);
    $('#devolucionMessage').text(response);
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

        // Obtener el nombre del recurso de devolución para cada notificación seleccionada
        var nombreNetDevo = {};
        selectedIds.forEach(function(id) {
          nombreNetDevo[id]= $('select[name="nombreNetDevo[' + id + ']"]').val();
          
         
        });

        $.ajax({
          url: 'handle_devolucion.php',
          type: 'POST',
          data: {
            status: status,
            id: selectedIds, // Envía todos los IDs seleccionados
            nombreNetDevo: nombreNetDevo
          },
          success: function(response) {
    console.log(response);
    $('#devolucionMessage').text(response);
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

    footer {
      width: 100%;
      position: fixed;
      bottom: 0;
    }

   /* Estilo para el modal */
   .modal {
    display: none; 
    position: fixed; 
    z-index: 1; 
    left: 0;
    top: 0;
    width: 100%; 
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.4); 
  }
  .modal-content {
    background-color: #fff;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 300px;
    text-align: center;
    position: relative;
  }
  .close {
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
  }
  #modalMessage {
    color: #333;
    font-size: 16px;
    margin-bottom: 20px;
  }
  .modal-buttons {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
  }
  .modal-buttons button {
    padding: 10px 20px;
    border: none;
    cursor: pointer;
    font-size: 14px;
  }
  .modal-buttons button:first-child {
    background-color: #f44336;
    color: white;
  }
  .modal-buttons button:last-child {
    background-color: #ddd;
    color: black;
  }
</style>
  <link rel="stylesheet" href="../../views/templates/stylelog.css">
  <link rel="icon" href="../../template/logofinal.png" type="image/png">
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
            if ($_SESSION['user_rol'] == 5 || $_SESSION['user_rol'] == 4 || $_SESSION['user_rol'] == 3 || $_SESSION['user_rol'] == 2 || $_SESSION['user_rol'] == 1) {
              echo '<li><a href="../../abm/abmPersonas/abmPersonas.php" class="nav-link px-2 text-white">Usuarios</a></li>';
              echo '<li><a href="../../abm/netbook/qr.php" class="nav-link px-2 text-white">Recursos</a></li>';
            }
          } ?>
          <li><a href="../netbook/visual.php" class="nav-link px-2 text-white">Visual</a></li>

          <li><a href="../../abm/abmPersonas/FAQ.php" class="nav-link px-2 text-white">FAQ</a></li>

          <!-- Botón de Cerrar Sesión -->
          <li><button id="logoutButton" type="button" onclick="openLogoutModal()" style="border: none; background: none; color: white; padding: 0; cursor: pointer; margin-top: 8px; margin-left: 10px;">Cerrar Sesión</button></li>

        </ul>

        <div class="contenedor" bis_skin_checked="1">
        <a href="../abmPersonas/infouser.php" style="color: white; padding: 0; cursor: pointer; margin-right: 10px; text-decoration: none; font-size: 30px;">ⓘ</a>
          <div class="caja-advertencia"><?php echo $_SESSION['user_name']; ?></div>
          <img class="ñiquito" src="../../views/templates/logofinal.png"> 
        </div>
      </div>
    </div>

    <!-- Modal de Confirmación de Cierre de Sesión -->
  <div id="logoutModal" class="modal" style="display:none;">
    <div class="modal-content">
    <p id="modalMessage">¿Estás seguro de que deseas Cerrar Sesión?</p>
    <div class="modal-buttons">
      <button class="btn btn-danger" onclick="confirmLogout()">Sí, Cerrar</button>
      <button class="btn btn-secondary" onclick="closeLogoutModal()">Cancelar</button>
      </div>
    </div>
  </div>


  
    <script>
        function openLogoutModal() {
        document.getElementById("logoutModal").style.display = "block";
      }

      function closeLogoutModal() {
        document.getElementById("logoutModal").style.display = "none";
      }

      function confirmLogout() {
        // Cierra el modal
        closeLogoutModal();
        
        // Redirige a la página de cierre de sesión
        window.location.href = "../../index.php?logout"; 
      }

      // Cerrar el modal si se hace clic fuera del contenido del modal
      window.onclick = function(event) {
        const modal = document.getElementById("logoutModal");
        if (event.target === modal) {
          closeLogoutModal();
        }
      }
      </script>
  </header>