<?php
include 'db.php';

// Capturar búsqueda y orden
$search = isset($_GET['search']) ? mysqli_real_escape_string($conexion, $_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'fecha_desc';

$order = "fecha_pub DESC";
if ($sort == 'fecha_asc') $order = "fecha_pub ASC";
if ($sort == 'titulo_asc') $order = "titulo ASC";

// Consulta simple
$sql = "SELECT * FROM noticias 
        WHERE titulo LIKE '%$search%' OR descripcion LIKE '%$search%' 
        ORDER BY $order";

$result = mysqli_query($conexion, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($noticia = mysqli_fetch_assoc($result)) {
        // Si la columna imagen_url existe pero está vacía, usamos el placeholder
        $foto = (!empty($noticia['imagen_url'])) ? $noticia['imagen_url'] : 'https://via.placeholder.com/400x250?text=Noticia';
        
        echo '<div class="col-12 mb-4">
                <div class="card news-card shadow-sm border-0">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img src="'.$foto.'" class="img-fluid rounded-start h-100 w-100" style="object-fit: cover; min-height: 180px;">
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <h5 class="card-title fw-bold">'.$noticia['titulo'].'</h5>
                                <p class="card-text text-secondary">'.substr(strip_tags($noticia['descripcion']), 0, 150).'...</p>
                                <a href="'.$noticia['url_noticia'].'" target="_blank" class="btn btn-primary btn-sm">Leer más</a>
                            </div>
                        </div>
                    </div>
                </div>
              </div>';
    }
} else {
    echo "<div class='text-center w-100 py-5'><h5>No hay noticias. Haz clic en 'Actualizar'.</h5></div>";
}
?>
