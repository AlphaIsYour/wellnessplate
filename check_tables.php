<?php
require_once 'config/koneksi.php';

function showTableStructure($koneksi, $tableName) {
    echo "\nTable: $tableName\n";
    $query = "SHOW CREATE TABLE $tableName";
    $result = mysqli_query($koneksi, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        echo $row['Create Table'] . "\n";
    } else {
        echo "Error getting table structure: " . mysqli_error($koneksi) . "\n";
    }
}

showTableStructure($koneksi, 'resep');
showTableStructure($koneksi, 'admin');
showTableStructure($koneksi, 'users');
?> 