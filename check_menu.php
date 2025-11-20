<?php
require_once 'config.php';
require_once 'functions.php';

echo "<h2>Daftar Menu di Database</h2>";
echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
echo "<tr style='background: #333; color: white;'>";
echo "<th>ID</th><th>Nama Menu</th><th>Kategori</th><th>Nama File Gambar</th><th>Status Gambar</th>";
echo "</tr>";

$menu_items = get_menu_items();

foreach ($menu_items as $item) {
    $image_filename = strtolower($item['name']) . '.jpg';
    $image_path = 'img/' . $image_filename;
    $image_exists = file_exists($image_path);
    
    echo "<tr>";
    echo "<td>" . $item['id'] . "</td>";
    echo "<td><strong>" . htmlspecialchars($item['name']) . "</strong></td>";
    echo "<td>" . htmlspecialchars($item['category']) . "</td>";
    echo "<td><code>" . $image_filename . "</code></td>";
    echo "<td style='color: " . ($image_exists ? 'green' : 'red') . ";'>";
    echo $image_exists ? "✅ Ada" : "❌ Tidak Ada";
    echo "</td>";
    echo "</tr>";
}

echo "</table>";

echo "<h3>File Gambar di Folder img/</h3>";
echo "<ul>";
$files = scandir('img/');
foreach ($files as $file) {
    if ($file != '.' && $file != '..') {
        echo "<li>" . $file . "</li>";
    }
}
echo "</ul>";
?>
