<?php
include("../../bd.php");

// Leer todas las reservas
$sentencia = $conn->prepare("SELECT * FROM reservas");
$sentencia->execute();
$reservas = $sentencia->fetchAll(PDO::FETCH_ASSOC);
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

<section class="container">

<h2>Lista de Reservas</h2>

<a href="crear.php" class="btn btn-primary">Nueva Reserva</a>

<table class="table table-bordered mt-3">
    <thead>
        <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Teléfono</th>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Personas</th>
            <th>Mesa</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($reservas as $reserva){ ?>
            <tr>
                <td><?php echo $reserva["id"]; ?></td>
                <td><?php echo $reserva["nombre_cliente"]; ?></td>
                <td><?php echo $reserva["telefono"]; ?></td>
                <td><?php echo $reserva["fecha"]; ?></td>
                <td><?php echo $reserva["hora"]; ?></td>
                <td><?php echo $reserva["numero_personas"]; ?></td>
                <td><?php echo $reserva["mesa"]; ?></td>
                <td><?php echo $reserva["estado"]; ?></td>
                <td>
                    <a href="editar.php?id=<?php echo $reserva['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                    <a href="eliminar.php?id=<?php echo $reserva['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro de eliminar la reserva?')">Eliminar</a>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
</section>

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
