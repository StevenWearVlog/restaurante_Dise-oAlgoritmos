<?php
include("../../bd.php");
include("../../templates/header.php");

$errores = [];

// Obtener ID (puede venir por GET txtID)
$id = 0;
if (isset($_GET['txtID'])) $id = (int)$_GET['txtID'];
if (isset($_POST['id'])) $id = (int)$_POST['id'];

if ($id <= 0) {
    echo "<div class='container mt-4 alert alert-danger'>ID de reserva no válido.</div>";
    exit;
}

// Cargar reserva
$select = $pdo->prepare("SELECT * FROM reservas WHERE id = :id");
$select->execute([':id' => $id]);
$reserva = $select->fetch(PDO::FETCH_ASSOC);
if (!$reserva) {
    echo "<div class='container mt-4 alert alert-warning'>Reserva no encontrada.</div>";
    exit;
}

// Obtener mesas ocupadas (excluyendo esta reserva)
$ocupadasStmt = $pdo->prepare("SELECT mesa, estado FROM reservas WHERE id != :id");
$ocupadasStmt->execute([':id' => $id]);
$ocupadasAll = $ocupadasStmt->fetchAll(PDO::FETCH_ASSOC);
$ocupadasList = [];
foreach ($ocupadasAll as $r) {
    $st = $r['estado'];
    if ($st === 1 || $st === '1' || in_array(strtolower((string)$st), ['ocupado','reservada','reservado'])) {
        $ocupadasList[] = (int)$r['mesa'];
    }
}

// Procesar POST (actualizar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente = trim($_POST['nombre_cliente'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $fecha = $_POST['fecha'] ?? '';
    $hora = $_POST['hora'] ?? '';
    $numero_personas = (int)($_POST['numero_personas'] ?? 1);
    $mesa = (int)($_POST['mesa'] ?? 0);
    $estado = $_POST['estado'] ?? 'ocupado';

    if ($cliente === '') $errores[] = "El nombre es obligatorio.";
    if ($telefono === '') $errores[] = "El teléfono es obligatorio.";
    if ($fecha === '' || $hora === '') $errores[] = "Fecha y hora son obligatorias.";
    if ($mesa < 1) $errores[] = "Mesa inválida.";

    // Si la mesa elegida está en ocupadasList y no es la misma que tenía esta reserva, error
    if (in_array($mesa, $ocupadasList) && $mesa != (int)$reserva['mesa']) {
        $errores[] = "La mesa seleccionada está ocupada por otra reserva.";
    }

    if (empty($errores)) {
        $update = $pdo->prepare("UPDATE reservas
                                 SET nombre_cliente = :nombre_cliente,
                                     telefono = :telefono,
                                     fecha = :fecha,
                                     hora = :hora,
                                     numero_personas = :numero_personas,
                                     mesa = :mesa,
                                     estado = :estado
                                 WHERE id = :id");
        $update->execute([
            ':nombre_cliente' => $cliente,
            ':telefono' => $telefono,
            ':fecha' => $fecha,
            ':hora' => $hora,
            ':numero_personas' => $numero_personas,
            ':mesa' => $mesa,
            ':estado' => $estado,
            ':id' => $id
        ]);

        header("Location: index.php");
        exit;
    }
}
?>

<!doctype html>
<html lang="es">
<head>
    <title>Editar Reserva</title>
    <meta charset="utf-8" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<main class="container mt-4">
    <?php if (!empty($errores)): ?>
        <div class="alert alert-danger"><ul><?php foreach($errores as $e) echo "<li>".htmlspecialchars($e)."</li>"; ?></ul></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">Editar Reserva #<?= htmlspecialchars($id) ?></div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
                <div class="mb-3">
                    <label>Nombre del Cliente:</label>
                    <input type="text" name="nombre_cliente" class="form-control" value="<?= htmlspecialchars($reserva['nombre_cliente']) ?>" required>
                </div>
                <div class="mb-3">
                    <label>Teléfono:</label>
                    <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($reserva['telefono']) ?>" required>
                </div>
                <div class="mb-3">
                    <label>Fecha:</label>
                    <input type="date" name="fecha" class="form-control" value="<?= htmlspecialchars($reserva['fecha']) ?>" required>
                </div>
                <div class="mb-3">
                    <label>Hora:</label>
                    <input type="time" name="hora" class="form-control" value="<?= htmlspecialchars($reserva['hora']) ?>" required>
                </div>
                <div class="mb-3">
                    <label>Número de Personas:</label>
                    <input type="number" name="numero_personas" class="form-control" min="1" value="<?= htmlspecialchars($reserva['numero_personas']) ?>" required>
                </div>
                <div class="mb-3">
                    <label>Mesa:</label>
                    <select name="mesa" class="form-select" required>
                        <?php
                        for ($i = 1; $i <= 10; $i++):
                            // permitir seleccionar la mesa actual incluso si aparece en ocupadasList
                            $disabled = (in_array($i, $ocupadasList) && $i != (int)$reserva['mesa']) ? 'disabled' : '';
                            $sel = ($i == (int)$reserva['mesa']) ? 'selected' : '';
                        ?>
                            <option value="<?= $i ?>" <?= $disabled ?> <?= $sel ?>>Mesa <?= $i ?> <?= $disabled ? '(ocupada)' : '' ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Estado:</label>
                    <select name="estado" class="form-select">
                        <option value="ocupado" <?= (strtolower($reserva['estado'])=='ocupado' || $reserva['estado']==1) ? 'selected' : '' ?>>Ocupado</option>
                        <option value="libre" <?= (strtolower($reserva['estado'])=='libre' || $reserva['estado']==0) ? 'selected' : '' ?>>Libre</option>
                        <option value="reservada" <?= (strtolower($reserva['estado'])=='reservada' || strtolower($reserva['estado'])=='reservada') ? 'selected' : '' ?>>Reservada</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-warning">Actualizar</button>
                <a class="btn btn-secondary" href="index.php">Cancelar</a>
            </form>
        </div>
    </div>
</main>
</body>
</html>
