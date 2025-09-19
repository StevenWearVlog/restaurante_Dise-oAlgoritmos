<?php
include("../../bd.php");
include("../../templates/header.php");

if($_POST){
    $nombre = isset($_POST["nombre"]) ? $_POST["nombre"] : "";
    $opinion = isset($_POST["opinion"]) ? $_POST["opinion"] : "";

    $sentencia = $conn->prepare("INSERT INTO testimonios (nombre, opinion) 
                                VALUES (:nombre, :opinion);");

    $sentencia->bindParam(":nombre" , $nombre);
    $sentencia->bindParam(":opinion" , $opinion);
    
    $sentencia->execute();
    header("Location:index.php");
}
?>

<!doctype html>
<html lang="en">
    <head>
        <title>Agregar Testimonio</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    </head>

    <body>
        <main>
            <div class="card">
                <div class="card-header">Nuevo Testimonio</div>
                <div class="card-body">
                    <form action="" method="POST">
                        
                        <div class="mb-3">
                            <label class="form-label">Nombre:</label>
                            <input type="text" class="form-control" name="nombre" placeholder="Ejemplo: Juan Pérez" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Opinión:</label>
                            <textarea class="form-control" name="opinion" rows="4" placeholder="Escribe aquí el testimonio..." required></textarea>
                        </div>

                        <button type="submit" class="btn btn-success">Agregar Testimonio</button>
                        <a class="btn btn-primary" href="index.php" role="button">Cancelar</a>
                    </form>
                </div>
            </div>
        </main>
    </body>
</html>
