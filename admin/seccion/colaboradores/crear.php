<?php
include("../../bd.php");
include("../../templates/header.php");

if($_POST){
    $nombre = isset($_POST["nombre"]) ? $_POST["nombre"] : "";
    $descripcion = isset($_POST["descripcion"]) ? $_POST["descripcion"] : "";
    $linkfacebook = isset($_POST["linkfacebook"]) ? $_POST["linkfacebook"] : "";
    $linkinstagram = isset($_POST["linkinstagram"]) ? $_POST["linkinstagram"] : "";
    $linkyoutube = isset($_POST["linkyoutube"]) ? $_POST["linkyoutube"] : "";

    $foto = "";
    if(isset($_FILES["foto"]["name"]) && $_FILES["foto"]["name"] != ""){
        $foto = time()."_".$_FILES["foto"]["name"]; // nombre único
        $tmpFoto = $_FILES["foto"]["tmp_name"];
        move_uploaded_file($tmpFoto, "../../img/".$foto);
    }

    $sentencia = $conn->prepare("INSERT INTO colaboradores (nombre, descripcion, linkfacebook, linkinstagram, linkyoutube, foto) 
                                VALUES (:nombre, :descripcion, :linkfacebook, :linkinstagram, :linkyoutube, :foto);");

    $sentencia->bindParam(":nombre" , $nombre);
    $sentencia->bindParam(":descripcion" , $descripcion);
    $sentencia->bindParam(":linkfacebook", $linkfacebook);
    $sentencia->bindParam(":linkinstagram", $linkinstagram);
    $sentencia->bindParam(":linkyoutube", $linkyoutube);
    $sentencia->bindParam(":foto", $foto);
    
    $sentencia->execute();
    header("Location:index.php");
}
?>

<!doctype html>
<html lang="en">
    <head>
        <title>Agregar Chef</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    </head>

    <body>
        <main>
            <div class="card">
                <div class="card-header">Agregar Chef</div>
                <div class="card-body">
                    <form action="" method="POST" enctype="multipart/form-data">
                        
                        <div class="mb-3">
                            <label class="form-label">Nombre:</label>
                            <input type="text" class="form-control" name="nombre" placeholder="Escriba el nombre del chef" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descripción:</label>
                            <input type="text" class="form-control" name="descripcion" placeholder="Escriba la descripción" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Facebook:</label>
                            <input type="text" class="form-control" name="linkfacebook" placeholder="URL de Facebook" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Instagram:</label>
                            <input type="text" class="form-control" name="linkinstagram" placeholder="URL de Instagram" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">YouTube:</label>
                            <input type="text" class="form-control" name="linkyoutube" placeholder="URL de YouTube" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Foto:</label>
                            <input type="file" class="form-control" name="foto" placeholder="Nombre o ruta de la foto" required>
                        </div>

                        <button type="submit" class="btn btn-success">Crear Chef</button>
                        <a class="btn btn-primary" href="index.php" role="button">Cancelar</a>
                    </form>
                </div>
            </div>
        </main>
    </body>
</html>
