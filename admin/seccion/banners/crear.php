<?php
include("../../bd.php");
include("../../templates/header.php");

if($_POST){
    $titulo = isset($_POST["titulo"]) ? $_POST["titulo"] : "";
    $descripcion = isset($_POST["Descripcion"]) ? $_POST["Descripcion"] : "";
    $enlace = isset($_POST["Enlace"]) ? $_POST["Enlace"] : "";

    

    //print_r($titulo);
    //print_r($descripcion);
    //print_r($enlace);

    $sentencia = $pdo->prepare("INSERT INTO banner (titulo,descripcion,link) VALUES (:titulo, :descripcion, :enlace);");
    $sentencia->bindParam(":titulo" , $titulo);
    $sentencia->bindParam(":descripcion" , $descripcion);
    $sentencia->bindParam(":enlace", $enlace);
    $sentencia->execute();
    header("Location:index.php");


}


?>

<!doctype html>
<html lang="en">
    <head>
        <title>Crear</title>
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
        <main>
            <div class="card">
                <div class="card-header">Banners</div>
                <div class="card-body">
                    <form action="" method="POST">
                        
                        <div class="mb-3">
                            <label for="" class="form-label">Titulo:</label>
                            <input type="text" class="form-control" name="titulo" aria-describedby="helpId" placeholder="Escriba el titulo del banner">
                        </div>

                        <div class="mb-3">
                            <label for="titulo" class="form-label">Descripción:</label>
                            <input type="text" class="form-control" name="Descripcion" id="Descripcion" aria-describedby="helpId" placeholder="Escriba la descripción del banner">
                        </div>

                        <div class="mb-3">
                            <label for="Enlace" class="form-label">Enlace:</label>
                            <input type="text" class="form-control" name="Enlace" id="Enlace" aria-describedby="helpId" placeholder="Escriba el enlace del banner">
                        </div>

                        <button type="submit" class="btn btn-success">Crear Banner</button>
                        <a name="" id="" class="btn btn-primary" href="index.php" role="button">Cancelar</a>
                    </form>
                    
                   
                </div>
                <div class="card-footer text-muted"></div>
            </div>
            
        </main>
        <footer>
            <!-- place footer here -->
        </footer>
        <!-- Bootstrap JavaScript Libraries -->
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
