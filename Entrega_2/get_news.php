<?php
include 'db.php';

$search = isset($_GET['search']) ? mysqli_real_escape_string($conexion, $_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'fecha_desc';

$order = "fecha_pub DESC";
if ($sort == 'fecha_asc') $order = "fecha_pub ASC";
if ($sort == 'titulo_asc') $order = "titulo ASC";

$sql = "SELECT * FROM noticias 
        WHERE titulo LIKE '%$search%' OR descripcion LIKE '%$search%' 
        ORDER BY $order 
        LIMIT 250";

$result = mysqli_query($conexion, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($noticia = mysqli_fetch_assoc($result)) {
        $foto = (!empty($noticia['imagen_url'])) ? $noticia['imagen_url'] : 'https://picsum.photos/700/500?random=' . rand(1,1000);
        
        echo '<div class="col-12 mb-4">
                <article class="card news-card shadow-sm border-0">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img src="'.$foto.'" alt="Imagen noticia" class="img-fluid rounded-start w-100" 
                                 style="height: 200px; object-fit: cover;" 
                                 onerror="this.src=\'https://via.placeholder.com/400x250?text=Error\'">
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <h5 class="card-title fw-bold">'.htmlspecialchars($noticia['titulo']).'</h5>
                                <p class="card-text text-secondary">'.htmlspecialchars(substr(strip_tags($noticia['descripcion']), 0, 150)).'...</p>
                                <a href="'.$noticia['url_noticia'].'" target="_blank" class="btn btn-primary btn-sm">Leer más</a>
                            </div>
                        </div>
                    </div>
                </article>
              </div>';
    }
} else {
    echo "<div class='text-center w-100 py-5'><h5>No hay noticias.</h5></div>";
}
?>