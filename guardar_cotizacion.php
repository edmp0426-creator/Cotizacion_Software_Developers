<?php
session_start();

// Este archivo puede ser usado si se envían datos via AJAX
// Por ahora, la funcionalidad está en CotView.php con formulario tradicional

header('Content-Type: application/json');

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
    exit();
}

echo json_encode(['success' => false, 'message' => 'Use el formulario en CotView.php']);
?>
