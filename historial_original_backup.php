<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit();
}

// Configuración de la base de datos
$servername = "db";
$username_db = "apti";
$password_db = "apti";
$dbname = "AdministracionProyectosTecnologiasInformacion";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// Obtener ID del usuario
$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT id FROM usuarios WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user_row = $result->fetch_assoc();
$usuario_id = $user_row['id'];
$stmt->close();

// Obtener historial de cotizaciones del usuario
$stmt = $conn->prepare("SELECT id, nombre_cotizacion, total_costo, fecha_creacion, datos_cotizacion FROM historial_cotizaciones WHERE usuario_id = ? ORDER BY fecha_creacion DESC");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$cotizaciones = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Cotizaciones</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .contenedor {
            max-width: 1000px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        .botones-superior {
            margin-bottom: 20px;
        }
        .botones-superior a, .botones-superior button {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
            font-size: 14px;
        }
        .botones-superior a:hover, .botones-superior button:hover {
            background-color: #0056b3;
        }
        .logout-btn {
            background-color: #ffffff !important;
            float: right;
        }
        .logout-btn:hover {
            background-color: #ffffff !important;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th {
            background-color: #007bff;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        table tr:hover {
            background-color: #f9f9f9;
        }
        .acciones {
            text-align: center;
        }
        .btn-ver {
            background-color: #28a745;
            color: white;
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            border: none;
        }
        .btn-ver:hover {
            background-color: #218838;
        }
        .btn-eliminar {
            background-color: #dc3545;
            color: white;
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            border: none;
            margin-left: 5px;
        }
        .btn-eliminar:hover {
            background-color: #c82333;
        }
        .sin-datos {
            text-align: center;
            padding: 40px;
            color: #999;
            font-size: 16px;
        }
    </style>
</head>
<body>

<div class="contenedor">
    <a href="logout.php" class="logout-btn">Cerrar sesión</a>
    <h1>Historial de Cotizaciones</h1>
    <p>Usuario: <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></p>

    <div class="botones-superior">
        <a href="CotView.php">← Volver a Cotizador</a>
        <button onclick="limpiarHistorial()" style="background-color: #ffc107; color: #000;">Limpiar Historial</button>
    </div>

    <?php if ($cotizaciones->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Nombre Cotización</th>
                    <th>Total</th>
                    <th>Fecha</th>
                    <th class="acciones">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $cotizaciones->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nombre_cotizacion']); ?></td>
                        <td><strong>$<?php echo number_format($row['total_costo'], 2); ?></strong></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($row['fecha_creacion'])); ?></td>
                        <td class="acciones">
                            <a href="detail.php?id=<?php echo $row['id']; ?>" class="btn-ver" title="Ver Detalles"><i class="fas fa-eye"></i> Ver Detalles</a>
                            <form method="POST" action="eliminar_cotizacion.php" style="display: inline;" onsubmit="return confirm('¿Estás seguro que deseas eliminar esta cotización?');">
                                <input type="hidden" name="json_data" value='{"id":<?php echo $row['id']; ?>}'>
                                <button type="submit" class="btn-eliminar">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="sin-datos">
            <p>No hay cotizaciones guardadas aún.</p>
            <a href="CotView.php" style="color: #007bff; text-decoration: underline;">Crear una nueva cotización</a>
        </div>
    <?php endif; ?>

</div>

<script>
function limpiarHistorial() 
    if (confirm('¿Estás seguro que deseas eliminar TODO el historial? Esta acción no se puede deshacer.')) {
        fetch('limpiar_historial.php', {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Historial limpiado');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
}
</script>

</body>
</html>

<?php
$conn->close();
?>

