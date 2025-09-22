<?php
include("../../templates/header.php");
include("../../bd.php");

$titulo = "";
$descripcion = "";
$enlace = "";
$id = "";


if (isset($_GET['txtID'])) {
    $id = $_GET['txtID'];

    try {
        $stmt = $conn->prepare("SELECT * FROM banner WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $banner = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($banner) {
            $titulo = $banner['titulo'];
            $descripcion = $banner['descripcion'];
            $enlace = $banner['link'];
        }
    } catch (PDOException $e) {
        echo "Error al cargar: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $enlace = $_POST['enlace'];

    try {
        $stmt = $conn->prepare("UPDATE banner SET titulo = :titulo, descripcion = :descripcion, link = :enlace WHERE id = :id");
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':enlace', $enlace);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        header("Location: index.php");
        exit;
    } catch (PDOException $e) {
        echo "Error al actualizar: " . $e->getMessage();
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <title>Editar Banner</title>
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
        crossorigin="anonymous" />
</head>

<body>
    <main>
        <br>
        <div class="card">
            <div class="card-header">Editar Banner</div>
            <div class="card-body">

                <form action="" method="post">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">

                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título:</label>
                        <input type="text" class="form-control" name="titulo"
                               value="<?php echo htmlspecialchars($titulo); ?>"
                               id="titulo" placeholder="Escribe el título del banner" required>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción:</label>
                        <input type="text" class="form-control" name="descripcion"
                               value="<?php echo htmlspecialchars($descripcion); ?>"
                               id="descripcion" placeholder="Escribe la descripción" required>
                    </div>

                    <div class="mb-3">
                        <label for="enlace" class="form-label">Enlace:</label>
                        <input type="text" class="form-control" name="enlace"
                               value="<?php echo htmlspecialchars($enlace); ?>"
                               id="enlace" placeholder="Escribe el enlace" required>
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
