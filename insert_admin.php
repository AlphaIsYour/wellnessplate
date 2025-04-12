<?php
// Koneksi
$conn = new mysqli('localhost:3307', 'root', '', 'wellnessplate');
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Input dari CLI
echo "ID Admin: ";
$id_admin = trim(fgets(STDIN));

echo "Username: ";
$username = trim(fgets(STDIN));

echo "Password: ";
$password = trim(fgets(STDIN));
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

echo "Nama: ";
$nama = trim(fgets(STDIN));

echo "Email: ";
$email = trim(fgets(STDIN));

// SQL Insert
$sql = "INSERT INTO admin (id_admin, username, password, nama, email)
        VALUES (?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $id_admin, $username, $hashed_password, $nama, $email);

if ($stmt->execute()) {
    echo "✅ Data berhasil dimasukkan ke tabel admin!\n";
} else {
    echo "❌ Gagal insert: " . $stmt->error . "\n";
}

$stmt->close();
$conn->close();
