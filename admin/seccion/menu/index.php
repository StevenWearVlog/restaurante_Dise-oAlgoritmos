<?php
include("../../bd.php");

$sentencia = $pdo->prepare("SELECT * FROM menu");
$sentencia->execute();
$resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET["txtID"])) {
    $txtID = $_GET["txtID"];

    $select = $pdo->prepare("SELECT foto FROM menu WHERE id=:id");
    $select->bindParam(":id", $txtID);
    $select->execute();
    $row = $select->fetch(PDO::FETCH_ASSOC);

    if ($row && $row['foto'] && file_exists(__DIR__ . "/../../../" . $row['foto'])) {
        unlink(__DIR__ . "/../../../" . $row['foto']);
    }

    $borrar = $pdo->prepare("DELETE FROM menu WHERE id=:id");
    $borrar->bindParam(":id", $txtID);
    $borrar->execute();

    header("Location:index.php");
    exit;
}
?>
<!doctype html>
<html lang="es">
<head>
    <title>Menú</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<header>
    <?php include("../../templates/header.php"); ?>
</header>

<main class="container mt-4">
    <div class="card">
        <div class="card-header">
            <a class="btn btn-primary" href="crear.php">Agregar Platillo</a>
        </div>
        <div class="card-body">
            <div class="table-responsive-sm">
                <table class="table text-center align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Ingredientes</th>
                            <th>Precio</th>
                            <th>Foto</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($resultado as $value): ?>
                        <tr>
                            <td><?= htmlspecialchars($value["id"]) ?></td>
                            <td><?= htmlspecialchars($value["nombre"]) ?></td>
                            <td><?= htmlspecialchars($value["ingredientes"]) ?></td>
                            <td>$<?= number_format($value["precio"], 0, ',', '.') ?></td>
                            <td>
                                <?php if ($value['foto'] && file_exists(__DIR__ . "/../../../" . $value['foto'])): ?>
                                    <img src="<?php echo $value['foto']; ?>" width="120" class="img-thumbnail">
                                <?php else: ?>
                                    <span class="text-muted">Sin imagen</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="editar.php?txtID=<?= $value["id"] ?>" class="btn btn-info btn-sm">Editar</a>
                                <a href="index.php?txtID=<?= $value["id"] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar este platillo?')">Eliminar</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<footer>
    <?php include("../../templates/footer.php"); ?>
</footer>
</body>
</html>
