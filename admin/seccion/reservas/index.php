<?php
include("../../bd.php");
include("../../templates/header.php");

// Obtener todas las reservas
$sentencia = $pdo->prepare("SELECT * FROM reservas ORDER BY fecha, hora, mesa");
$sentencia->execute();
$reservasList = $sentencia->fetchAll(PDO::FETCH_ASSOC);

// Borrar reserva si viene txtID
if (isset($_GET["txtID"])) {
    $txtID = (int)$_GET["txtID"];
    $borrar = $pdo->prepare("DELETE FROM reservas WHERE id = :id");
    $borrar->bindParam(":id", $txtID, PDO::PARAM_INT);
    $borrar->execute();
    header("Location: index.php");
    exit;
}

// Para mostrar mapa de mesas
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
<html lang="es">
<head>
    <title>Reservas</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body>
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
                                <th>Telefono</th>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Numero de personas</th>
                                <th>Mesa</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservasList as $reserva): ?>
                                <tr>
                                    <td><?= htmlspecialchars($reserva["id"]) ?></td>
                                    <td><?= htmlspecialchars($reserva["nombre_cliente"]) ?></td>
                                    <td><?= htmlspecialchars($reserva["fecha"]) ?></td>
                                    <td><?= htmlspecialchars($reserva["hora"]) ?></td>
                                    <td><?= htmlspecialchars($reserva["mesa"]) ?></td>
                                    <td><?= htmlspecialchars($reserva["telefono"]) ?></td>
                                    <td><?= htmlspecialchars($reserva["numero_personas"]) ?></td>
                                    <td>
                                        <span class="badge <?= $reserva['estado'] == 'ocupado' ? 'bg-danger' : 'bg-success' ?>">
                            <?= ucfirst($reserva['estado']) ?>
                                    </td>
                                    <td>
                                        <a class="btn btn-info btn-sm" href="editar.php?txtID=<?= urlencode($reserva["id"]) ?>">Editar</a>
                                        <a class="btn btn-danger btn-sm" href="index.php?txtID=<?= urlencode($reserva["id"]) ?>" onclick="return confirm('¿Deseas eliminar esta reserva?');">Borrar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($reservasList)): ?>
                                <tr><td colspan="9" class="text-center">No hay reservas</td></tr>
                            <?php endif; ?>
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
