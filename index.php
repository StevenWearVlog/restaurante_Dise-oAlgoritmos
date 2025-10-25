<?php
include("admin/bd.php");
session_start();
$query = "SELECT * FROM eventos";
$resultado = $pdo->query($query);

$eventos = [];
while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
    $eventos[] = $row;
}


shuffle($eventos);


$sentencia = $pdo->prepare("SELECT * FROM banner LIMIT 1");
$sentencia->execute();
$listaBanner = $sentencia->fetchAll(PDO::FETCH_ASSOC);

$chef1 = $pdo->prepare("SELECT * FROM colaboradores");
$chef1->execute();
$chefLista1 = $chef1->fetchAll(PDO::FETCH_ASSOC);


$testimonios = $pdo->prepare("SELECT * FROM testimonios");
$testimonios->execute();
$testimoniosList = $testimonios->fetchAll(PDO::FETCH_ASSOC);

$platos = $pdo->prepare("SELECT * FROM menu");
$platos->execute();
$menuLista = $platos->fetchAll(PDO::FETCH_ASSOC);

$reservasStmt = $pdo->prepare("SELECT mesa, estado FROM reservas");
$reservasStmt->execute();
$reservasList = $reservasStmt->fetchAll(PDO::FETCH_ASSOC);


// Reservas
$sentencia = $pdo->prepare("SELECT * FROM reservas WHERE estado = 1");
$sentencia->execute();
$reservasActivas = $sentencia->fetchAll(PDO::FETCH_ASSOC);


$totalMesas = 10;
$columnas = 5;
$filas = ceil($totalMesas / $columnas);
$mesasArray = array_fill(0, $filas, array_fill(0, $columnas, 0));


foreach ($reservasActivas as $reserva) {
    $fila = floor(($reserva['mesa'] - 1) / $columnas);
    $col = ($reserva['mesa'] - 1) % $columnas;
    $mesasArray[$fila][$col] = 1;
}


// arbol
$arbol = [
    "pregunta" => "¿Te gustaría comer algo liviano o contundente?",
    "liviano" => [
        "pregunta" => "¿Prefieres algo frío o caliente?",
        "frío" => [
            "pregunta" => "¿Quieres algo dulce?",
            "si" => ["sugerencia" => "Yogurt con frutas y miel"],
            "no" => ["sugerencia" => "Ensalada de atún con limonada"]
        ],
        "caliente" => [
            "pregunta" => "¿Te gustaría algo vegetariano?",
            "si" => ["sugerencia" => "Crema de espinaca con pan artesanal"],
            "no" => ["sugerencia" => "Sopa de pollo con arroz integral"]
        ]
    ],
    "contundente" => [
        "pregunta" => "¿Quieres carne, pollo o pescado?",
        "carne" => [
            "sugerencia" => "Lomo en salsa de champiñones con puré"
        ],
        "pollo" => [
            "sugerencia" => "Pechuga a la plancha con papas criollas"
        ],
        "pescado" => [
            "sugerencia" => "Filete de salmón con ensalada verde"
        ]
    ]
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    if (!isset($_SESSION['nodo'])) $_SESSION['nodo'] = $arbol;
    $nodoActual = $_SESSION['nodo'];
    $accion = $_POST['accion'];

    if ($accion === 'reiniciar') {
        $_SESSION['nodo'] = $arbol;
        $nodoActual = $_SESSION['nodo'];
    } elseif ($accion === 'respuesta' && isset($_POST['respuesta'])) {
        $resp = $_POST['respuesta'];
        if (isset($nodoActual[$resp])) {
            $_SESSION['nodo'] = $nodoActual[$resp];
            $nodoActual = $_SESSION['nodo'];
        }
    }

    header('Content-Type: application/json; charset=utf-8');
    if (isset($nodoActual['sugerencia'])) {
        echo json_encode(["sugerencia" => $nodoActual['sugerencia']]);
    } else {
        echo json_encode(["pregunta" => $nodoActual['pregunta']]);
    }
    exit;
}

?>

<?php
// ----- PILA -----
class PilaPedidos {
    public function apilar($pedido) {
        $_SESSION['pila'][] = $pedido;
    }
    public function desapilar() {
        array_pop($_SESSION['pila']);
    }
    public function obtenerPila() {
        return array_reverse($_SESSION['pila'] ?? []);
    }
}

// ----- COLA -----
class ColaPedidos {
    public function encolar($pedido) {
        $_SESSION['cola'][] = $pedido;
    }
    public function desencolar() {
        if (!empty($_SESSION['cola'])) array_shift($_SESSION['cola']);
    }
    public function obtenerCola() {
        return $_SESSION['cola'] ?? [];
    }
}

// ----- ARREGLO -----
class PlatosVendidos {
    public function agregar($plato) {
        $_SESSION['arreglo'][] = $plato;
        sort($_SESSION['arreglo']);
    }
    public function eliminar($plato) {
        if (isset($_SESSION['arreglo'])) {
            $_SESSION['arreglo'] = array_diff($_SESSION['arreglo'], [$plato]);
        }
    }
    public function obtener() {
        return $_SESSION['arreglo'] ?? [];
    }
}

/* ==========================================================
   ⚡ INICIALIZAR SESIONES SI ESTÁN VACÍAS
========================================================== */
if (!isset($_SESSION['pila'])) $_SESSION['pila'] = [];
if (!isset($_SESSION['cola'])) $_SESSION['cola'] = [];
if (!isset($_SESSION['arreglo'])) $_SESSION['arreglo'] = [
    "Bandeja paisa",
    "Pasta al pesto",
    "Lomo en salsa",
    "Pollo apanado"
];

/* ==========================================================
   🧠 INSTANCIAS
========================================================== */
$pila = new PilaPedidos();
$cola = new ColaPedidos();
$arreglo = new PlatosVendidos();

/* ==========================================================
   🧭 MANEJO DE FORMULARIOS
========================================================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion'])) {
        $accion = $_POST['accion'];
        $dato = trim($_POST['dato'] ?? '');

        switch ($accion) {
            case 'apilar': if ($dato) $pila->apilar($dato); break;
            case 'desapilar': $pila->desapilar(); break;

            case 'encolar': if ($dato) $cola->encolar($dato); break;
            case 'desencolar': $cola->desencolar(); break;

            case 'agregar_plato': if ($dato) $arreglo->agregar($dato); break;
            case 'eliminar_plato': if ($dato) $arreglo->eliminar($dato); break;

            case 'reiniciar': session_destroy(); header("Location: index.php"); exit;
        }
    }
    header("Location: index.php");
    exit;
}

/* ==========================================================
   📦 OBTENER DATOS
========================================================== */
$historialPedidos = $pila->obtenerPila();
$pedidosEnCola = $cola->obtenerCola();
$platosVendidos = $arreglo->obtener();
?>

<!doctype html>
<html lang="en">

<head>
    <title>Restaurante Steven</title>
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
    <link rel="stylesheet" href="styles/main.css">


</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark md-auto">
        <div class="container">

            <a class="navbar-brand" href="#">RESTAURANTE STEVEN</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">

                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-item nav-link active" href="#banner" aria-current="page">Inicio<span class="visually-hidden">(current)</span></a>
                    </li>

                    <li class="nav-item">
                         <a class="nav-item nav-link active" href="#evento-destacado" aria-current="page">Eventos<span class="visually-hidden">(current)</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#Chef">Chef</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#menu">Menu</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#testimonios">Testimonios</a>
                    </li>

                    <li class="nav-item">
                         <a class="nav-item nav-link active" href="#mapa-mesas" aria-current="page">Reservas<span class="visually-hidden">(current)</span></a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#contacto">Contacto</a>
                    </li>

                </ul>
                <a href="admin/login.php"><button>Iniciar sesion</button></a>
            </div>

        </div>



    </nav>

    <section id="banner" class="container-fluid p-0">
        <div class="banner-img" style="position:relative; background:url('images/stake2.jpg') center/cover no-repeat; height: 400px;">
            <div class="banner-text" style="position:absolute; top:50%; left: 50%; transform:translate(-50%, -50%); text-align:center;">
                <?php foreach ($listaBanner as $banner): ?>

                    <h1><?php echo $banner["titulo"]; ?></h1>
                    <p><?php echo $banner["descripcion"] ?></p>
                    <a href="<?php echo $banner["link"] ?>" class="btn btn-primary">Ver menú</a>
                <?php endforeach ?>
            </div>

        </div>

    </section>
    


    <?php if (!empty($eventos)): ?>
        <?php $evento = $eventos[0]; ?>
        <?php $imgPath = "/restaurant/" . $evento['imagen']; ?>



        <section id="evento-destacado" class="container-fluid p-0 my-5">
            <div class="evento-hero"
                style="position: relative; background: url('<?php echo htmlspecialchars($imgPath); ?>') center/cover no-repeat; height: 500px; border-radius: 20px; overflow:hidden;">


                <div style="position:absolute; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5);"></div>


                <div class="evento-text text-white text-center"
                    style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); max-width:700px; padding:20px;">
                    <h2 class="fw-bold display-5"><?php echo htmlspecialchars($evento['titulo']); ?></h2>
                    <p class="lead"><?php echo htmlspecialchars($evento['descripcion']); ?></p>
                    <p><strong><?php echo htmlspecialchars($evento['fecha']); ?></strong></p>
                </div>
            </div>
        </section>
    <?php endif; ?>



    <br>

    <section id="Chef" class="ms-4">
        <h2>Nuestros Chefs</h2>
        <br>
        <div class="row row-cols-3 row-cols-md-d g-4">
            <?php foreach ($chefLista1 as $chef): ?>
                <div class="col d-flex justify-content-center">
                    <div class="card h-100">


                        <img src="images/<?php echo $chef["foto"]; ?>" alt="Chef1" style="max-width:500px; border-radius:9px">
                        <div class="card-body">

                            <h5 class="card-title"><?php echo $chef["nombre"]; ?></h5>
                            <p class="card-text small"><strong><?php echo $chef["descripcion"]; ?></strong></p>
                            <div>
                                <?php if (!empty($chef['linkinstagram'])): ?>
                                    <a href="<?php echo htmlspecialchars($chef['linkinstagram']); ?>"
                                        target="_blank" rel="noopener">
                                        <img src="images/instagram.svg" alt="insta" style="width:24px">
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
                                        <img src="images/facebook.svg" alt="face" style="width:24px">
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
        <h2 class="text-center">Recomendados</h2>
        <br>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php foreach ($menuLista as $menu): ?>
                <div class="col d-flex justify-content-center">
                    <div class="card h-100">
                        <?php 
                        $imgPath = $menu['foto'] ? "/restaurant/" . $menu['foto'] : "uploads/menu/default.jpg";
                        ?>
                        <img src="<?php echo htmlspecialchars($imgPath); ?>" 
                             alt="<?php echo htmlspecialchars($menu['nombre']); ?>" 
                             class="card-img-top rounded-3">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($menu["nombre"]); ?></h5>
                            <p class="card-text small"><strong>Ingredientes: </strong><?php echo htmlspecialchars($menu["ingredientes"]); ?></p>
                            <p class="card-text"><strong>Precio: </strong><?php echo htmlspecialchars($menu["precio"]); ?><strong>$</strong></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- SECCIÓN ÁRBOL -->
<section id="sugerencias" class="container my-5">
    <h2 class="text-center mb-4">¿No sabes qué comer? Te ayudamos</h2>
    <div id="arbol" class="card p-4 text-center">
        <p id="texto" class="fs-4">¿Te gustaría comer algo liviano o contundente?</p>
        <div id="botones">
            <button class="btn btn-success mx-2" onclick="responder('liviano')">Liviano</button>
            <button class="btn btn-danger mx-2" onclick="responder('contundente')">Contundente</button>
        </div>
    </div>
</section>

<script>
// ==========================
// Árbol de Decisiones - Restaurante
// ==========================
const arbol = {
    pregunta: "¿Te gustaría comer algo liviano o contundente?",
    opciones: {
        liviano: {
            pregunta: "¿Prefieres algo frío o caliente?",
            opciones: {
                frío: {
                    pregunta: "¿Quieres algo dulce o salado?",
                    opciones: {
                        dulce: { resultado: "Te recomendamos una ensalada de frutas o un batido natural 🍓" },
                        salado: { resultado: "Te recomendamos una ensalada César o un wrap de pollo 🥗" }
                    }
                },
                caliente: {
                    pregunta: "¿Quieres algo rápido o elaborado?",
                    opciones: {
                        rápido: { resultado: "Prueba una sopa del día o una crema de verduras 🍵" },
                        elaborado: { resultado: "Te recomendamos un filete de pescado al vapor con verduras 🐟" }
                    }
                }
            }
        },
        contundente: {
            pregunta: "¿Prefieres carne, pollo o pasta?",
            opciones: {
                carne: {
                    pregunta: "¿La prefieres asada o en salsa?",
                    opciones: {
                        asada: { resultado: "Prueba nuestra bandeja paisa o un churrasco con papas 🍖" },
                        salsa: { resultado: "Te recomendamos carne en salsa de champiñones o lomo al vino 🍷" }
                    }
                },
                pollo: {
                    pregunta: "¿Quieres algo frito o al horno?",
                    opciones: {
                        frito: { resultado: "Te recomendamos pollo apanado con papas fritas 🍗" },
                        horno: { resultado: "Prueba nuestro pollo al horno con arroz y ensalada 🥘" }
                    }
                },
                pasta: {
                    pregunta: "¿Prefieres con carne o vegetariana?",
                    opciones: {
                        carne: { resultado: "Te recomendamos espaguetis a la boloñesa 🍝" },
                        vegetariana: { resultado: "Prueba una pasta al pesto con vegetales 🥦" }
                    }
                }
            }
        }
    }
};



let nodoActual = arbol; // Nodo inicial del árbol

function responder(opcion) {
    if (!nodoActual.opciones || !nodoActual.opciones[opcion]) {
        document.getElementById("texto").textContent = "Opción no válida.";
        return;
    }

    nodoActual = nodoActual.opciones[opcion];

    // Si es una pregunta, mostramos nuevas opciones
    if (nodoActual.pregunta) {
        document.getElementById("texto").textContent = nodoActual.pregunta;
        mostrarBotones(Object.keys(nodoActual.opciones));
    } 
    // Si ya hay resultado, mostramos la recomendación
    else if (nodoActual.resultado) {
        document.getElementById("texto").textContent = nodoActual.resultado;
        document.getElementById("botones").innerHTML = `
            <button class="btn btn-primary mt-3" onclick="reiniciarArbol()">Volver a empezar 🔁</button>
        `;
    }
}

function mostrarBotones(opciones) {
    const contenedor = document.getElementById("botones");
    contenedor.innerHTML = ""; // Limpiamos botones anteriores
    opciones.forEach(op => {
        const btn = document.createElement("button");
        btn.className = "btn btn-outline-success mx-2";
        btn.textContent = op.charAt(0).toUpperCase() + op.slice(1);
        btn.onclick = () => responder(op);
        contenedor.appendChild(btn);
    });
}

function reiniciarArbol() {
    nodoActual = arbol;
    document.getElementById("texto").textContent = arbol.pregunta;
    mostrarBotones(Object.keys(arbol.opciones));
}
</script>



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

<?php
// index.php
?>

  <!-- ========================== -->
  <!-- 🔹 PILA -->
  <!-- ========================== -->
  <section id="pila" class="ms-4 mt-4">
    <h2 class="text-center mb-3">🧱 Pila (Platos Apilados)</h2>
    <div class="card p-4 text-center">
      <input type="text" id="pilaInput" class="form-control mb-3" placeholder="Agregar plato a la pila">
      <div>
        <button class="btn btn-primary mx-2" onclick="pushPila()">Apilar</button>
        <button class="btn btn-danger mx-2" onclick="popPila()">Desapilar</button>
        <button class="btn btn-secondary mx-2" onclick="limpiarPila()">Vaciar pila</button>
      </div>
      <div id="pilaContenido" class="output-box mt-3">La pila está vacía.</div>
    </div>
  </section>

  <!-- ========================== -->
  <!-- 🔹 COLA -->
  <!-- ========================== -->
  <section id="cola" class="ms-4 mt-4">
    <h2 class="text-center mb-3">🚶‍♂️ Cola (Clientes esperando)</h2>
    <div class="card p-4 text-center">
      <input type="text" id="colaInput" class="form-control mb-3" placeholder="Agregar cliente a la cola">
      <div>
        <button class="btn btn-success mx-2" onclick="enqueue()">Encolar</button>
        <button class="btn btn-danger mx-2" onclick="dequeue()">Desencolar</button>
        <button class="btn btn-secondary mx-2" onclick="limpiarCola()">Vaciar cola</button>
      </div>
      <div id="colaContenido" class="output-box mt-3">No hay clientes en espera.</div>
    </div>
  </section>


  <script>
  // =========================================
  // 🔹 PILA (LIFO)
  // =========================================
  let pila = [];

  function pushPila() {
    const plato = document.getElementById("pilaInput").value.trim();
    if (plato) {
      pila.push(plato);
      document.getElementById("pilaInput").value = "";
      actualizarPila();
    } else {
      alert("⚠️ Ingresa un nombre de plato para apilar.");
    }
  }

  function popPila() {
    if (pila.length > 0) {
      const eliminado = pila.pop();
      alert(`🍽️ Se desapiló: ${eliminado}`);
    } else {
      alert("⚠️ La pila está vacía.");
    }
    actualizarPila();
  }

  function limpiarPila() {
    pila = [];
    actualizarPila();
  }

  function actualizarPila() {
    const cont = document.getElementById("pilaContenido");
    cont.innerHTML = pila.length
      ? "<strong>Platos en la pila (de abajo hacia arriba):</strong><br>" + pila.join(" 🍽️<br>")
      : "La pila está vacía.";
  }

  let cola = [];

  function enqueue() {
    const cliente = document.getElementById("colaInput").value.trim();
    if (cliente) {
      cola.push(cliente);
      document.getElementById("colaInput").value = "";
      actualizarCola();
    } else {
      alert("⚠️ Ingresa un nombre de cliente para encolar.");
    }
  }

  function dequeue() {
    if (cola.length > 0) {
      const atendido = cola.shift();
      alert(`✅ Se atendió a: ${atendido}`);
    } else {
      alert("⚠️ No hay clientes en la cola.");
    }
    actualizarCola();
  }

  function limpiarCola() {
    cola = [];
    actualizarCola();
  }

  function actualizarCola() {
    const cont = document.getElementById("colaContenido");
    cont.innerHTML = cola.length
      ? "<strong>Clientes en espera:</strong><br>" + cola.join(" 🧍‍♂️ → ")
      : "No hay clientes en espera.";
  }

  </script>




    <section id="mapa-mesas" class="container mt-5">
        <h2 class="text-center mb-4">Estado de Mesas</h2>
        <?php for ($f = 0; $f < $filas; $f++): ?>
            <div class="row justify-content-center mb-2">
                <?php for ($c = 0; $c < $columnas; $c++): ?>
                    <?php
                    $numMesa = $f * $columnas + $c + 1;
                    if ($numMesa > $totalMesas) continue;
                    ?>
                    <div class="col-auto">
                        <div class="card text-center"
                            style="width: 100px; height: 100px; 
                                background-color: <?= ($mesasArray[$f][$c]) ? '#dc3545' : '#198754' ?>;
                                color: white; display:flex; align-items:center; justify-content:center;">
                            Mesa <?= $numMesa ?><br>
                            <?= ($mesasArray[$f][$c]) ? 'Ocupada' : 'Libre' ?>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        <?php endfor; ?>
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