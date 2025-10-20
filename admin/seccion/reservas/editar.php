<?php
include("../../templates/header.php");
include("../../bd.php");

$id = isset($_GET['txtID']) ? $_GET['txtID'] : '';
$cliente = $fecha = $hora = "";
$mesa = $estado = 0;


$sentencia = $pdo->prepare("SELECT * FROM reservas WHERE estado = 1 AND id != :id");
$sentencia->bindParam(':id', $id);
$sentencia->execute();
$reservasOcupadas = $sentencia->fetchAll(PDO::FETCH_ASSOC);


$totalMesas = 10;
$columnas = 5;
$filas = ceil($totalMesas / $columnas);
$mesasArray = array_fill(0, $filas, array_fill(0, $columnas, 0));

foreach ($reservasOcupadas as $reserva) {
    $fila = floor(($reserva['mesa'] - 1) / $columnas);
    $col = ($reserva['mesa'] - 1) % $columnas;
    $mesasArray[$fila][$col] = 1;
}


if ($id) {
    $select = $pdo->prepare("SELECT * FROM reservas WHERE id = :id");
    $select->bindParam(':id', $id);
    $select->execute();
    $row = $select->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $cliente = $row['cliente'];
        $fecha = $row['fecha'];
        $hora = $row['hora'];
        $mesa = $row['mesa'];
        $estado = $row['estado'];
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente = $_POST['cliente'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $mesa = $_POST['mesa'];
    $estado = $_POST['estado'];

    try {
        $stmt = $pdo->prepare("UPDATE reservas 
                               SET cliente = :cliente, fecha = :fecha, hora = :hora, mesa = :mesa, estado = :estado
                               WHERE id = :id");
        $stmt->bindParam(':cliente', $cliente);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':hora', $hora);
        $stmt->bindParam(':mesa', $mesa);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        header("Location:index.php");
        exit;
    } catch (PDOException $e) {
        echo "Error al actualizar la reserva: " . $e->getMessage();
    }
}
?>

<!doctype html>
<html lang="es">
<head>
    <title>Editar Reserva</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<main class="container mt-4">
    <div class="card mb-4">
        <div class="card-header">Mapa de Mesas</div>
        <div class="card-body">
            <?php for ($f = 0; $f < $filas; $f++): ?>
                <div class="row justify-content-center mb-2">
                    <?php for ($c = 0; $c < $columnas; $c++): ?>
                        <?php 
                            $numMesa = $f * $columnas + $c + 1;
                            if ($numMesa > $totalMesas) continue;
                        ?>
                        <div class="col-auto">
                            <div class="card text-center"
                                 style="width: 100px; height: 100px; 
                                        background-color: <?= ($mesasArray[$f][$c] && $numMesa != $mesa) ? '#dc3545' : '#198754' ?>;
                                        color: white; display:flex; align-items:center; justify-content:center;">
                                Mesa <?= $numMesa ?><br>
                                <?= ($mesasArray[$f][$c] && $numMesa != $mesa) ? 'Ocupada' : 'Libre' ?>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            <?php endfor; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Editar Reserva</div>
        <div class="card-body">
            <form action="" method="post">
                <input type="hidden" name="id" value="<?= htmlspecialchars($id); ?>">

                <div class="mb-3">
                    <label class="form-label">Cliente:</label>
                    <input type="text" class="form-control" name="cliente" value="<?= htmlspecialchars($cliente); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Fecha:</label>
                    <input type="date" class="form-control" name="fecha" value="<?= htmlspecialchars($fecha); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Hora:</label>
                    <input type="time" class="form-control" name="hora" value="<?= htmlspecialchars($hora); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mesa:</label>
                    <select class="form-select" name="mesa" required>
                        <?php for ($f = 0; $f < $filas; $f++): ?>
                            <?php for ($c = 0; $c < $columnas; $c++): ?>
                                <?php 
                                    $numMesa = $f * $columnas + $c + 1;
                                    if ($numMesa > $totalMesas) continue;
                                    if ($mesasArray[$f][$c] && $numMesa != $mesa) continue; // ocupada por otra
                                ?>
                                <option value="<?= $numMesa; ?>" <?= ($mesa == $numMesa) ? "selected" : ""; ?>>
                                    Mesa <?= $numMesa; ?>
                                </option>
                            <?php endfor; ?>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Estado:</label>
                    <select class="form-select" name="estado">
                        <option value="0" <?= ($estado == 0) ? "selected" : ""; ?>>Libre</option>
                        <option value="1" <?= ($estado == 1) ? "selected" : ""; ?>>Ocupada</option>
                    </select>
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
