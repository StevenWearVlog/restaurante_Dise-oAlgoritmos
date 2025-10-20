<?php
include("../../bd.php");

$id = $_GET["id"];
$sentencia = $conn->prepare("SELECT * FROM reservas WHERE id=:id");
$sentencia->bindParam(":id", $id);
$sentencia->execute();
$reserva = $sentencia->fetch(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">
    <head>
        <title>Title</title>
        <!-- Required meta tags -->
        <meta charset="utf-8" />
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1, shrink-to-fit=no"
        />

        <!-- Bootstrap CSS v5.2.1 -->
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
            crossorigin="anonymous"
        />
    </head>

    <body>
        

<?php include("../../templates/header.php"); ?>

<h2>Editar Reserva</h2>

<form method="POST" action="update.php">
    <input type="hidden" name="id" value="<?php echo $reserva['id']; ?>">

    <div class="mb-3">
        <label>Nombre Cliente:</label>
        <input type="text" name="nombre_cliente" class="form-control" value="<?php echo $reserva['nombre_cliente']; ?>" required>
    </div>

    <div class="mb-3">
        <label>Teléfono:</label>
        <input type="text" name="telefono" class="form-control" value="<?php echo $reserva['telefono']; ?>" required>
    </div>

    <div class="mb-3">
        <label>Fecha:</label>
        <input type="date" name="fecha" class="form-control" value="<?php echo $reserva['fecha']; ?>" required>
    </div>

    <div class="mb-3">
        <label>Hora:</label>
        <input type="time" name="hora" class="form-control" value="<?php echo $reserva['hora']; ?>" required>
    </div>

    <div class="mb-3">
        <label>Número de Personas:</label>
        <input type="number" name="numero_personas" class="form-control" value="<?php echo $reserva['numero_personas']; ?>" required>
    </div>

    <div class="mb-3">
        <label>Mesa:</label>
        <input type="number" name="mesa" class="form-control" value="<?php echo $reserva['mesa']; ?>" required>
    </div>

    <div class="mb-3">
        <label>Estado:</label>
        <select name="estado" class="form-control">
            <option value="Reservada" <?php echo ($reserva['estado']=="Reservada")?"selected":""; ?>>Reservada</option>
            <option value="Cancelada" <?php echo ($reserva['estado']=="Cancelada")?"selected":""; ?>>Cancelada</option>
            <option value="Completada" <?php echo ($reserva['estado']=="Completada")?"selected":""; ?>>Completada</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Actualizar</button>
    <a href="index.php" class="btn btn-secondary">Cancelar</a>
</form>

<?php include("../../templates/footer.php"); ?>

        <script
            src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
            integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
            crossorigin="anonymous"
        ></script>

        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
            integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
            crossorigin="anonymous"
        ></script>
    </body>
</html>
