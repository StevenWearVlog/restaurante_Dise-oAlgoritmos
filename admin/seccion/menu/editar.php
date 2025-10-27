<?php
include("../../templates/header.php");
include("../../bd.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? "";
    $nombre = trim($_POST['nombre'] ?? "");
    $ingredientes = trim($_POST['ingredientes'] ?? "");
    $precio = $_POST['precio'] ?? "";

    if ($id && $nombre && $ingredientes && is_numeric($precio) && $precio > 0) {
        $carpetaServidor = __DIR__ . "/../../../uploads/menu/";
        $carpetaWeb = "uploads/menu/";

        if (!file_exists($carpetaServidor)) {
            mkdir($carpetaServidor, 0777, true);
        }

        $query = $pdo->prepare("SELECT foto FROM menu WHERE id = :id");
        $query->bindParam(':id', $id);
        $query->execute();
        $oldData = $query->fetch(PDO::FETCH_ASSOC);
        $oldImagePath = $oldData ? $oldData['foto'] : null;

        $rutaFoto = $oldImagePath;

        if (!empty($_FILES['foto']['tmp_name']) && $_FILES['foto']['error'] === 0) {
            $nombreArchivo = time() . "_" . basename($_FILES['foto']['name']);
            $rutaDestinoServidor = $carpetaServidor . $nombreArchivo;
            $rutaDestinoWeb = $carpetaWeb . $nombreArchivo;

            if (move_uploaded_file($_FILES["foto"]["tmp_name"], $rutaDestinoServidor)) {
                $rutaFoto = $rutaDestinoWeb;
                if ($oldImagePath && file_exists(__DIR__ . "/../../../" . $oldImagePath)) {
                    unlink(__DIR__ . "/../../../" . $oldImagePath);
                }
            }
        }

        $stmt = $pdo->prepare("UPDATE menu 
                               SET nombre = :nombre, 
                                   ingredientes = :ingredientes, 
                                   precio = :precio, 
                                   foto = :foto 
                               WHERE id = :id");
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':ingredientes', $ingredientes);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':foto', $rutaFoto);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        header("Location: index.php");
        exit;
    }
}

$id = $_GET['txtID'] ?? '';
$nombre = $ingredientes = $foto = "";
$precio = "";

if ($id) {
    $select = $pdo->prepare("SELECT * FROM menu WHERE id = :id");
    $select->bindParam(':id', $id);
    $select->execute();
    $row = $select->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $nombre = $row['nombre'];
        $ingredientes = $row['ingredientes'];
        $precio = $row['precio'];
        $foto = $row['foto'];
    }
}
?>

<!doctype html>
<html lang="es">
<head>
    <title>Editar Plato del Menú</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<main class="container mt-4">
    <div class="card">
        <div class="card-header">Editar Plato del Menú</div>
        <div class="card-body">
            <form action="" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">

                <div class="mb-3">
                    <label class="form-label">Nombre del Plato:</label>
                    <input type="text" class="form-control" name="nombre"
                           value="<?php echo htmlspecialchars($nombre); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ingredientes:</label>
                    <input type="text" class="form-control" name="ingredientes"
                           value="<?php echo htmlspecialchars($ingredientes); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Precio:</label>
                    <input type="number" class="form-control" name="precio"
                           value="<?php echo htmlspecialchars($precio); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Imagen actual:</label><br>
                    <?php if ($foto): ?>
                        <img src="<?php echo $foto; ?>" width="150" class="img-thumbnail mb-2">

                    <?php else: ?>
                        <p class="text-muted">Sin imagen</p>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label class="form-label">Cambiar Imagen:</label>
                    <input type="file" class="form-control" name="foto" accept="image/*">
                </div>

                <button type="submit" class="btn btn-success">Guardar Cambios</button>
                <a class="btn btn-primary" href="index.php">Cancelar</a>
            </form>
        </div>
    </div>
</main>

<footer>
    <?php include("../../templates/footer.php"); ?>
</footer>
</body>
</html>
