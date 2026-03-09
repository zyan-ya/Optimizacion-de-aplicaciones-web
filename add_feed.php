<?php
require 'db.php';

if (isset($_POST['url']) && !empty($_POST['url'])) {
    $url = mysqli_real_escape_string($conexion, $_POST['url']);

    // Validar que sea una URL real
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        $sql = "INSERT IGNORE INTO feeds (url) VALUES ('$url')";
        if (mysqli_query($conexion, $sql)) {
            echo "Feed guardado exitosamente.";
        } else {
            echo "Error al guardar.";
        }
    } else {
        echo "URL no válida.";
    }
}
?>