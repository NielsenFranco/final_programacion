<?php
session_start();
include("../includes/conexion.php");
conectar(); // Asegúrate de que esta función realice correctamente la conexión

// Procesar acciones (crear, modificar, eliminar)
if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['accion'])) {
    $id = $_POST['id'] ?? null;
    $origen = $_POST['origen'] ?? null;
    $destino = $_POST['destino'] ?? null;
    $id_hotel = $_POST['id_hotel'] ?? null;
    $id_comida = $_POST['id_comida'] ?? null;
    $id_transporte = $_POST['id_transporte'] ?? null;
    $precio_total = $_POST['precio_total'] ?? null;
    $accion = $_POST['accion'] ?? $_GET['accion'];

    if ($accion === 'crear') {
        // Insertar datos en las tablas relacionadas
        $query_viaje = "INSERT INTO viaje (origen, destino) VALUES ('$origen', '$destino')";
        if ($con->query($query_viaje)) {
            $id_viaje = $con->insert_id;
            $query_paquete = "INSERT INTO paquete (id_viaje, id_hotel, id_comida, id_transporte, precio_total) 
                              VALUES ('$id_viaje', '$id_hotel', '$id_comida', '$id_transporte', '$precio_total')";
            $con->query($query_paquete);
        }
    } elseif ($accion === 'modificar') {
        // Actualizar datos en las tablas relacionadas
        $id_viaje = $_POST['id_viaje'];
        $query_viaje = "UPDATE viaje SET origen = '$origen', destino = '$destino' WHERE id_viaje = $id_viaje";
        $con->query($query_viaje);

        $query_paquete = "UPDATE paquete SET id_hotel = '$id_hotel', id_comida = '$id_comida', 
                                          id_transporte = '$id_transporte', precio_total = '$precio_total' 
                          WHERE id_paquete = $id";
        $con->query($query_paquete);
    } elseif ($accion === 'eliminar') {
        $id = $_GET['id'];
        $query = "DELETE FROM paquete WHERE id_paquete = $id";
        $con->query($query);
    }

    header('Location: abm_paquetes.php');
    exit;
}

// Obtener lista de paquetes
$query = "SELECT p.id_paquete, v.id_viaje, v.origen, v.destino, h.nombre AS hotel, 
                 c.todo_incluido, t.compania, p.precio_total
          FROM paquete p
          JOIN viaje v ON p.id_viaje = v.id_viaje
          JOIN hotel h ON p.id_hotel = h.id_hotel
          JOIN comida c ON p.id_comida = c.id_comida
          JOIN transporte t ON p.id_transporte = t.id_transporte";

// Verificar si la consulta se ejecutó correctamente
$paquetes_result = $con->query($query);
if ($paquetes_result === false) {
    die('Error en la consulta SQL: ' . $con->error);
}
$paquetes = $paquetes_result; // Asignar el resultado de la consulta

// Obtener datos de formulario para edición
$id_editar = $_GET['id'] ?? null;
$paquete_editar = null;
if ($id_editar) {
    $query = "SELECT p.*, v.origen, v.destino 
              FROM paquete p
              JOIN viaje v ON p.id_viaje = v.id_viaje
              WHERE id_paquete = $id_editar";
    $result = $con->query($query);
    $paquete_editar = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../css/styles.css">
    <title>Gestión de Paquetes</title>
</head>
<body>
    <header>
        <h2>Agencia de Viajes</h2>
        <ul>
            <li><a href="../index.php">Inicio</a></li>
            <li><a href="consulta_paquetes.php">Consultar Paquetes</a></li>
        </ul>
    </header>
    <h1>Gestión de Paquetes</h1>

    <!-- Tabla de Paquetes -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Origen</th>
                <th>Destino</th>
                <th>Hotel</th>
                <th>Comida</th>
                <th>Transporte</th>
                <th>Precio Total</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $paquetes->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id_paquete'] ?></td>
                    <td><?= $row['origen'] ?></td>
                    <td><?= $row['destino'] ?></td>
                    <td><?= $row['hotel'] ?></td>
                    <td><?= $row['todo_incluido'] ? 'Sí' : 'No' ?></td>
                    <td><?= $row['compania'] ?></td>
                    <td>$<?= $row['precio_total'] ?></td>
                    <td>
                        <a href="abm_paquetes.php?id=<?= $row['id_paquete'] ?>" class="boton">Editar</a>
                        <a href="abm_paquetes.php?accion=eliminar&id=<?= $row['id_paquete'] ?>" 
                           class="boton rojo" onclick="return confirm('¿Eliminar este paquete?')">Eliminar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Formulario para Crear/Editar -->
    <form method="post" action="abm_paquetes.php" onsubmit="return validarFormulario()">
        <input type="hidden" name="id" value="<?= $paquete_editar['id_paquete'] ?? '' ?>">
        <input type="hidden" name="id_viaje" value="<?= $paquete_editar['id_viaje'] ?? '' ?>">
        <label for="origen">Origen:</label>
        <input type="text" name="origen" value="<?= $paquete_editar['origen'] ?? '' ?>" required>
        <label for="destino">Destino:</label>
        <input type="text" name="destino" value="<?= $paquete_editar['destino'] ?? '' ?>" required>
        <label for="id_hotel">ID Hotel:</label>
        <input type="number" name="id_hotel" value="<?= $paquete_editar['id_hotel'] ?? '' ?>" required>
        <label for="id_comida">ID Comida:</label>
        <input type="number" name="id_comida" value="<?= $paquete_editar['id_comida'] ?? '' ?>" required>
        <label for="id_transporte">ID Transporte:</label>
        <input type="number" name="id_transporte" value="<?= $paquete_editar['id_transporte'] ?? '' ?>" required>
        <label for="precio_total">Precio Total:</label>
        <input type="text" name="precio_total" value="<?= $paquete_editar['precio_total'] ?? '' ?>" required>
        <button type="submit" name="accion" value="<?= $id_editar ? 'modificar' : 'crear' ?>">
            <?= $id_editar ? 'Guardar Cambios' : 'Crear' ?>
        </button>
    </form>

    <script>
        // Validación del formulario
        function validarFormulario() {
            let origen = document.querySelector('input[name="origen"]').value;
            let destino = document.querySelector('input[name="destino"]').value;

            if (!origen || !destino) {
                alert("Por favor, complete los campos de origen y destino.");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
