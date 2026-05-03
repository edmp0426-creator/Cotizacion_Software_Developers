-- Crear tabla usuarios si no existe
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertar usuario de prueba (contraseña: password hasheada con bcrypt)
INSERT INTO usuarios (username, password, email) VALUES 
('admin', '$2y$10$UGAlTDGBqWH.CdCURIPjtuu5puQo5aSPzx9a0l.r7DNuOZWYXrre2', 'admin@example.com')
ON DUPLICATE KEY UPDATE id=id;

-- Crear tabla historial_cotizaciones
CREATE TABLE IF NOT EXISTS historial_cotizaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    nombre_cotizacion VARCHAR(100),
    datos_cotizacion LONGTEXT NOT NULL,
    total_costo DECIMAL(10, 2),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);
