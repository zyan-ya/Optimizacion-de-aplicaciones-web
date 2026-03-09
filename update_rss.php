<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'db.php';

// Limpiamos noticias viejas para que no se mezclen
mysqli_query($conexion, "DELETE FROM noticias");

$sql = "SELECT id, url FROM feeds";
$result = mysqli_query($conexion, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    $feed_id = $row['id'];
    $url = trim($row['url']);

    // Configuración para que no se bloquee por tiempo
    $opts = [
        "http" => [
            "method" => "GET",
            "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36\r\n"
        ]
    ];
    $context = stream_context_create($opts);

    // Intentamos bajar el archivo
    $xml_data = @file_get_contents($url, false, $context);
    
    if ($xml_data === false) {
        continue; // Si este link falla, saltamos al siguiente
    }

    // Cargamos el XML ignorando errores internos
    $rss = @simplexml_load_string($xml_data, 'SimpleXMLElement', LIBXML_NOCDATA);
    
    if (!$rss || !isset($rss->channel->item)) {
        continue; // Si el XML está mal formado, saltamos
    }

    foreach ($rss->channel->item as $item) {
        $titulo = mysqli_real_escape_string($conexion, (string)$item->title);
        $link = mysqli_real_escape_string($conexion, (string)$item->link);
        $descripcion = mysqli_real_escape_string($conexion, strip_tags((string)$item->description));
        $fecha_pub = date('Y-m-d H:i:s', strtotime((string)$item->pubDate));

        // BUSCADOR DE IMAGEN MULTI-ETIQUETA
        $imagen_url = "";

        // 1. Intentar con enclosure (National Geographic usa esto)
        if (isset($item->enclosure)) {
            $imagen_url = (string)$item->enclosure['url'];
        } 
        
        // 2. Intentar con media:content (Yahoo Media)
        if (empty($imagen_url)) {
            $media = $item->children('http://search.yahoo.com/mrss/');
            if (isset($media->content)) {
                $imagen_url = (string)$media->content->attributes()->url;
            }
        }

        // 3. IMAGEN DE RELLENO (Si no hay foto, ponemos una de internet para que se vea Pro)
        if (empty($imagen_url)) {
            $imagen_url = "https://picsum.photos/400/300?random=" . rand(1, 1000);
        }

        $query = "INSERT INTO noticias (titulo, descripcion, url_noticia, imagen_url, fecha_pub, feed_id) 
                  VALUES ('$titulo', '$descripcion', '$link', '$imagen_url', '$fecha_pub', $feed_id)";
        
        mysqli_query($conexion, $query);
    }
}

echo "<h1>Actualización exitosa</h1><p>Se han procesado los feeds correctamente.</p>";
?>