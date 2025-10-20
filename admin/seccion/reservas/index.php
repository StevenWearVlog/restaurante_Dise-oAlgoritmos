<?php
include("../../bd.php");


$sentencia = $pdo->prepare("SELECT * FROM reservas ORDER BY fecha, hora, mesa");
$sentencia->execute();
$reservasList = $sentencia->fetchAll(PDO::FETCH_ASSOC);


if (isset($_GET["txtID"])) {
    $txtID = $_GET["txtID"];
    $borrar = $pdo->prepare("DELETE FROM reservas WHERE id=:id");
    $borrar->bindParam(":id", $txtID);
    $borrar->execute();
    header("Location:index.php");
    exit;
}


$totalMesas = 10; // numero de mesas
$columnas = 5; // columnas


$filas = ceil($totalMesas / $columnas);
$mesas = array_fill(0, $filas, array_fill(0, $columnas, 0));


foreach ($reservasList as $reserva) {
    if ($reserva['estado'] == 1) {
        $fila = floor(($reserva['mesa'] - 1) / $columnas);
        $col = ($reserva['mesa'] - 1) % $columnas;
        $mesas[$fila][$col] = 1;
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <title>Reservas</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/yourcode.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

</head>
<body>
<header>
    <?php include("../../templates/header.php"); ?>
   
</header>

<main>
    
    <section class="container mt-5">
        <div class="card mb-4">
            <div class="card-header">
                <a class="btn btn-primary" href="crear.php" role="button">Agregar Reserva</a>
            </div>
            <div class="card-body">
                <h2 class="text-center mb-4">Mapa de Mesas</h2>
                <?php foreach ($mesas as $filaIndex => $fila): ?>
                    <div class="row justify-content-center mb-2">
                        <?php foreach ($fila as $colIndex => $estado): ?>
                            <?php $numMesa = $filaIndex * $columnas + $colIndex + 1; ?>
                            <?php if ($numMesa <= $totalMesas): ?>
                                <div class="col-auto">
                                    <div class="card text-center"
                                         style="width: 100px; height: 100px;
                                                background-color: <?= $estado ? '#dc3545' : '#198754' ?>;
                                                color: white; display:flex; align-items:center; justify-content:center;">
                                        Mesa <?= $numMesa ?><br>
                                        <?= $estado ? 'Ocupada' : 'Libre' ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Tabla de reservas detallada -->
        <div class="card">
            <div class="card-header">Listado de Reservas</div>
            <div class="card-body">
                <div class="table-responsive-sm">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Mesa</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservasList as $reserva): ?>
                                <tr>
                                    <td><?= $reserva["id"] ?></td>
                                    <td><?= htmlspecialchars($reserva["cliente"]) ?></td>
                                    <td><?= $reserva["fecha"] ?></td>
                                    <td><?= $reserva["hora"] ?></td>
                                    <td><?= $reserva["mesa"] ?></td>
                                    <td>
                                        <?php if($reserva["estado"] == 1): ?>
                                            <span class="badge bg-danger">Ocupada</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Libre</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a class="btn btn-info btn-sm" href="editar.php?txtID=<?= $reserva["id"] ?>">Editar</a>
                                        <a class="btn btn-danger btn-sm" href="index.php?txtID=<?= $reserva["id"] ?>" onclick="return confirm('¿Deseas eliminar esta reserva?');">Borrar</a>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
</body>
</html>
