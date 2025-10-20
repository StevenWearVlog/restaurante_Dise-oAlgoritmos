<?php
include("../../templates/header.php");
include("../../bd.php");


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? $_POST['id'] : "";
    $usuario = isset($_POST['usuario']) ? $_POST['usuario'] : "";
    $password = isset($_POST['password']) ? $_POST['password'] : "";
    $correo = isset($_POST['correo']) ? $_POST['correo'] : "";

    try {
        $stmt = $pdo->prepare("UPDATE usuarios 
                               SET usuario = :usuario, 
                                   password = :password, 
                                   correo = :correo 
                               WHERE id = :id");
        $stmt->bindParam(':usuario', $usuario);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        header("Location: index.php");
        exit;
    } catch (PDOException $e) {
        echo "Error al actualizar: " . $e->getMessage();
    }
}

$id = isset($_GET['txtID']) ? $_GET['txtID'] : '';
$usuario = $password = $correo = "";

if ($id) {
    $select = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
    $select->bindParam(':id', $id);
    $select->execute();
    $row = $select->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $usuario = $row['usuario'];
        $password = $row['password'];
        $correo = $row['correo'];
    }
}
?>

<!doctype html>
<html lang="es">
<head>
    <title>Editar Usuario</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
    <main>
        <br>
        <div class="card">
            <div class="card-header">Editar Usuario</div>
            <div class="card-body">
                <form action="" method="post">
                    
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">

                    <div class="mb-3">
                        <label class="form-label">Usuario:</label>
                        <input type="text" class="form-control" name="usuario"
                               value="<?php echo htmlspecialchars($usuario); ?>"
                               placeholder="Ejemplo: juan123" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Contraseña:</label>
                        <input type="password" class="form-control" name="password"
                               value="<?php echo htmlspecialchars($password); ?>"
                               placeholder="Escribe una contraseña segura" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Correo:</label>
                        <input type="email" class="form-control" name="correo"
                               value="<?php echo htmlspecialchars($correo); ?>"
                               placeholder="Ejemplo: usuario@correo.com" required>
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
