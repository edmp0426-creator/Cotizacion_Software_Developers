<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
    exit();
}

$servername = "db";
$username_db = "apti";
$password_db = "apti";
$dbname = "AdministracionProyectosTecnologiasInformacion";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión']);
    exit();
}

$json_str = $_POST['json_data'] ?? file_get_contents("php://input");
$data = json_decode($json_str, true);

if (!isset($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
    exit();
}

$id = intval($data['id']);
$username = $_SESSION['username'];

// Obtener ID del usuario
$stmt = $conn->prepare("SELECT id FROM usuarios WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user_row = $result->fetch_assoc();
$usuario_id = $user_row['id'];
$stmt->close();

// Verificar que la cotización pertenezca al usuario y eliminarla
$stmt = $conn->prepare("DELETE FROM historial_cotizaciones WHERE id = ? AND usuario_id = ?");
$stmt->bind_param("ii", $id, $usuario_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Cotización eliminada']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al eliminar']);
}

$stmt->close();
$conn->close();
?>
