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

class GrafoRestaurante {
    public $grafo = [];

    public function __construct() {
        $this->grafo = [
            "Cocina" => ["Barra", "Bodega"],
            "Barra" => ["Cocina", "Mesas", "Caja"],
            "Mesas" => ["Barra", "Caja", "Salida"],
            "Bodega" => ["Cocina"],
            "Caja" => ["Barra", "Mesas", "Salida"],
            "Salida" => ["Mesas", "Caja"]
        ];
    }

    // Obtener las conexiones de un nodo
    public function obtenerVecinos($zona) {
        return $this->grafo[$zona] ?? [];
    }

    // Algoritmo BFS: encuentra ruta más corta entre dos puntos
    public function rutaMasCorta($inicio, $fin) {
        if (!isset($this->grafo[$inicio]) || !isset($this->grafo[$fin])) return null;

        $cola = [[$inicio]];
        $visitados = [$inicio];

        while (!empty($cola)) {
            $ruta = array_shift($cola);
            $ultimo = end($ruta);

            if ($ultimo === $fin) return $ruta;

            foreach ($this->grafo[$ultimo] as $vecino) {
                if (!in_array($vecino, $visitados)) {
                    $visitados[] = $vecino;
                    $nuevaRuta = $ruta;
                    $nuevaRuta[] = $vecino;
                    $cola[] = $nuevaRuta;
                }
            }
        }
        return null;
    }
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
$grafo = new GrafoRestaurante();

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
    <link rel="stylesheet" href="styles/styles.css">


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
        <div class="banner-img" style="position:relative; background:url('images/banners.jpg') center/cover no-repeat; height: 400px;">
            <div class="banner-text" style="position:absolute; top:50%; left: 50%; transform:translate(-50%, -50%); text-align:center;">
                <?php foreach ($listaBanner as $banner): ?>

                    <h1><?php echo $banner["titulo"]; ?></h1>
                    <p><?php echo $banner["descripcion"] ?></p>
                    <a href="#menu" class="btn btn-primary">Ver menú</a>
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
                                        <img src="images/instagram.png" alt="insta" style="width:24px">
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
    <h2 class="text-center">Recomendados</h2>
    <br>
    <div class="row row-cols-1 row-cols-md-3 g-4">
      <?php foreach ($menuLista as $menu): ?>
        <div class="col d-flex justify-content-center">
          <div class="card h-100">
            <?php 
            $imgPath = !empty($menu['foto']) && file_exists($menu['foto']) 
                ? $menu['foto'] 
                : "uploads/menu/default.jpg";
            ?>
            <img src="<?php echo htmlspecialchars($imgPath); ?>" 
                 alt="<?php echo htmlspecialchars($menu["nombre"]); ?>" 
                 class="card-img-top rounded-3">
            <div class="card-body">
              <h5 class="card-title"><?php echo htmlspecialchars($menu["nombre"]); ?></h5>
              <p class="card-text small">
                <strong>Ingredientes: </strong><?php echo htmlspecialchars($menu["ingredientes"]); ?>
              </p>
              <p class="card-text">
                <strong>Precio: </strong><?php echo htmlspecialchars($menu["precio"]); ?>$
              </p>
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



let nodoActual = arbol;

function responder(opcion) {
    if (!nodoActual.opciones || !nodoActual.opciones[opcion]) {
        document.getElementById("texto").textContent = "Opción no válida.";
        return;
    }

    nodoActual = nodoActual.opciones[opcion];


    if (nodoActual.pregunta) {
        document.getElementById("texto").textContent = nodoActual.pregunta;
        mostrarBotones(Object.keys(nodoActual.opciones));
    } 

    else if (nodoActual.resultado) {
        document.getElementById("texto").textContent = nodoActual.resultado;
        document.getElementById("botones").innerHTML = `
            <button class="btn btn-primary mt-3" onclick="reiniciarArbol()">Volver a empezar </button>
        `;
    }
}

function mostrarBotones(opciones) {
    const contenedor = document.getElementById("botones");
    contenedor.innerHTML = ""; 
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


  <section id="pila" class="ms-4 mt-4">
    <h2 class="text-center mb-3">Pila (Platos Apilados)</h2>
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


  <section id="cola" class="ms-4 mt-4">
    <h2 class="text-center mb-3">Cola (Clientes esperando)</h2>
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


  <section id="grafo" class="card p-4 mb-4">
    <h2 class="text-center mb-3">Rutas del Restaurante</h2>
    <div class="card p-4 text-center">
      <p>Selecciona dos zonas del restaurante para ver la ruta más corta.</p>
      <div class="row justify-content-center mb-3">
        <div class="col-md-4">
          <select id="inicio" class="form-select">
            <option disabled selected>Zona inicial</option>
            <option>Cocina</option><option>Barra</option><option>Mesas</option><option>Bodega</option><option>Caja</option><option>Salida</option>
          </select>
        </div>
        <div class="col-md-4">
          <select id="fin" class="form-select">
            <option disabled selected>Zona final</option>
            <option>Cocina</option><option>Barra</option><option>Mesas</option><option>Bodega</option><option>Caja</option><option>Salida</option>
          </select>
        </div>
      </div>
      <button class="btn btn-primary" onclick="buscarRuta()">Calcular ruta</button>
      <div id="rutaResultado" class="output-box mt-3">Selecciona los puntos para ver el camino.</div>
    </div>
  </section>

  <section id="algoritmos" class="container my-5">
    <h2 class="text-center mb-4">Curiosidades de nuestro restaurante</h2>


    <div class="container mt-4">
        <h4>Vamos a elegir el mejor plato calidad precio de nuestro menu</h4>
        <p>Selecciona siempre la mejor opción local: elegimos el plato con mejor relación calidad/precio.</p>
        <button class="btn btn-primary" onclick="ejecutarVoraz()">Ejecutar Voraz</button>
        <div id="resultadoVoraz" class="mt-3 text-success fw-bold"></div>
    </div>

    <div class="container mt-4">
        <h4>Costo total del menú diario.</h4>
        <p>Recorremos todos los platos para calcular el costo total del menú diario.</p>
        <button class="btn btn-warning" onclick="ejecutarIterativo()">Ejecutar Iterativo</button>
        <div id="resultadoIterativo" class="mt-3 text-success fw-bold"></div>
    </div>

    <div class="container mt-4">
        <h4>Escribe el numero de platos que crees que hay en  <br> el menu y sabras la cantidad de combinaciones posibles</h4>
        <p>Calculamos la cantidad de combinaciones posibles de platos según el número de opciones disponibles.</p>
        <input type="number" id="nPlatos" class="form-control w-25 mx-auto" placeholder="Número de platos" min="1">
        <button class="btn btn-success mt-2" onclick="ejecutarRecursivo()">Calcular combinaciones</button>
        <div id="resultadoRecursivo" class="mt-3 text-success fw-bold"></div>
    </div>
</section>

<script>

function ejecutarVoraz() {
    let platos = [...platosBD];

    if (platos.length === 0) {
        document.getElementById("resultadoVoraz").innerText = "No hay platos en la base de datos.";
        return;
    }

    // Simulación de calidad automática basada en precio (solo para cumplir el algoritmo)
    platos = platos.map(p => {
        let precio = parseInt(p.precio);
        let calidad = 5;

        if (precio >= 25000) calidad = 9;
        else if (precio >= 18000) calidad = 8;
        else if (precio >= 15000) calidad = 7;

        return { ...p, calidad: calidad };
    });

    // Algoritmo voraz real
    let mejor = platos[0];
    let mejorRelacion = mejor.calidad / mejor.precio;

    for (let i = 1; i < platos.length; i++) {
        const r = platos[i].calidad / platos[i].precio;
        if (r > mejorRelacion) {
            mejor = platos[i];
            mejorRelacion = r;
        }
    }

    document.getElementById("resultadoVoraz").innerHTML =
        `🍽️ El mejor plato calidad/precio es <strong>${mejor.nombre}</strong>  
        (Precio: $${mejor.precio}, Calidad: ${mejor.calidad})`;
}

function ejecutarIterativo() {
    if (!platosBD || platosBD.length === 0) {
        document.getElementById("resultadoIterativo").innerText = "No hay platos en el menú.";
        return;
    }

    let total = 0;

    // Recorrido iterativo de los platos de la base de datos
    for (let i = 0; i < platosBD.length; i++) {
        total += parseInt(platosBD[i].precio);
    }

    document.getElementById("resultadoIterativo").innerHTML =
        `💰 El costo total de todos los platos del menú es: <strong>$${total}</strong>`;
}



function factorial(n) {
    if (n <= 1) return 1;
    return n * factorial(n - 1);
}

function ejecutarRecursivo() {
    const n = parseInt(document.getElementById('nPlatos').value);
    if (isNaN(n) || n < 1) {
        document.getElementById('resultadoRecursivo').innerText =
            "⚠️ Ingresa un número válido de platos (mínimo 1)";
        return;
    }

    const combinaciones = factorial(n);
    document.getElementById('resultadoRecursivo').innerText =
        `🍽️ Existen ${combinaciones.toLocaleString()} combinaciones posibles de ${n} platos`;
}
</script>
</div>




  <script>

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

  const grafo = {
  "Cocina": ["Barra", "Bodega"],
  "Barra": ["Cocina", "Mesas", "Caja"],
  "Mesas": ["Barra", "Caja", "Salida"],
  "Bodega": ["Cocina"],
  "Caja": ["Barra", "Mesas", "Salida"],
  "Salida": ["Mesas", "Caja"]
};

function buscarRuta() {
  const inicio = document.getElementById("inicio").value;
  const fin = document.getElementById("fin").value;
  if (!inicio || !fin || inicio === fin) {
    document.getElementById("rutaResultado").innerHTML = "⚠️ Selecciona dos zonas distintas.";
    return;
  }

  const ruta = bfs(grafo, inicio, fin);
  document.getElementById("rutaResultado").innerHTML =
    ruta ? `🚶‍♂️ Ruta más corta: <strong>${ruta.join(" → ")}</strong>` :
    "❌ No existe conexión entre esas zonas.";
}


function bfs(grafo, inicio, fin) {
  const cola = [[inicio]];
  const visitados = new Set([inicio]);
  while (cola.length) {
    const ruta = cola.shift();
    const nodo = ruta[ruta.length - 1];
    if (nodo === fin) return ruta;
    for (const vecino of grafo[nodo] || []) {
      if (!visitados.has(vecino)) {
        visitados.add(vecino);
        cola.push([...ruta, vecino]);
      }
    }
  }
  return null;
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

        <section class="container mt-4">
    <div class="col-md-6">
      <div class="card p-3">
        <h5>Grafos (DFS)</h5>
        <p>Selecciona zonas para ver rutas.</p>
        <div class="row">
          <div class="col-6"><select id="inicioG" class="form-select"><option>Cocina</option><option>Barra</option><option>Mesas</option><option>Bodega</option><option>Caja</option><option>Salida</option></select></div>
          <div class="col-6"><select id="finG" class="form-select"><option>Cocina</option><option>Barra</option><option>Mesas</option><option>Bodega</option><option>Caja</option><option>Salida</option></select></div>
        </div>
        <div class="mt-2">
          <button class="btn btn-secondary" onclick="calcularRutaDFS()">DFS (alguna ruta)</button>
        </div>
        <pre id="salidaGrafo" style="background:#f8f9fa;padding:8px;border-radius:6px;margin-top:10px"></pre>
      </div>
    </div>
    </section>
<br>

        <section class="container mt-4">
        <div class="card p-4 mb-4">
            <h4>Ordenamiento Burbuja</h4>
            <p>Ordena los platos del menú según su precio desde la base de datos.</p>
            <button class="btn btn-info" onclick="ordenarBurbujaBD()">Ordenar con Burbuja</button>
            <div id="resultadoBurbujaBD" class="mt-3 text-primary fw-bold"></div>
        </div>
        </section>

<br>



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


    <script>
        function bfs(g, inicio, fin) {
  const cola = [[inicio]];
  const visit = new Set([inicio]);
  while (cola.length) {
    const ruta = cola.shift();
    const node = ruta[ruta.length-1];
    if (node === fin) return ruta;
    for (const v of (g[node]||[])) {
      if (!visit.has(v)) { visit.add(v); cola.push([...ruta, v]); }
    }
  }
  return null;
}
function dfs(g, inicio, fin) {
  const stack = [[inicio]];
  const visit = new Set();
  while (stack.length) {
    const ruta = stack.pop();
    const node = ruta[ruta.length-1];
    if (node === fin) return ruta;
    if (!visit.has(node)) {
      visit.add(node);
      for (const v of (g[node]||[])) {
        stack.push([...ruta, v]);
      }
    }
  }
  return null;
}
function calcularRutaBFS() {
  const ini = document.getElementById('inicioG').value;
  const fin = document.getElementById('finG').value;
  if (ini === fin) { document.getElementById('salidaGrafo').innerText = 'Elige dos zonas distintas.'; return; }
  const r = bfs(grafo, ini, fin);
  document.getElementById('salidaGrafo').innerText = r? 'Ruta BFS: ' + r.join(' → ') : 'No hay ruta';
}
function calcularRutaDFS() {
  const ini = document.getElementById('inicioG').value;
  const fin = document.getElementById('finG').value;
  const r = dfs(grafo, ini, fin);
  document.getElementById('salidaGrafo').innerText = r? 'Ruta DFS: ' + r.join(' → ') : 'No hay ruta';
}


    </script>

<script>
    // Convertir lista de la base de datos a JavaScript
    let platosBD = <?php echo json_encode($menuLista); ?>;

    function ordenarBurbujaBD() {
    let platos = [...platosBD]; // copiamos para no modificar el original

    for (let i = 0; i < platos.length - 1; i++) {
        for (let j = 0; j < platos.length - 1 - i; j++) {
            if (parseInt(platos[j].precio) > parseInt(platos[j+1].precio)) {
                let temp = platos[j];
                platos[j] = platos[j+1];
                platos[j+1] = temp;
            }
        }
    }

    let salida = "<strong>Platos ordenados por precio (BD):</strong><br><br>";

    platos.forEach(p => {
        salida += `${p.nombre} — $${p.precio}<br>`;
    });

    document.getElementById("resultadoBurbujaBD").innerHTML = salida;
}

</script>

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