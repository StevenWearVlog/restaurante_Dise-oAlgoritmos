<?php
include("../../templates/header.php");
include("../../bd.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? $_POST['id'] : "";
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : "";
    $correo = isset($_POST['correo']) ? $_POST['correo'] : "";
    $mensaje = isset($_POST['mensaje']) ? $_POST['mensaje'] : "";

    try {
        $stmt = $pdo->prepare("UPDATE mensaje 
                               SET nombre = :nombre, 
                                   correo = :correo, 
                                   mensaje = :mensaje 
                               WHERE id = :id");
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':mensaje', $mensaje);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        header("Location: index.php");
        exit;
    } catch (PDOException $e) {
        echo "Error al actualizar: " . $e->getMessage();
    }
}

$id = isset($_GET['txtID']) ? $_GET['txtID'] : '';
$nombre = $correo = $mensaje = "";

if ($id) {
    $select = $pdo->prepare("SELECT * FROM mensaje WHERE id = :id");
    $select->bindParam(':id', $id);
    $select->execute();
    $row = $select->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $nombre = $row['nombre'];
        $correo = $row['correo'];
        $mensaje = $row['mensaje'];
    }
}
?>

<!doctype html>
<html lang="es">
<head>
    <title>Editar Mensaje</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
   
</head>
<body>
    <main>
        <br>
        <div class="card">
            <div class="card-header">Editar Mensaje</div>
            <div class="card-body">
                <form action="" method="post">
                    <!-- id oculto -->
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">

                    <div class="mb-3">
                        <label class="form-label">Nombre:</label>
                        <input type="text" class="form-control" name="nombre"
                               value="<?php echo htmlspecialchars($nombre); ?>"
                               placeholder="Ejemplo: Juan Pérez" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Correo:</label>
                        <input type="email" class="form-control" name="correo"
                               value="<?php echo htmlspecialchars($correo); ?>"
                               placeholder="ejemplo@correo.com" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mensaje:</label>
                        <textarea class="form-control" name="mensaje" rows="4"
                                  placeholder="Escribe aquí tu mensaje..." required><?php echo htmlspecialchars($mensaje); ?></textarea>
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
