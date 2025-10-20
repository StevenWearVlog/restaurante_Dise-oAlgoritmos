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
<html lang="en">
<head>
    <title>Menú</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body>
<header>
    <?php include("../../templates/header.php"); ?>
</header>
<main>
    <section class="container mt-4">
        <div class="card">
            <div class="card-header">
                <a class="btn btn-primary" href="crear.php" role="button">Agregar Platillo</a>
            </div>
            <div class="card-body">
                <div class="table-responsive-sm">
                    <table class="table align-middle text-center">
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
                                <td><?php echo $value["id"]; ?></td>
                                <td><?php echo $value["nombre"]; ?></td>
                                <td><?php echo $value["ingredientes"]; ?></td>
                                <td><?php echo $value["precio"]; ?></td>
                                <td>
                                    <?php if ($value['foto'] && file_exists(__DIR__ . "/../../../" . $value['foto'])): ?>
                                        <img src="/restaurant/<?php echo $value['foto']; ?>" alt="Foto del platillo" width="120" class="img-thumbnail">
                                    <?php else: ?>
                                        <span class="text-muted">Sin imagen</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a class="btn btn-info" href="editar.php?txtID=<?php echo $value["id"]; ?>">Editar</a>
                                    <a class="btn btn-danger" href="index.php?txtID=<?php echo $value["id"]; ?>">Borrar</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-muted"></div>
        </div>
    </section>
</main>
<footer>
    <?php include("../../templates/footer.php"); ?>
</footer>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
</body>
</html>
