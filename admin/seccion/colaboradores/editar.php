<?php
include("../../templates/header.php");
include("../../bd.php");

// 1) Si se envió el formulario => procesar UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? $_POST['id'] : "";
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : "";
    $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : "";
    $linkfacebook = isset($_POST['linkfacebook']) ? $_POST['linkfacebook'] : "";
    $linkinstagram = isset($_POST['linkinstagram']) ? $_POST['linkinstagram'] : "";
    $linkyoutube = isset($_POST['linkyoutube']) ? $_POST['linkyoutube'] : "";
    $foto = isset($_POST['foto']) ? $_POST['foto'] : "";

    try {
        $stmt = $conn->prepare("UPDATE colaboradores 
                               SET nombre = :nombre, 
                                   descripcion = :descripcion, 
                                   linkfacebook = :linkfacebook, 
                                   linkinstagram = :linkinstagram, 
                                   linkyoutube = :linkyoutube, 
                                   foto = :foto 
                               WHERE id = :id");
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':linkfacebook', $linkfacebook);
        $stmt->bindParam(':linkinstagram', $linkinstagram);
        $stmt->bindParam(':linkyoutube', $linkyoutube);
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
$nombre = $descripcion = $linkfacebook = $linkinstagram = $linkyoutube = $foto = "";

if ($id) {
    $select = $conn->prepare("SELECT * FROM chef WHERE id = :id");
    $select->bindParam(':id', $id);
    $select->execute();
    $colaboradores = $select->fetch(PDO::FETCH_ASSOC);

    if ($colaboradores) {
        $nombre = $colaboradores['nombre'];
        $descripcion = $colaboradores['descripcion'];
        $linkfacebook = $colaboradores['linkfacebook'];
        $linkinstagram = $colaboradores['linkinstagram'];
        $linkyoutube = $colaboradores['linkyoutube'];
        $foto = $colaboradores['foto'];
    }
}
?>

<!doctype html>
<html lang="es">
<head>
    <title>Editar Chef</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
    <main>
        <br>
        <div class="card">
            <div class="card-header">Editar Chef</div>
            <div class="card-body">
                <form action="" method="post">
                    <!-- id oculto -->
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">

                    <div class="mb-3">
                        <label class="form-label">Nombre:</label>
                        <input type="text" class="form-control" name="nombre"
                               value="<?php echo htmlspecialchars($nombre); ?>"
                               placeholder="Ejemplo: Gordon Ramsay" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción:</label>
                        <input type="text" class="form-control" name="descripcion"
                               value="<?php echo htmlspecialchars($descripcion); ?>"
                               placeholder="Breve descripción del chef" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Facebook:</label>
                        <input type="text" class="form-control" name="linkfacebook"
                               value="<?php echo htmlspecialchars($linkfacebook); ?>"
                               placeholder="https://facebook.com/usuario (opcional)">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Instagram:</label>
                        <input type="text" class="form-control" name="linkinstagram"
                               value="<?php echo htmlspecialchars($linkinstagram); ?>"
                               placeholder="https://instagram.com/usuario (opcional)">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">YouTube:</label>
                        <input type="text" class="form-control" name="linkyoutube"
                               value="<?php echo htmlspecialchars($linkyoutube); ?>"
                               placeholder="https://youtube.com/usuario (opcional)">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Foto:</label>
                        <input type="text" class="form-control" name="foto"
                               value="<?php echo htmlspecialchars($foto); ?>"
                               placeholder="Nombre o ruta del archivo de la foto (ej. chef.jpg)">
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
