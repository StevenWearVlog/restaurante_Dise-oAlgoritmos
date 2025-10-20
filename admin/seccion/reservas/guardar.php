<?php
include("../../bd.php");

if($_POST){
    $nombre = $_POST["nombre_cliente"];
    $telefono = $_POST["telefono"];
    $fecha = $_POST["fecha"];
    $hora = $_POST["hora"];
    $personas = $_POST["numero_personas"];
    $mesa = $_POST["mesa"];

    $sentencia = $conn->prepare("INSERT INTO reservas 
    (nombre_cliente, telefono, fecha, hora, numero_personas, mesa) 
    VALUES (:nombre, :telefono, :fecha, :hora, :personas, :mesa)");

    $sentencia->bindParam(":nombre", $nombre);
    $sentencia->bindParam(":telefono", $telefono);
    $sentencia->bindParam(":fecha", $fecha);
    $sentencia->bindParam(":hora", $hora);
    $sentencia->bindParam(":personas", $personas);
    $sentencia->bindParam(":mesa", $mesa);

    $sentencia->execute();

    header("Location: index.php");
}
?>
