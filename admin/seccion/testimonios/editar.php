<?php
include("../../templates/header.php");
include("../../bd.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? $_POST['id'] : "";
    $opinion = isset($_POST['opinion']) ? $_POST['opinion'] : "";
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : "";

    try {
        $stmt = $conn->prepare("UPDATE testimonios 
                               SET opinion = :opinion, 
                                   nombre = :nombre 
                               WHERE id = :id");
        $stmt->bindParam(':opinion', $opinion);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        header("Location: index.php");
        exit;
    } catch (PDOException $e) {
        echo "Error al actualizar: " . $e->getMessage();
    }
}

$id = isset($_GET['txtID']) ? $_GET['txtID'] : '';
$opinion = $nombre = "";

if ($id) {
    $select = $pdo->prepare("SELECT * FROM testimonios WHERE id = :id");
    $select->bindParam(':id', $id);
    $select->execute();
    $row = $select->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $opinion = $row['opinion'];
        $nombre = $row['nombre'];
    }
}
?>

<!doctype html>
<html lang="es">
<head>
    <title>Editar Testimonio</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
    <main>
        <br>
        <div class="card">
            <div class="card-header">Editar Testimonio</div>
            <div class="card-body">
                <form action="" method="post">
                    <!-- id oculto -->
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">

                    <div class="mb-3">
                        <label class="form-label">Opinión:</label>
                        <input type="text" class="form-control" name="opinion"
                               value="<?php echo htmlspecialchars($opinion); ?>"
                               placeholder="Ejemplo: La comida es excelente, volveré sin duda." required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nombre del Cliente:</label>
                        <input type="text" class="form-control" name="nombre"
                               value="<?php echo htmlspecialchars($nombre); ?>"
                               placeholder="Ejemplo: Juan Pérez" required>
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
