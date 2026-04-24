<?php
include 'db.php';

$sql = "SELECT * FROM feeds";
$result = mysqli_query($conexion, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    echo "<li class='list-group-item'>".$row['url']."</li>";
}
?>