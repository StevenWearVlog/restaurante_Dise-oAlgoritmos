<?php
include("../../bd.php");
include("../../templates/header.php");

if($_POST){
    $nombre = isset($_POST["nombre"]) ? $_POST["nombre"] : "";
    $ingredientes = isset($_POST["ingredientes"]) ? $_POST["ingredientes"] : "";
    $precio = isset($_POST["precio"]) ? $_POST["precio"] : "";
    $foto = isset($_POST["foto"]) ? $_POST["foto"] : "";

    $sentencia = $conn->prepare("INSERT INTO menu (nombre, ingredientes, precio, foto) 
                                VALUES (:nombre, :ingredientes, :precio, :foto);");

    $sentencia->bindParam(":nombre" , $nombre);
    $sentencia->bindParam(":ingredientes" , $ingredientes);
    $sentencia->bindParam(":precio" , $precio);
    $sentencia->bindParam(":foto", $foto);
    
    $sentencia->execute();
    header("Location:index.php");
}
?>

<!doctype html>
<html lang="en">
    <head>
        <title>Agregar Platillo</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    </head>

    <body>
        <main>
            <div class="card">
                <div class="card-header">Nuevo Platillo</div>
                <div class="card-body">
                    <form action="" method="POST">
                        
                        <div class="mb-3">
                            <label class="form-label">Nombre del Platillo:</label>
                            <input type="text" class="form-control" name="nombre" placeholder="Ejemplo: Pizza Margarita" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ingredientes:</label>
                            <input type="text" class="form-control" name="ingredientes" placeholder="Ejemplo: masa, queso, tomate" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Precio:</label>
                            <input type="number" class="form-control" name="precio" placeholder="Ejemplo: 25000" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Foto:</label>
                            <input type="text" class="form-control" name="foto" placeholder="Ruta o nombre de la foto" required>
                        </div>

                        <button type="submit" class="btn btn-success">Agregar Platillo</button>
                        <a class="btn btn-primary" href="index.php" role="button">Cancelar</a>
                    </form>
                </div>
            </div>
        </main>
    </body>
</html>
