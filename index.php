<?php
include("admin/bd.php");
$sentencia = $conn->prepare("SELECT * FROM banner LIMIT 1");
$sentencia->execute();
$listaBanner = $sentencia->fetchAll(PDO::FETCH_ASSOC);

$chef1 = $conn->prepare("SELECT * FROM colaboradores");
$chef1->execute();
$chefLista1 = $chef1->fetchAll(PDO::FETCH_ASSOC);


$testimonios = $conn->prepare("SELECT * FROM testimonios");
$testimonios->execute();
$testimoniosList = $testimonios->fetchAll(PDO::FETCH_ASSOC);

$platos = $conn->prepare("SELECT * FROM menu");
$platos->execute();
$menuLista = $platos->fetchAll(PDO::FETCH_ASSOC);
?>


<!doctype html>
<html lang="en">

<head>
    <title>Restaurante</title>

    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="styles/main.css">


</head>

<body>
<nav class="navbar navbar-expand-lg custom-nav">
  <div class="container">


    <a class="navbar-brand" href="#">Restaurante</a>


    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>


    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link active" href="#banner" aria-current="page">Inicio</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#Chef">Chef</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#menu">Menú</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#testimonios">Testimonios</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#contacto">Contacto</a>
        </li>
        <li class="nav-item">
          <a class="nav-link btn-login" href="/admin/login.php">Iniciar sesión</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<style>

.custom-nav {
  background: linear-gradient(135deg, #1c1c1c, #2c2c2c);
  padding: 12px 30px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
}


.custom-nav .navbar-brand {
  font-size: 1.6rem;
  font-weight: 700;
  color: #ffcc70 !important; 
  letter-spacing: 1px;
  transition: color 0.3s ease;
}

.custom-nav .navbar-brand:hover {
  color: #ffa94d !important;
}

.custom-nav .nav-link {
  color: #ffffff !important;
  font-weight: 500;
  margin: 0 10px;
  transition: color 0.3s ease, transform 0.2s ease;
}

.custom-nav .nav-link:hover {
  color: #ffcc70 !important;
  transform: translateY(-2px);
}

.btn-login {
  display: inline-block;
  background: linear-gradient(135deg, #000000, #333333);
  color: #ffffff !important;
  font-weight: 600;
  font-size: 16px;
  padding: 8px 20px;
  border-radius: 25px;
  text-decoration: none;
  transition: all 0.3s ease;
  box-shadow: 0 4px 10px rgba(0,0,0,0.3);
}

.btn-login:hover {
  background: linear-gradient(135deg, #444444, #000000);
  transform: translateY(-2px);
  box-shadow: 0 6px 14px rgba(0,0,0,0.4);
}
</style>


    <section id="banner" class="container-fluid p-0">
        <div class="banner-img" style="position:relative; background:url('images/banners.jpg') center/cover no-repeat; height: 400px;">
            <div class="banner-text" style="position:absolute; top:50%; left: 50%; transform:translate(-50%, -50%); text-align:center;">
                <?php foreach ($listaBanner as $banner): ?>

                    <h1 style="color: white; -webkit-text-stroke: 3px black; font-size:70px; font-weight: bold;"><?php echo $banner["titulo"]; ?></h1>
                    <p style="color:white; -webkit-text-stroke: 1px black; font-size:25px; font-weight: bold;"><?php echo $banner["descripcion"] ?></p>
                    <img src="<?php echo $banner["link"] ?>" alt="">
                    <a href="#menu" class="btn btn-primary">Ver menú</a>
                <?php endforeach ?>
            </div>

        </div>

    </section>
    <br>
    <div class="d-flex justify-content-center">
        <div class="card text-white bg-dark mb-3 w-75">
            <div class="card-body">
                <h1 class="card-title" style="text-align: center;">Bienvenidos al Restaurante</h1>

            </div>
        </div>
    </div>


    <br>

    <section id="Chef" class="ms-4">
        <h2>Nuestros Chefs</h2>
        <br>
        <div class="row row-cols-3 row-cols-md-d g-4">
            <?php foreach ($chefLista1 as $chef): ?>
                <div class="col d-flex justify-content-center">
                    <div class="card h-100">


                        <img src="images/<?php echo $chef["foto"]; ?>" alt="Chef1" style="max-width:400px; border-radius:9px">
                        <div class="card-body">

                            <h5 class="card-title"><?php echo $chef["nombre"]; ?></h5>
                            <p class="card-text small"><strong><?php echo $chef["descripcion"]; ?></strong></p>
                            <div>
                                <?php if (!empty($chef['linkinstagram'])): ?>
                                    <a href="<?php echo htmlspecialchars($chef['linkinstagram']); ?>"
                                        target="_blank" rel="noopener">
                                        <img src="images/Instagram.png" alt="insta" style="width:24px">
                                    </a>
                                <?php endif; ?>

                                <?php if (!empty($chef['linkyoutube'])): ?>
                                    <a href="<?php echo htmlspecialchars($chef['linkyoutube']); ?>"
                                        target="_blank" rel="noopener">
                                        <img src="images/youtube.svg" alt="youtube" style="width:24px">
                                    </a>
                                <?php endif; ?>

                                <?php if (!empty($chef['linkfacebook'])): ?>
                                    <a href="<?php echo htmlspecialchars($chef['linkfacebook']); ?>"
                                        target="_blank" rel="noopener">
                                        <img src="images/facebook.png" alt="face" style="width:24px">
                                    </a>
                                <?php endif; ?>
                            </div>

                        </div>


                    </div>

                </div>
            <?php endforeach ?>
        </div>


        </div>
        </div>




    </section>


    <section id="testimonios" class="bg-light py-5">
        <div class="container">


            <h2 class="text-center mb-4">Testimonios</h2>

            <div class="row">
                <?php foreach ($testimoniosList as $testimonio): ?>

                    <div class="col-md-6 d-flex">
                        <div class="card mb-4 w-100">
                            <div class="card-body">
                                <p class="card-text"><?php echo $testimonio["opinion"] ?></p>
                            </div>
                            <div class="card-footer text-muted">
                                <?php echo $testimonio["nombre"] ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>

            </div>

        </div>

    </section>

    <section id="menu">
        <div class="container">
            <h2 class="text-center">Recomendados del menu</h2>
            <br>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php
    $menu = [
        "Entradas" => [
            ["nombre" => "Empanadas", "precio" => 5000],
            ["nombre" => "Arepas", "precio" => 4000],
            ["nombre" => "Patacones", "precio" => 6000]
        ],
        "Platos Fuertes" => [
            ["nombre" => "Bandeja Paisa", "precio" => 18000],
            ["nombre" => "Sancocho", "precio" => 15000],
            ["nombre" => "Ajiaco", "precio" => 16000]
        ],
        "Bebidas" => [
            ["nombre" => "Jugo Natural", "precio" => 4000],
            ["nombre" => "Gaseosa", "precio" => 3000],
            ["nombre" => "Cerveza", "precio" => 6000]
        ],
        "Postres" => [
            ["nombre" => "Flan", "precio" => 5000],
            ["nombre" => "Arroz con leche", "precio" => 4500],
            ["nombre" => "Helado", "precio" => 5500]
        ]
    ];

    foreach ($menu as $categoria => $platos) {
        echo "<h2>$categoria</h2><ul>";
        foreach ($platos as $plato) {
            echo "<li>{$plato['nombre']} - $" . number_format($plato['precio']) . "</li>";
        }
        echo "</ul>";
    }
    ?>

            </div>

        </div>

    </section>

    

    <section id="contacto" class="container mt-4"><br>
        <h2>Contacto</h2>
        <p>Para cualquier consulta o pedido, no dudes en contactarte con nosotros.</p>
        <form action="admin/seccion/comentarios/crear.php" method="POST">


            <div class="form-group">
                <label for="nombre">Nombre: </label>
                <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ingrese su nombre"><br>
                <input type="email" class="form-control" id="correo" name="correo" placeholder="Ingrese su correo electrónico" required><br>
                <div class="mb-3">
                    <label for="Mensaje" class="form-label">Mensaje</label>
                    <textarea class="form-control" id="mensaje" name="mensaje" rows="6" placeholder="Escriba su mensaje"></textarea>
                </div>
                <br>
                <input type="submit" class="btn btn-primary" value="Enviar mensaje" name="enviar" id="">
            </div>
        </form>

    </section>
    <br>
    <section id="Horarios" class="ms-4 mt-4">
        <h2 style="text-align: center;">Horarios de atención</h2>
        <div class="d-flex justify-content-center">
            <div class="card text-white bg-dark mb-3 card text-white bg-dark mb-3 w-50">
                <div class="card-body">
                    <h3 class="card-title" style="text-align: center;">Lunes-viernes</h3>
                    <h4 class="card-text" style="text-align: center;">9:00 -- 17:00</h4>
                    <br>
                    <h3 class="card-title" style="text-align: center;">Sábados</h3>
                    <h4 class="card-text" style="text-align: center;">10:00 -- 13:00</h4>
                    <br>
                    <h3 class="card-title" style="text-align: center;">Domingo y feriados</h3>
                    <h4 class="card-text" style="text-align: center;">CERRADO</h4>

                </div>
            </div>
        </div>

    </section>
    <br>



    <header>
        <!-- place navbar here -->
    </header>
    <main></main>
    <footer class="bg-dark text-light text-center">
        <p> &copy; 2025 Derechos reservados</p>

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