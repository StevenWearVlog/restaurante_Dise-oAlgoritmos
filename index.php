<?php
include("admin/bd.php");
$sentencia = $pdo->prepare("SELECT * FROM banner LIMIT 1");
$sentencia->execute();
$listaBanner = $sentencia->fetchAll(PDO::FETCH_ASSOC);

$chef1 = $pdo->prepare("SELECT * FROM chef");
$chef1->execute();
$chefLista1 = $chef1->fetchAll(PDO::FETCH_ASSOC);


$testimonios = $pdo->prepare("SELECT * FROM testimonios");
$testimonios->execute();
$testimoniosList = $testimonios->fetchAll(PDO::FETCH_ASSOC);

$platos = $pdo->prepare("SELECT * FROM menu");
$platos->execute();
$menuLista = $platos->fetchAll(PDO::FETCH_ASSOC);
?>


<!doctype html>
<html lang="en">
    <head>
        <title>Title</title>
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
        

<nav class="navbar navbar-expand navbar-dark bg-dark">
    <div class="container">
            <a class="navbar-brand" href=""#banner">Restaurante</a>
    <ul class="nav navbar-nav">
        <li class="nav-item">
            <a class="nav-link" href="#Menu" aria-current="page"
                >Menu del dia <span class="visually-hidden">(current)</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#cheff">Chefs</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#testimonios" aria-current="page"
                >Testimonios <span class="visually-hidden">(current)</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#contacto">Contacto</a>
        </li>
    </ul>
    </div>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
    <div class="collapse #navbar-collapse" id="navbarNav"></div>


</nav>

<section id="banner" class="container-fluid p-0">
    <div class="banner-img" style="position: relative; background: url('/images/banner.jpg') center/cover no-repeat; height: 400px;">

        <div class="banner-text" style="position:absolute; top:50%; left:50%; transform: translate(-50%, -50%); text-align:center; color:#fff;">
            <h1>Restaurante Freddy</h1>
            <p>Bienvenido a nuestro restaurante</p>
            <a href="menu" class="btn btn-primary">Reserva tu mesa</a>
        </div>
    </div>
</section>
<br>
<section id="cheff" style="margin-top:10px;" >

    <div class="d-flex justify-content-center gap-3">
        <div class="card h-100" style="width: 18rem;">
            <img src="/images/81896100-cooking-profession-and-people-concept-happy-male-chef-cook-with-crossed-hands-at-restaurant.jpg" class="card-img-top" alt="Bandeja Paisa">
            <div class="card-body">
                <h5 class="card-title">Patricio</h5>
                <p class="card-text small"><strong>Ingredientes</strong></p>
                <p class="card-text">Precio: 220000</p>
            </div>
        </div>

        <div class="card h-100" style="width: 18rem;">
            <img src="/images/45904861-cooking-profession-vegetarian-food-and-people-concept-happy-male-chef-chopping-pepper-over.jpg" class="card-img-top" alt="Bandeja Paisa">
            <div class="card-body">
                <h5 class="card-title">Patroclo</h5>
                <p class="card-text small"><strong>Ingredientes</strong></p>
                <p class="card-text">Precio: 220000</p>
            </div>
        </div>

        <div class="card h-100" style="width: 18rem;">
            <img src="/images/chef-qsp56rmf4us7gld77kmxdk7ouzb0hh3kd0sp5pjwhc.jpg" class="card-img-top" alt="Bandeja Paisa">
            <div class="card-body">
                <h5 class="card-title">Jorge</h5>
                <p class="card-text small"><strong>Ingredientes</strong></p>
                <p class="card-text">Precio: 220000</p>
            </div>
    </div>
</div>
</section>

<section id="testimonios" class="bg-light py-5">
    <div class="container">
        <h2 class="text-center mb-4"> Testimonios</h2>
        <div class="row">

            <div class="col-md 6 d-flex">
                <div class="card mb-4 w-100">
                    <div class="card-body">
                        <p class="card-text"> Sirven muy buena comida</p>
                    </div>
                    <div class="card-footer text-muted">
                        Oscar Jimenez
                    </div>
                </div>
            </div>
            <div class="col-md-6 d-flex">
                <div class="card mb-4 w-100">
                    <div class="card-body">
                        <p class="card-text">Muy buena atencion</p>
                    </div>
                    <div class="card-footer text-muted">
                        Pedro Mondragon
                    </div>
                </div>
            </div>

            <section>
                <h2 class="text-center">Recomendados</h2>

                <br>
        
                <div class="row row-cols-1 row-cols-md-d g-4">

                    <div class="d-flex justify-content-center gap-3">
    <div class="card h-100" style="width: 18rem;" id="menu">
        <img src="/images/Bandeja_paisa,_plato_Colombiano.jpg" class="card-img-top" alt="Bandeja Paisa">
        <div class="card-body">
            <h5 class="card-title">Bandeja Paisa</h5>
            <p class="card-text small"><strong>Ingredientes</strong></p>
            <p class="card-text">Precio: 220000</p>
        </div>
    </div>

    <div class="card h-100" style="width: 18rem;">
        <img src="/images/Bandeja_paisa,_plato_Colombiano.jpg" class="card-img-top" alt="Bandeja Paisa">
        <div class="card-body">
            <h5 class="card-title">Bandeja Paisa</h5>
            <p class="card-text small"><strong>Ingredientes</strong></p>
            <p class="card-text">Precio: 220000</p>
        </div>
    </div>

    <div class="card h-100" style="width: 18rem;">
        <img src="/images/Bandeja_paisa,_plato_Colombiano.jpg" class="card-img-top" alt="Bandeja Paisa">
        <div class="card-body">
            <h5 class="card-title">Bandeja Paisa</h5>
            <p class="card-text small"><strong>Ingredientes</strong></p>
            <p class="card-text">Precio: 220000</p>
        </div>
    </div>
    <div class="card h-100" style="width: 18rem;">
        <img src="/images/Bandeja_paisa,_plato_Colombiano.jpg" class="card-img-top" alt="Bandeja Paisa">
        <div class="card-body">
            <h5 class="card-title">Bandeja Paisa</h5>
            <p class="card-text small"><strong>Ingredientes</strong></p>
            <p class="card-text">Precio: 220000</p>
        </div>
    </div>
</div>

                            
                        </div>
                        </div>
                    </div>
                    
                    
                </div>
            </section>


        </div>
    </div>

</section>

<section id="contacto" class="container mt-4"> <br>
    <h2>Contacto</h2>
    <p>Para cualquier consulta o pedido, no dudes en contactarte con nosotros</p>
    <form action="?" method="post">

    <div class="form-group">
        <label for="nombre">Name</label>
        <input
            type="text"
            name="nombre"
            id="nombre"
            class="form-control"
            placeholder="Ingrese su nombre"/> <br>
            <input
            type="email"
            name="email"
            id="email"
            class="form-control"
            placeholder="Ingrese su correo electronico"/> <br>
            <input
            type="text"
            name="telefono"
            id="telefono"
            class="form-control"
            placeholder="Ingrese su numero de telefono"/> <br>
            <div class="mb-3">
                <label for="Mensaje" class="form-label">Mensaje</label>
                <textarea class="form-control" name="mensaje" id="mensaje" rows="6" placeholder="Escriba su mensaje"></textarea>
            </div>
            <br>
            <input type="submit" class="btn btn-primary" value="enviar" id="enviar">
</br>
</br>
</br>
    </div>
    </form>
    
</section>
<br>

<section id="horarios" class="container my-5">
    <h2 class="text-center mb-4">Horarios de Atención</h2>
    <div class="table-responsive">
        <table class="table table-striped table-bordered text-center">
            <thead class="table-dark">
                <tr>
                    <th>Día</th>
                    <th>Apertura</th>
                    <th>Cierre</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Lunes - Viernes</td>
                    <td>10:00 AM</td>
                    <td>10:00 PM</td>
                </tr>
                <tr>
                    <td>Sábados</td>
                    <td>11:00 AM</td>
                    <td>11:30 PM</td>
                </tr>
                <tr>
                    <td>Domingos</td>
                    <td>12:00 PM</td>
                    <td>9:00 PM</td>
                </tr>
                <tr>
                    <td>Festivos</td>
                    <td>12:00 PM</td>
                    <td>8:00 PM</td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

        <header>
            <!-- place navbar here -->
        </header>
        <main></main>
        <footer class="bg-dark  text-light text-center">
            <p> &copy; 2025 Derechos reservado</p>
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
