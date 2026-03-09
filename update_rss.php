<?php
require 'db.php';

// 1. Obtener las URLs de la base de datos
$sql = "SELECT id, url FROM feeds";
$result = mysqli_query($conexion, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    $feed_id = $row['id'];
    $url = $row['url'];

    // 2. Cargar el XML del RSS (Nativo)
    // Usamos @ para ignorar errores si la URL está caída
    $rss = @simplexml_load_file($url);

    if ($rss) {
        // En RSS 2.0, las noticias están en <channel><item>
        foreach ($rss->channel->item as $item) {

            $titulo = mysqli_real_escape_string($conexion, (string)$item->title);
            $link = mysqli_real_escape_string($conexion, (string)$item->link);
            $descripcion = mysqli_real_escape_string($conexion, strip_tags((string)$item->description));

            // Convertir fecha a formato MySQL (YYYY-MM-DD HH:MM:SS)
            $fecha_raw = (string)$item->pubDate;
            $fecha_formateada = date('Y-m-d H:i:s', strtotime($fecha_raw));

            // Categoría (algunos feeds no la traen, ponemos 'General' por defecto)
            $categoria = isset($item->category) ? mysqli_real_escape_string($conexion, (string)$item->category) : 'General';

            // 3. Insertar en la DB
            $insert = "INSERT IGNORE INTO noticias (titulo, descripcion, url_noticia, fecha_pub, categoria, feed_id) 
                       VALUES ('$titulo', '$descripcion', '$link', '$fecha_formateada', '$categoria', $feed_id)";

            mysqli_query($conexion, $insert);
        }
    }
}

echo "Actualización nativa completada";
?>