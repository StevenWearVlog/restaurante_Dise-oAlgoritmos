<?php
include("../../bd.php");
include("../../templates/header.php");

if($_POST){
    $nombre = isset($_POST["nombre"]) ? $_POST["nombre"] : "";
    $correo = isset($_POST["correo"]) ? $_POST["correo"] : "";
    $mensaje = isset($_POST["mensaje"]) ? $_POST["mensaje"] : "";

    $sentencia = $conn->prepare("INSERT INTO mensaje (nombre, correo, mensaje) 
                                VALUES (:nombre, :correo, :mensaje);");

    $sentencia->bindParam(":nombre" , $nombre);
    $sentencia->bindParam(":correo" , $correo);
    $sentencia->bindParam(":mensaje", $mensaje);
    
    $sentencia->execute();
    header("Location:index.php");
}
?>

<!doctype html>
<html lang="en">
    <head>
        <title>Agregar Mensaje</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    </head>

    <body>
        <main>
            <div class="card">
                <div class="card-header">Nuevo Mensaje</div>
                <div class="card-body">
                    <form action="" method="POST">
                        
                        <div class="mb-3">
                            <label class="form-label">Nombre:</label>
                            <input type="text" class="form-control" name="nombre" placeholder="Escriba su nombre" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Correo:</label>
                            <input type="email" class="form-control" name="correo" placeholder="Escriba su correo" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mensaje:</label>
                            <textarea class="form-control" name="mensaje" rows="3" placeholder="Escriba su mensaje" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-success">Enviar Mensaje</button>
                        <a class="btn btn-primary" href="index.php" role="button">Cancelar</a>
                    </form>
                </div>
            </div>
        </main>
    </body>
</html>

