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

$conn->set_charset("utf8");

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
            float: right;
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
        .detalle-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.4);
        }
        .detalle-contenido {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            border-radius: 8px;
            width: 80%;
            max-height: 70vh;
            overflow-y: auto;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .cerrar-modal {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .cerrar-modal:hover {
            color: #000;
        }
        .modal-titulo {
            margin-top: 0;
            margin-bottom: 20px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        .detalles-tabla {
            width: 100%;
            border-collapse: collapse;
        }
        .detalles-tabla th, .detalles-tabla td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .detalles-tabla th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .total-final {
            text-align: right;
            margin-top: 20px;
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
        }
    </style>
</head>
<body>

<div class="contenedor">
    <a href="logout.php" class="logout-btn">Cerrar sesión</a>
    <h1>📊 Historial de Cotizaciones</h1>
    <p>Usuario: <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></p>

    <div class="botones-superior">
        <a href="CotView.php">← Volver a Cotizador</a>
        <button onclick="limpiarHistorial()" style="background-color: #ffc107; color: #000;">🗑️ Limpiar Historial</button>
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
                            <button class="btn-ver" onclick="verDetalles(<?php echo htmlspecialchars(json_encode($row)); ?>)">👁️ Ver Detalles</button>
                            <button class="btn-eliminar" onclick="eliminarCotizacion(<?php echo $row['id']; ?>)">🗑️ Eliminar</button>
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

<!-- Modal para ver detalles -->
<div id="detalleModal" class="detalle-modal">
    <div class="detalle-contenido">
        <span class="cerrar-modal" onclick="cerrarModal()">&times;</span>
        <h2 class="modal-titulo">Detalles de Cotización</h2>
        <div id="detalleContenido"></div>
    </div>
</div>

<script>
function verDetalles(cotizacion) {
    const datos = JSON.parse(cotizacion.datos_cotizacion);
    const modal = document.getElementById('detalleModal');
    const contenido = document.getElementById('detalleContenido');

    let html = `<p><strong>Nombre:</strong> ${cotizacion.nombre_cotizacion}</p>`;
    html += `<p><strong>Fecha:</strong> ${new Date(cotizacion.fecha_creacion).toLocaleString('es-ES')}</p>`;
    html += `<table class="detalles-tabla"><thead><tr><th>Posición</th><th>Cantidad</th><th>Costo Unitario</th><th>Subtotal</th></tr></thead><tbody>`;

    for (let key in datos) {
        if (datos[key].cantidad > 0) {
            const label = datos[key].label;
            const cantidad = datos[key].cantidad;
            const costo = datos[key].costo;
            const subtotal = cantidad * costo;
            html += `<tr><td>${label}</td><td>${cantidad}</td><td>$${costo.toLocaleString()}</td><td>$${subtotal.toLocaleString()}</td></tr>`;
        }
    }

    html += `</tbody></table>`;
    html += `<div class="total-final">Total: $${parseFloat(cotizacion.total_costo).toLocaleString('es-ES', {minimumFractionDigits: 2})}</div>`;

    contenido.innerHTML = html;
    modal.style.display = 'block';
}

function cerrarModal() {
    document.getElementById('detalleModal').style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('detalleModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}

function eliminarCotizacion(id) {
    if (confirm('¿Estás seguro que deseas eliminar esta cotización?')) {
        fetch('eliminar_cotizacion.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: id })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Cotización eliminada');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
}

function limpiarHistorial() {
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
