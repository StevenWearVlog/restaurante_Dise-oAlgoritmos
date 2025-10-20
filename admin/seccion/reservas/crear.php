<?php
include("../../bd.php");
include("../../templates/header.php");

// Obtenemos las reservas actuales (ocupadas)
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
    // Capturar y sanitizar (mínimo)
    $cliente = isset($_POST["nombre_cliente"]) ? trim($_POST["nombre_cliente"]) : '';
    $fecha = isset($_POST["fecha"]) ? $_POST["fecha"] : '';
    $hora = isset($_POST["hora"]) ? $_POST["hora"] : '';
    $mesa = isset($_POST["mesa"]) ? (int)$_POST["mesa"] : 0;
    $telefono = isset($_POST["telefono"]) ? trim($_POST["telefono"]) : '';
    $numero_personas = isset($_POST["numero_personas"]) ? (int)$_POST["numero_personas"] : 0;

    // Validaciones básicas
    $errores = [];
    if ($cliente === '') $errores[] = "El nombre del cliente es obligatorio.";
    if ($fecha === '') $errores[] = "La fecha es obligatoria.";
    if ($hora === '') $errores[] = "La hora es obligatoria.";
    if ($telefono === '') $errores[] = "El teléfono es obligatorio.";
    if ($numero_personas <= 0) $errores[] = "El número de personas debe ser mayor a 0.";
    if ($mesa <= 0 || $mesa > $totalMesas) $errores[] = "Mesa inválida.";

    if (empty($errores)) {
        try {
            // Opcional: comprobar que la mesa no esté ya ocupada (estado=1) en la misma fecha/hora
            $check = $pdo->prepare("SELECT COUNT(*) FROM reservas WHERE mesa = :mesa AND fecha = :fecha AND hora = :hora AND estado = 1");
            $check->bindParam(':mesa', $mesa, PDO::PARAM_INT);
            $check->bindParam(':fecha', $fecha);
            $check->bindParam(':hora', $hora);
            $check->execute();
            $count = (int)$check->fetchColumn();
            if ($count > 0) {
                $errores[] = "La mesa seleccionada ya tiene una reserva para la misma fecha y hora.";
            } else {
                $sentencia = $pdo->prepare("INSERT INTO reservas (nombre_cliente, fecha, hora, mesa, telefono, numero_personas, estado) 
                                            VALUES (:nombre_cliente, :fecha, :hora, :mesa, :telefono, :numero_personas, 1)");
                $sentencia->bindParam(":nombre_cliente", $cliente);
                $sentencia->bindParam(":fecha", $fecha);
                $sentencia->bindParam(":hora", $hora);
                $sentencia->bindParam(":mesa", $mesa, PDO::PARAM_INT);
                $sentencia->bindParam(":telefono", $telefono);
                $sentencia->bindParam(":numero_personas", $numero_personas, PDO::PARAM_INT);
                $sentencia->execute();

                header("Location: index.php");
                exit;
            }
        } catch (PDOException $e) {
            $errores[] = "Error al crear la reserva: " . $e->getMessage();
        }
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
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nombre del Cliente:</label>
                    <input type="text" class="form-control" name="nombre_cliente" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Teléfono:</label>
                    <input type="text" class="form-control" name="telefono" required>
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
                    <label class="form-label">Número de Personas:</label>
                    <input type="number" class="form-control" name="numero_personas" required min="1">
                </div>

                <div class="mb-3">
                    <label class="form-label">Mesa:</label>
                    <select class="form-select" name="mesa" required>
                        <?php
                        // Cargar mesas libres (donde no haya reserva activa)
                        $stmt = $pdo->prepare("SELECT mesa FROM reservas WHERE estado = 'ocupado'");
                        $stmt->execute();
                        $ocupadas = $stmt->fetchAll(PDO::FETCH_COLUMN);

                        for ($i = 1; $i <= 10; $i++):
                            if (!in_array($i, $ocupadas)):
                        ?>
                                <option value="<?= $i ?>">Mesa <?= $i ?></option>
                        <?php 
                            endif;
                        endfor;
                        ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-success">Guardar Reserva</button>
                <a href="index.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
        <div class="card-footer text-muted"></div>
    </div>
</main>
</body>
</html>
