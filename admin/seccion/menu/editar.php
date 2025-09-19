<?php
include("../../templates/header.php");
include("../../bd.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? $_POST['id'] : "";
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : "";
    $ingredientes = isset($_POST['ingredientes']) ? $_POST['ingredientes'] : "";
    $precio = isset($_POST['precio']) ? $_POST['precio'] : "";
    $foto = isset($_POST['foto']) ? $_POST['foto'] : "";

    try {
        $stmt = $conn->prepare("UPDATE menu 
                               SET nombre = :nombre, 
                                   ingredientes = :ingredientes, 
                                   precio = :precio, 
                                   foto = :foto 
                               WHERE id = :id");
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':ingredientes', $ingredientes);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':foto', $foto);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        header("Location: index.php");
        exit;
    } catch (PDOException $e) {
        echo "Error al actualizar: " . $e->getMessage();
    }
}

$id = isset($_GET['txtID']) ? $_GET['txtID'] : '';
$nombre = $ingredientes = $foto = "";
$precio = "";

if ($id) {
    $select = $conn->prepare("SELECT * FROM menu WHERE id = :id");
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
    <main>
        <br>
        <div class="card">
            <div class="card-header">Editar Plato del Menú</div>
            <div class="card-body">
                <form action="" method="post">
                   
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">

                    <div class="mb-3">
                        <label class="form-label">Nombre del Plato:</label>
                        <input type="text" class="form-control" name="nombre"
                               value="<?php echo htmlspecialchars($nombre); ?>"
                               placeholder="Ejemplo: Pasta Alfredo" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ingredientes:</label>
                        <input type="text" class="form-control" name="ingredientes"
                               value="<?php echo htmlspecialchars($ingredientes); ?>"
                               placeholder="Ejemplo: Pasta, crema, queso parmesano" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Precio:</label>
                        <input type="number" class="form-control" name="precio"
                               value="<?php echo htmlspecialchars($precio); ?>"
                               placeholder="Ejemplo: 25000" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Foto (URL):</label>
                        <input type="text" class="form-control" name="foto"
                               value="<?php echo htmlspecialchars($foto); ?>"
                               placeholder="Ejemplo: https://midominio.com/imagenes/pasta.jpg" required>
                    </div>

                    <button type="submit" class="btn btn-success">Guardar Cambios</button>
                    <a class="btn btn-primary" href="index.php" role="button">Cancelar</a>
                </form>
            </div>
        </div>
    </main>

    <footer>
        <?php include("../../templates/footer.php"); ?>
    </footer>
</body>
</html>
