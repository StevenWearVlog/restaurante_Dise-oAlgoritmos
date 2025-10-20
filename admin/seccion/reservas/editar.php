<?php
include("../../bd.php");
include("../../templates/header.php");

$id = isset($_GET['txtID']) ? (int)$_GET['txtID'] : 0;
$cliente = $fecha = $hora = $telefono = "";
$mesa = 0;
$estado = 1;
$numero_personas = 1;

// Obtener reservas ocupadas (excluyendo la actual para poder reasignar la misma mesa)
$sentencia = $pdo->prepare("SELECT * FROM reservas WHERE estado = 1 AND id != :id");
$sentencia->bindParam(':id', $id, PDO::PARAM_INT);
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

// Si hay id, cargar datos actuales de la reserva
if ($id) {
    $select = $pdo->prepare("SELECT * FROM reservas WHERE id = :id");
    $select->bindParam(':id', $id, PDO::PARAM_INT);
    $select->execute();
    $row = $select->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // Asegurarse de usar los nombres de columna correctos
        $cliente = $row['nombre_cliente'];
        $fecha = $row['fecha'];
        $hora = $row['hora'];
        $mesa = (int)$row['mesa'];
        $estado = (int)$row['estado'];
        $telefono = $row['telefono'];
        $numero_personas = isset($row['numero_personas']) ? (int)$row['numero_personas'] : 1;
    } else {
        echo "<div class='alert alert-warning'>Reserva no encontrada.</div>";
    }
}

// Procesar actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Tomar valores del POST (si el formulario lo envía por hidden o normal)
    $idPost = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $cliente = isset($_POST['nombre_cliente']) ? trim($_POST['nombre_cliente']) : '';
    $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : '';
    $hora = isset($_POST['hora']) ? $_POST['hora'] : '';
    $mesa = isset($_POST['mesa']) ? (int)$_POST['mesa'] : 0;
    $estado = isset($_POST['estado']) ? (int)$_POST['estado'] : 0;
    $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
    $numero_personas = isset($_POST['numero_personas']) ? (int)$_POST['numero_personas'] : 1;

    // Validación básica
    $errores = [];
    if ($cliente === '') $errores[] = "El nombre del cliente es obligatorio.";
    if ($fecha === '') $errores[] = "La fecha es obligatoria.";
    if ($hora === '') $errores[] = "La hora es obligatoria.";
    if ($telefono === '') $errores[] = "El teléfono es obligatorio.";
    if ($numero_personas <= 0) $errores[] = "El número de personas debe ser mayor a 0.";

    if (empty($errores)) {
        try {
            // Verificar que no exista otra reserva con la misma mesa/fecha/hora (excluyendo la actual)
            $check = $pdo->prepare("SELECT COUNT(*) FROM reservas WHERE mesa = :mesa AND fecha = :fecha AND hora = :hora AND estado = 1 AND id != :id");
            $check->bindParam(':mesa', $mesa, PDO::PARAM_INT);
            $check->bindParam(':fecha', $fecha);
            $check->bindParam(':hora', $hora);
            $check->bindParam(':id', $idPost, PDO::PARAM_INT);
            $check->execute();
            $count = (int)$check->fetchColumn();
            if ($count > 0) {
                $errores[] = "La mesa seleccionada ya tiene una reserva para la misma fecha y hora.";
            } else {
                $stmt = $pdo->prepare("UPDATE reservas 
                                       SET nombre_cliente = :nombre_cliente, fecha = :fecha, hora = :hora, mesa = :mesa, telefono = :telefono, numero_personas = :numero_personas, estado = :estado
                                       WHERE id = :id");
                $stmt->bindParam(':nombre_cliente', $cliente);
                $stmt->bindParam(':fecha', $fecha);
                $stmt->bindParam(':hora', $hora);
                $stmt->bindParam(':mesa', $mesa, PDO::PARAM_INT);
                $stmt->bindParam(':telefono', $telefono);
                $stmt->bindParam(':numero_personas', $numero_personas, PDO::PARAM_INT);
                $stmt->bindParam(':estado', $estado, PDO::PARAM_INT);
                $stmt->bindParam(':id', $idPost, PDO::PARAM_INT);
                $stmt->execute();

                header("Location: index.php");
                exit;
            }
        } catch (PDOException $e) {
            $errores[] = "Error al actualizar la reserva: " . $e->getMessage();
        }
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
    <?php if (!empty($errores)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errores as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

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
             <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nombre del Cliente:</label>
                    <input type="text" class="form-control" name="nombre_cliente" value="<?= $reserva['nombre_cliente'] ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Teléfono:</label>
                    <input type="text" class="form-control" name="telefono" value="<?= $reserva['telefono'] ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Fecha:</label>
                    <input type="date" class="form-control" name="fecha" value="<?= $reserva['fecha'] ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Hora:</label>
                    <input type="time" class="form-control" name="hora" value="<?= $reserva['hora'] ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Número de Personas:</label>
                    <input type="number" class="form-control" name="numero_personas" value="<?= $reserva['numero_personas'] ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mesa:</label>
                    <input type="number" class="form-control" name="mesa" value="<?= $reserva['mesa'] ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Estado:</label>
                    <select name="estado" class="form-select">
                        <option value="ocupado" <?= $reserva['estado'] == 'ocupado' ? 'selected' : '' ?>>Ocupado</option>
                        <option value="libre" <?= $reserva['estado'] == 'libre' ? 'selected' : '' ?>>Libre</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-warning">Actualizar</button>
                <a href="index.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</main>
<footer>
    <?php include("../../templates/footer.php"); ?>
</footer>
</body>
</html>
