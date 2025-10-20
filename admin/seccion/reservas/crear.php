<?php
include("../../bd.php");
include("../../templates/header.php");

// Para mostrar mapa simple de mesas ocupadas (opcional)
$totalMesas = 10;
$columnas = 5;
$filas = ceil($totalMesas / $columnas);
$mesas = array_fill(0, $filas, array_fill(0, $columnas, 0));

$occupiedStmt = $pdo->prepare("SELECT mesa, estado FROM reservas");
$occupiedStmt->execute();
$all = $occupiedStmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($all as $r) {
    $estado = $r['estado'];
    $isOcupada = ( $estado === 1 || $estado === '1' || in_array(strtolower((string)$estado), ['ocupado','reservada','reservado']) );
    if ($isOcupada) {
        $m = (int)$r['mesa'];
        if ($m>=1 && $m <= $totalMesas) {
            $fila = floor(($m - 1) / $columnas);
            $col = ($m - 1) % $columnas;
            $mesas[$fila][$col] = 1;
        }
    }
}

// Crear reserva
$errores = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente = trim($_POST["nombre_cliente"] ?? '');
    $telefono = trim($_POST["telefono"] ?? '');
    $fecha = $_POST["fecha"] ?? '';
    $hora = $_POST["hora"] ?? '';
    $numero_personas = (int)($_POST["numero_personas"] ?? 1);
    $mesa = (int)($_POST["mesa"] ?? 0);

    if ($cliente === '') $errores[] = "El nombre es obligatorio.";
    if ($telefono === '') $errores[] = "El teléfono es obligatorio.";
    if ($fecha === '' || $hora === '') $errores[] = "Fecha y hora son obligatorias.";
    if ($mesa < 1 || $mesa > $totalMesas) $errores[] = "Mesa inválida.";

    // Comprobar si mesa ocupada (según estado)
    $check = $pdo->prepare("SELECT COUNT(*) FROM reservas WHERE mesa = :mesa AND (estado = 'ocupado' OR estado = 'Reservada' OR estado = 'reservada' OR estado = 1 OR estado = '1')");
    $check->execute([':mesa' => $mesa]);
    if ($check->fetchColumn() > 0) {
        $errores[] = "La mesa seleccionada ya está ocupada.";
    }

    if (empty($errores)) {
        $insert = $pdo->prepare("INSERT INTO reservas (nombre_cliente, telefono, fecha, hora, numero_personas, mesa, estado)
                                 VALUES (:nombre, :telefono, :fecha, :hora, :num, :mesa, :estado)");
        $estado = 'ocupado'; // guardamos como 'ocupado' por consistencia
        $insert->execute([
            ':nombre' => $cliente,
            ':telefono' => $telefono,
            ':fecha' => $fecha,
            ':hora' => $hora,
            ':num' => $numero_personas,
            ':mesa' => $mesa,
            ':estado' => $estado
        ]);
        header("Location: index.php");
        exit;
    }
}
?>

<!doctype html>
<html lang="es">
<head>
    <title>Crear Reserva</title>
    <meta charset="utf-8" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<main class="container mt-4">

    <?php if (!empty($errores)): ?>
        <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errores as $e) echo "<li>".htmlspecialchars($e)."</li>"; ?></ul></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">Nueva Reserva</div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nombre del Cliente:</label>
                    <input type="text" class="form-control" name="nombre_cliente" value="<?= isset($cliente) ? htmlspecialchars($cliente) : '' ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Teléfono:</label>
                    <input type="text" class="form-control" name="telefono" value="<?= isset($telefono) ? htmlspecialchars($telefono) : '' ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Fecha:</label>
                    <input type="date" class="form-control" name="fecha" value="<?= isset($fecha) ? htmlspecialchars($fecha) : '' ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Hora:</label>
                    <input type="time" class="form-control" name="hora" value="<?= isset($hora) ? htmlspecialchars($hora) : '' ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Número de Personas:</label>
                    <input type="number" class="form-control" name="numero_personas" min="1" value="<?= isset($numero_personas) ? (int)$numero_personas : 1 ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mesa:</label>
                    <select class="form-select" name="mesa" required>
                        <?php
                        // Mostrar solo mesas libres (según el criterio anterior)
                        $ocupadasList = [];
                        foreach ($all as $r) {
                            $st = $r['estado'];
                            if ($st === 1 || $st === '1' || in_array(strtolower((string)$st), ['ocupado','reservada','reservado'])) {
                                $ocupadasList[] = (int)$r['mesa'];
                            }
                        }
                        for ($i = 1; $i <= $totalMesas; $i++):
                            if (!in_array($i, $ocupadasList)):
                        ?>
                            <option value="<?= $i ?>"><?= "Mesa $i" ?></option>
                        <?php
                            endif;
                        endfor;
                        ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-success">Guardar Reserva</button>
                <a class="btn btn-secondary" href="index.php">Cancelar</a>
            </form>
        </div>
    </div>
</main>
</body>
</html>
