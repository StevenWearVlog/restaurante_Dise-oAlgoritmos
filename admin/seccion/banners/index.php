<?php
include("../../bd.php");
$sentencia=$conn->prepare("SELECT * FROM banner;");
$sentencia->execute();
$resultado=$sentencia->fetchAll(PDO::FETCH_ASSOC);

if(isset($_GET["txtID"])){
    $txtID=(isset($_GET["txtID"]))?$_GET["txtID"]:"";

    $borrar=$conn->prepare("DELETE FROM banner WHERE id=:id");
    
    $borrar->bindParam(":id", $txtID);
    $borrar->execute();
    header("Location:index.php");
}
?>

<!doctype html>
<html lang="en">

<head>
    <title>banner</title>
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
        <?php include("../../templates/header.php"); ?>
    </header>
    <main>
        <section class="container">
            <div class="card">

                <div class="card-header">
                    <a name="" id="" class="btn btn-primary" href="crear.php" role="button">Agregar registros</a>
                </div>
                <div class="card-body">
                    <div
                        class="table-responsive-sm">
                        <table
                            class="table">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Titulo</th>
                                    <th scope="col">Descripcion</th>
                                    <th scope="col">Enlace</th>
                                    <th scope="col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($resultado as $key => $value):?>
                                <tr class="">
                                    <td><?php echo $value["id"];?></td>
                                    <td><?php echo $value["titulo"];?></td>
                                    <td><?php echo $value["descripcion"];?></td>
                                    <td><?php echo $value["link"];?></td>
                                    <td>
                                        <a name="" id="" class="btn btn-info" href="editar.php?txtID=<?php echo $value["id"]; ?>" role="button">Editar</a>
                                        <a name="" id="" class="btn btn-danger" href="index.php?txtID= <?php echo $value["id"];?>" role="button">Borrar</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="card-footer text-muted"></div>

            </div>
        </section>

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