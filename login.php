<?php
session_start();

// Configuración de la base de datos
// --------- PENDIENTE ---------- 08/06/2024

// Verificar si hay una sesión iniciada 
// --------- CAMBIOS REALIZADOS ---------- 08/06/2024
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('Location: CotView.php');
    exit();
}

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // -------- CAMBIAR LAS CREDENCIALES Y USAR LAS DE LA BASE DE DATOS -------
    if ($username === 'admin' && $password === 'password') {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        header('Location: dashboard.php');
        exit();
    } else {
        $error = 'Invalid username or password.';
    }
}

// Visualización del formulario de inicio de sesión
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="login-container">
        <div class="form-box" id="login-form">
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <img src = "imagenes/logo.png" alt="Logo" class="logo">
                <h2>Login</h2>
                <?php if (isset($error)) { echo '<p class="error">' . $error . '</p>'; } ?>
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit">Login</button>
        </div>