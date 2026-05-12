<?php
session_start();

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

// Verificar si hay una sesión iniciada
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('Location: CotView.php');
    exit();
}

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Verificar credenciales en la base de datos
    $stmt = $conn->prepare("SELECT password FROM usuarios WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            header('Location: CotView.php');
            exit();
        } else {
            $error = 'Invalid username or password.';
        }
    } else {
        $error = 'Invalid username or password.';
    }

    $stmt->close();
}

$conn->close();

// Visualización del formulario de inicio de sesión
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Axotimate - Login</title>
    <link rel="icon" href="imagenes/logo.png" type="image/png">
    <style>
        :root {
            --bg: #f8fafc;
            --surface: #ffffff;
            --text: #0f172a;
            --text-muted: #475569;
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --accent: #14b8a6;
            --border: rgba(15, 23, 42, 0.08);
            --shadow: 0 24px 60px rgba(15, 23, 42, 0.1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: radial-gradient(circle at top, rgba(37, 99, 235, 0.16), transparent 35%), var(--bg);
            color: var(--text);
            padding: 100px 20px;
            text-align: center;
            min-height: 100vh;
        }

        .login-container {
            display: block;
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }

        .form-box {
            background: var(--surface);
            border-radius: 24px;
            padding: 40px 30px;
            width: 100%;
            max-width: 350px;
            margin: 0 auto;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
        }

        .logo {
            width: 230px;
            height: auto;
            display: block;
            margin: 0 auto -12px;
        }

        h2 {
            text-align: center;
            color: var(--primary);
            margin-bottom: 22px;
            font-size: 26px;
            font-weight: 600;
        }

        .error {
            background: rgba(220, 38, 38, 0.1);
            color: #dc2626;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid rgba(220, 38, 38, 0.2);
            font-size: 14px;
        }

        .input-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            color: var(--primary);
            font-weight: 500;
            font-size: 14px;
        }

        input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--border);
            border-radius: 12px;
            background: white;
            font-size: 16px;
            transition: all 0.3s ease;
            color: var(--text);
            font-family: inherit;
        }

        input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.4);
        }

        button:active {
            transform: translateY(0);
        }

        @media (max-width: 480px) {
            body { padding: 60px 15px; }
            .form-box { padding: 35px 25px; max-width: 320px; }
            h2 { font-size: 24px; }
            .logo { width: 185px; margin-bottom: -8px; }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="form-box" id="login-form">
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <img src="imagenes/logo.png?v=1" alt="Logo" class="logo">
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
            </form>
        </div>
    </div>
</body>
</html>
