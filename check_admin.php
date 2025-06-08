<?php
require_once 'config/koneksi.php';

// Get table structure
echo "Admin Table Structure:\n";
$result = mysqli_query($koneksi, "DESCRIBE admin");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        print_r($row);
        echo "\n";
    }
} else {
    echo "Error getting table structure: " . mysqli_error($koneksi) . "\n";
}

// Get sample data
echo "\nAdmin Table Data:\n";
$result = mysqli_query($koneksi, "SELECT * FROM admin LIMIT 5");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        print_r($row);
        echo "\n";
    }
} else {
    echo "Error getting data: " . mysqli_error($koneksi) . "\n";
}
?> 