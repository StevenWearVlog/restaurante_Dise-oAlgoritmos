<?php
include("../../bd.php");
include("../../templates/header.php");

// Borrar reserva si viene txtID (opcional)
if (isset($_GET["txtID"])) {
    $txtID = (int)$_GET["txtID"];
    $borrar = $pdo->prepare("DELETE FROM reservas WHERE id = :id");
    $borrar->bindParam(":id", $txtID, PDO::PARAM_INT);
    $borrar->execute();
    header("Location: index.php");
    exit;
}

// Obtener todas las reservas (lista completa para tabla)
$stmt = $pdo->prepare("SELECT * FROM reservas ORDER BY fecha, hora, mesa");
$stmt->execute();
$reservasList = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Configuración de mapa
$totalMesas = 10;
$columnas = 5;
$filas = ceil($totalMesas / $columnas);
// Inicializar matriz de mesas (0 = libre, 1 = ocupada)
$mesas = array_fill(0, $filas, array_fill(0, $columnas, 0));

// Determinar mesas ocupadas según reservas cuyo estado indica ocupación
foreach ($reservasList as $reserva) {
    $estado = $reserva['estado'];
    // Normalizar y comprobar distintas formas de "ocupado"
    $isOcupada = false;
    if ($estado === 1 || $estado === '1') $isOcupada = true;
    $lower = strtolower((string)$estado);
    if ($lower === 'ocupado' || $lower === 'reservada' || $lower === 'reservado') $isOcupada = true;

    if ($isOcupada) {
        $mesaNum = (int)$reserva['mesa'];
        if ($mesaNum >= 1 && $mesaNum <= $totalMesas) {
            $fila = floor(($mesaNum - 1) / $columnas);
            $col = ($mesaNum - 1) % $columnas;
            $mesas[$fila][$col] = 1;
        }
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
</head>
<body>
<main class="container mt-5">

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Mapa de Mesas</span>
            <a class="btn btn-primary" href="crear.php">Agregar Reserva</a>
        </div>
        <div class="card-body text-center">
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
                                    <div>
                                        Mesa <?= $numMesa ?><br>
                                        <?= $estado ? 'Ocupada' : 'Libre' ?>
                                    </div>
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
                    <thead class="table-light text-center">
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
                    <tbody class="text-center">
                        <?php foreach ($reservasList as $reserva): ?>
                            <tr>
                                <td><?= htmlspecialchars($reserva["id"]) ?></td>
                                <td><?= htmlspecialchars($reserva["nombre_cliente"]) ?></td>
                                <td><?= htmlspecialchars($reserva["telefono"]) ?></td>
                                <td><?= htmlspecialchars($reserva["fecha"]) ?></td>
                                <td><?= htmlspecialchars($reserva["hora"]) ?></td>
                                <td><?= htmlspecialchars($reserva["numero_personas"]) ?></td>
                                <td><?= htmlspecialchars($reserva["mesa"]) ?></td>
                                <td>
                                    <?php
                                        $estadoLabel = $reserva['estado'];
                                        $lower = strtolower((string)$estadoLabel);
                                        $badge = ($lower === 'ocupado' || $estadoLabel === 1 || $estadoLabel === '1' || $lower === 'reservada') ? 'bg-danger' : 'bg-success';
                                        $text = ucfirst($estadoLabel);
                                    ?>
                                    <span class="badge <?= $badge ?>"><?= htmlspecialchars($text) ?></span>
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
    </div>

</main>
<?php include("../../templates/footer.php"); ?>
</body>
</html>
