<?php

$host = "localhost";     // Servidor
$dbname = "restaurante";  // Nombre de la base de datos
$username = "root";      // Usuario de la BD
$password = "";          // Contraseña (vacía en XAMPP por defecto)

try {
    // Crear conexión PDO
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

    // Configurar errores para que lance excepciones
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $error) {
    echo "Error de conexión: " . $error->getMessage();
}



?>