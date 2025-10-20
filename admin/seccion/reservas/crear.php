<?php
include("../../bd.php");
include("../../templates/header.php");

// Obtenemos las reservas actuales
$sentencia = $pdo->prepare("SELECT * FROM reservas WHERE estado = 1");
$sentencia->execute();
$reservasOcupadas = $sentencia->fetchAll(PDO::FETCH_ASSOC);

// Total de mesas y columnas
$totalMesas = 10;
$columnas = 5;

// Creamos la matriz de mesas
$filas = ceil($totalMesas / $columnas);
$mesas = array_fill(0, $filas, array_fill(0, $columnas, 0));

// Marcamos mesas ocupadas
foreach ($reservasOcupadas as $reserva) {
    $fila = floor(($reserva['mesa'] - 1) / $columnas);
    $col = ($reserva['mesa'] - 1) % $columnas;
    $mesas[$fila][$col] = 1;
}

// Crear reserva
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente = $_POST["cliente"];
    $fecha = $_POST["fecha"];
    $hora = $_POST["hora"];
    $mesa = $_POST["mesa"];

    try {
        $sentencia = $pdo->prepare("INSERT INTO reservas (cliente, fecha, hora, mesa, estado) 
                                    VALUES (:cliente, :fecha, :hora, :mesa, 1)");
        $sentencia->bindParam(":cliente", $cliente);
        $sentencia->bindParam(":fecha", $fecha);
        $sentencia->bindParam(":hora", $hora);
        $sentencia->bindParam(":mesa", $mesa);
        $sentencia->execute();

        header("Location:index.php");
        exit;
    } catch (PDOException $e) {
        echo "Error al crear la reserva: " . $e->getMessage();
    }
}
?>

<!doctype html>
<html lang="es">
<head>
    <title>Crear Reserva</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<main class="container mt-4">
    <div class="card mb-4">
        <div class="card-header">Mapa de Mesas</div>
        <div class="card-body">
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

    <div class="card">
        <div class="card-header">Nueva Reserva</div>
        <div class="card-body">
            <form action="" method="POST">
                <div class="mb-3">
                    <label class="form-label">Nombre del Cliente:</label>
                    <input type="text" class="form-control" name="cliente" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Fecha:</label>
                    <input type="date" class="form-control" name="fecha" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Hora:</label>
                    <input type="time" class="form-control" name="hora" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mesa:</label>
                    <select class="form-select" name="mesa" required>
                        <?php foreach ($mesas as $filaIndex => $fila): ?>
                            <?php foreach ($fila as $colIndex => $estado): ?>
                                <?php $numMesa = $filaIndex * $columnas + $colIndex + 1; ?>
                                <?php if ($numMesa <= $totalMesas && $estado == 0): ?>
                                    <option value="<?= $numMesa ?>">Mesa <?= $numMesa ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-success">Crear Reserva</button>
                <a class="btn btn-primary" href="index.php">Cancelar</a>
            </form>
        </div>
        <div class="card-footer text-muted"></div>
    </div>
</main>
</body>
</html>
