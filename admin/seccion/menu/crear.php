<?php
include("../../bd.php");
include("../../templates/header.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST["nombre"]);
    $ingredientes = trim($_POST["ingredientes"]);
    $precio = $_POST["precio"];

    if (!is_numeric($precio) || $precio <= 0) {
        echo '<div class="alert alert-danger">Debes ingresar un precio válido.</div>';
    } else {
        $carpetaServidor = __DIR__ . "/../../../uploads/menu/";
        $carpetaWeb = "../../uploads/menu/"; // ← Ruta correcta para mostrar luego

        if (!file_exists($carpetaServidor)) {
            mkdir($carpetaServidor, 0777, true);
        }

        $nombreImagen = time() . "_" . basename($_FILES["foto"]["name"]);
        $rutaDestinoServidor = $carpetaServidor . $nombreImagen;
        $rutaDestinoWeb = $carpetaWeb . $nombreImagen;

        if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] === 0) {
            if (move_uploaded_file($_FILES["foto"]["tmp_name"], $rutaDestinoServidor)) {
                $sentencia = $pdo->prepare("
                    INSERT INTO menu (nombre, ingredientes, precio, foto) 
                    VALUES (:nombre, :ingredientes, :precio, :foto)
                ");

                $sentencia->bindParam(":nombre", $nombre);
                $sentencia->bindParam(":ingredientes", $ingredientes);
                $sentencia->bindParam(":precio", $precio);
                $sentencia->bindParam(":foto", $rutaDestinoWeb);
                $sentencia->execute();

                header("Location: index.php");
                exit;
            } else {
                echo '<div class="alert alert-danger">Error al subir la imagen.</div>';
            }
        } else {
            echo '<div class="alert alert-danger">No se recibió ninguna imagen válida.</div>';
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <title>Agregar Platillo</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>
<main class="container mt-4">
    <div class="card">
        <div class="card-header">Nuevo Platillo</div>
        <div class="card-body">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Nombre del Platillo:</label>
                    <input type="text" class="form-control" name="nombre" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ingredientes:</label>
                    <input type="text" class="form-control" name="ingredientes" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Precio:</label>
                    <input type="number" class="form-control" name="precio" min="1" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Foto:</label>
                    <input type="file" class="form-control" name="foto" accept="image/*" required>
                </div>

                <button type="submit" class="btn btn-success">Agregar Platillo</button>
                <a class="btn btn-secondary" href="index.php">Cancelar</a>
            </form>
        </div>
    </div>
</main>
</body>
</html>
