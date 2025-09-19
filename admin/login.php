<?php
session_start();
include("bd.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario   = trim($_POST['usuario'] ?? "");
    $password  = trim($_POST['password'] ?? "");

    if ($usuario === "" || $password === "") {
        echo "<div class='alert alert-danger'>Por favor complete todos los campos.</div>";
    } else {

        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE usuario = :usuario LIMIT 1");
        $stmt->bindParam(":usuario", $usuario);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

    
        if ($user && $password === $user["password"]) {
            $_SESSION['usuario'] = $user['usuario'];
            header("Location: index.php"); 
            exit;
        } else {
            echo "<div class='alert alert-danger'>Usuario o contraseña incorrectos.</div>";
        }
    }
}
?>




<!doctype html>
<html lang="en">

<head>
    <title>Login</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS v5.2.1 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
        crossorigin="anonymous" />
</head>

<body>
    <header>
        <!-- place navbar here -->
    </header>

    <main>

        <div class="container">
            <div
                class="row justify-content-center align-items-center g-2">
                <div class="col-md-6">
                    <br>
                    <div class="card text-center">
                        <div class="card-header">Login</div>
                        <div class="card-body">
                            <form action="" method="post">
                                <div class="mb-3 row">
                                    <label for="inputName" class="col-4 col-form-label">Usuario</label>
                                    <div class="col-8">
                                        <input type="text" class="form-control" id="inputName" name="usuario" placeholder="Ingrese su usuario" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="inputPass" class="col-4 col-form-label">Contraseña</label>
                                    <div class="col-8">
                                        <input type="password" class="form-control" id="inputPass" name="password" placeholder="Ingrese su contraseña" required>
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <div class="offset-sm-4 col-sm-8">
                                        <button type="submit" class="form-control btn btn-primary" name="login">Ingresar</button>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
    <footer>
        <!-- place footer here -->
    </footer>
    <!-- Bootstrap JavaScript Libraries -->
    <script
        src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
        crossorigin="anonymous"></script>
</body>

</html>