<?php
include("../../templates/header.php");
include("../../bd.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $enlace = $_POST['enlace'];

    try {
        $stmt = $pdo->prepare("UPDATE banner SET titulo = :titulo, descripcion = :descripcion, link = :enlace WHERE id = :id");
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':enlace', $enlace);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        header("Location: index.php");
        exit;
    } catch (PDOException $e) {
        echo "Error al actualizar: " . $e->getMessage();
    }
}



?>
<!doctype html>
<html lang="en">

<head>
    <title>Title</title>
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
    <main>
        <br>

        <div class="card">
            <div class="card-header">Banners</div>
            <div class="card-body">

                <form action="" method="post">

                    <div class="mb-3">
                        <label for="titulo" class="form-label">Titulo:</label>
                        <input type="text" class="form-control" name="titulo" value="<?php echo $titulo; ?>" id="titulo" aria-describedby="helpId" placeholder="Escribe el titulo del banner" required>
                    </div>

                    <div class="mb-3">
                        <label for="Descripcion" class="form-label">Descripción:</label>
                        <input type="text" class="form-control" name="descripcion" value="<?php echo $descripcion; ?>" id="descripcion" aria-describedby="helpId" placeholder="Escribe la descripcion" required>
                    </div>

                    <div class="mb-3">
                        <label for="Enlace" class="form-label">Enlace:</label>
                        <input type="hidden" name="id" value="<?php echo $_GET['txtID']; ?>">
                        <input type="text" class="form-control" name="enlace" value="<?php echo $enlace; ?>" id="enlace" aria-describedby="helpId" placeholder="Escribe el enlace" required>
                    </div>
                    

                    <button type="submit" class="btn btn-success">Editar Banner</button>
                    <a name="" id="" class="btn btn-primary" href="index.php" role="button">Cancelar</a>

                </form>

            </div>
            <div class="card-footer text-muted"></div>
        </div>
    </main>
    <footer>
        <?php include("../../templates/footer.php"); ?>
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