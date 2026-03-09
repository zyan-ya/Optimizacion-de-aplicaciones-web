<?php
include 'db.php';

$search = isset($_GET['search']) ? mysqli_real_escape_string($conexion, $_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'fecha_desc';

// Definir el orden SQL
$order_query = "fecha_pub DESC";
if ($sort == 'fecha_asc') $order_query = "fecha_pub ASC";
if ($sort == 'titulo_asc') $order_query = "titulo ASC";
if ($sort == 'categoria_asc') $order_query = "categoria ASC";

// Consulta con filtro de búsqueda
$sql = "SELECT * FROM noticias 
        WHERE titulo LIKE '%$search%' OR descripcion LIKE '%$search%' 
        ORDER BY $order_query";

$result = mysqli_query($conexion, $sql);

if (mysqli_num_rows($result) > 0) {
    while ($noticia = mysqli_fetch_assoc($result)) {
        ?>
        <div class="col-12 mb-3">
            <div class="card news-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge bg-info text-dark news-category"><?php echo $noticia['categoria']; ?></span>
                        <span class="news-date text-muted"><?php echo date('d/m/Y H:i', strtotime($noticia['fecha_pub'])); ?></span>
                    </div>
                    <h5 class="card-title fw-bold"><?php echo $noticia['titulo']; ?></h5>
                    <p class="card-text text-secondary"><?php echo substr($noticia['descripcion'], 0, 200) . '...'; ?></p>
                    <a href="<?php echo $noticia['url_noticia']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">Leer más</a>
                </div>
            </div>
        </div>
        <?php
    }
} else {
    echo "<div class='col-12 text-center'><p class='text-muted'>No se encontraron noticias.</p></div>";
}
?>