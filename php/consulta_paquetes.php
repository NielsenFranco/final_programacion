<?php
// Conexión a la base de datos
$host = "localhost";
$usuario = "root";
$password = "";
$base_datos = "agencia_viajes";

$conn = new mysqli($host, $usuario, $password, $base_datos);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Procesar filtro
$filtro_origen = $_GET['origen'] ?? '';
$filtro_destino = $_GET['destino'] ?? '';
$filtro_precio_min = $_GET['precio_min'] ?? '';
$filtro_precio_max = $_GET['precio_max'] ?? '';

$query = "SELECT p.id_paquete, v.origen, v.destino, h.nombre AS hotel, c.todo_incluido, 
                 t.compania, p.precio_total
          FROM paquete p
          JOIN viaje v ON p.id_viaje = v.id_viaje
          JOIN hotel h ON p.id_hotel = h.id_hotel
          JOIN comida c ON p.id_comida = c.id_comida
          JOIN transporte t ON p.id_transporte = t.id_transporte
          WHERE 1 = 1";

if (!empty($filtro_origen)) {
    $query .= " AND v.origen LIKE '%$filtro_origen%'";
}
if (!empty($filtro_destino)) {
    $query .= " AND v.destino LIKE '%$filtro_destino%'";
}
if (!empty($filtro_precio_min)) {
    $query .= " AND p.precio_total >= $filtro_precio_min";
}
if (!empty($filtro_precio_max)) {
    $query .= " AND p.precio_total <= $filtro_precio_max";
}

$paquetes = $conn->query($query);
?>
<link rel="stylesheet" href="../css/styles.css">
<title>Consulta de Paquetes de Viajes</title>
    <header>
        <h2>Agencia de Viajes</h2>
        <ul>
            <li><a href="../index.php">Inicio</a></li>
            <li><a href="abm_paquetes.php">ABM Paquetes</a></li>
            <li><a href="consulta_paquetes.php">Consulta de Paquetes</a></li>
        </ul>
    </header>
    <h1>Consulta de Paquetes</h1>

    <!-- Formulario de Filtros -->
    <form method="get" action="consulta_paquetes.php">
        <label for="origen">Origen:</label>
        <input type="text" name="origen" value="<?= htmlspecialchars($filtro_origen) ?>">
        
        <label for="destino">Destino:</label>
        <input type="text" name="destino" value="<?= htmlspecialchars($filtro_destino) ?>">
        
        <label for="precio_min">Precio Mínimo:</label>
        <input type="number" name="precio_min" value="<?= htmlspecialchars($filtro_precio_min) ?>">
        
        <label for="precio_max">Precio Máximo:</label>
        <input type="number" name="precio_max" value="<?= htmlspecialchars($filtro_precio_max) ?>">

        <button type="submit">Filtrar</button>
    </form>

    <!-- Tabla de Resultados -->
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
            </tr>
        </thead>
        <tbody>
            <?php if ($paquetes->num_rows > 0): ?>
                <?php while ($row = $paquetes->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id_paquete'] ?></td>
                        <td><?= $row['origen'] ?></td>
                        <td><?= $row['destino'] ?></td>
                        <td><?= $row['hotel'] ?></td>
                        <td><?= $row['todo_incluido'] ? 'Sí' : 'No' ?></td>
                        <td><?= $row['compania'] ?></td>
                        <td>$<?= $row['precio_total'] ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">No se encontraron resultados</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
