<?php
session_start();

$_SESSION = array(); // Limpiar todas las variables de sesión

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy(); // Destruir la sesión

header('Location: login.php'); // Redirigir al login
exit();