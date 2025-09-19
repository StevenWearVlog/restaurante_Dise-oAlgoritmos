<?php
include("../../bd.php");
include("../../templates/header.php");

if($_POST){
    $usuario = isset($_POST["usuario"]) ? $_POST["usuario"] : "";
    $password = isset($_POST["password"]) ? $_POST["password"] : "";
    $correo   = isset($_POST["correo"]) ? $_POST["correo"] : "";

    
    $sentencia = $conn->prepare("INSERT INTO usuarios (usuario, password, correo) 
                                VALUES (:usuario, :password, :correo);");

    $sentencia->bindParam(":usuario", $usuario);
    $sentencia->bindParam(":password", $password);
    $sentencia->bindParam(":correo", $correo);
    
    $sentencia->execute();
    header("Location:index.php");
}
?>

<!doctype html>
<html lang="es">
    <head>
        <title>Crear Usuario</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    </head>

    <body>
        <main>
            <div class="card">
                <div class="card-header">Nuevo Usuario</div>
                <div class="card-body">
                    <form action="" method="POST">
                        
                        <div class="mb-3">
                            <label class="form-label">Usuario:</label>
                            <input type="text" class="form-control" name="usuario" placeholder="Escriba el nombre de usuario" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Correo:</label>
                            <input type="email" class="form-control" name="correo" placeholder="ejemplo@correo.com" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Contraseña:</label>
                            <input type="password" class="form-control" name="password" placeholder="Escriba la contraseña" required>
                        </div>

                        <button type="submit" class="btn btn-success">Crear Usuario</button>
                        <a class="btn btn-primary" href="index.php" role="button">Cancelar</a>
                    </form>
                </div>
            </div>
        </main>
    </body>
</html>
