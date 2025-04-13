<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$conn = new mysqli('localhost:3307', 'root', '', 'wellnessplate');
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil informasi admin yang login
$admin_id = $_SESSION['admin_id'];
$sql = "SELECT nama FROM admin WHERE id_admin = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$admin_name = $admin['nama'];

// Fungsi untuk menghitung jumlah record di tabel
function get_record_count($conn, $table) {
    $sql = "SELECT COUNT(*) as count FROM $table";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['count'];
}

// Data untuk grafik
$counts = [
    'Kondisi Kesehatan' => get_record_count($conn, 'kondisi_kesehatan'),
    'Resep' => get_record_count($conn, 'resep'),
    'Bahan' => get_record_count($conn, 'bahan'),
    'Gizi' => get_record_count($conn, 'gizi'),
    'Resep Bahan' => get_record_count($conn, 'resep_bahan'),
    'Users' => get_record_count($conn, 'users'),
    'Admins' => get_record_count($conn, 'admin'),
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - WellnessPlate</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <script src="https://unpkg.com/feather-icons"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h3>WellnessPlate</h3>
            <button class="toggle-sidebar" id="toggle-sidebar"><i data-feather="menu"></i></button>
        </div>
        <ul>
            <li><a href="dashboard.php" aria-label="Dashboard" class="active"><i data-feather="home"></i><span>Dashboard</span></a></li>
            <li><a href="manage_kondisi.php" aria-label="Kelola Kondisi Kesehatan"><i data-feather="heart"></i><span>Kondisi Kesehatan</span></a></li>
            <li><a href="manage_resep.php" aria-label="Kelola Resep"><i data-feather="book"></i><span>Resep</span></a></li>
            <li><a href="manage_bahan.php" aria-label="Kelola Bahan"><i data-feather="shopping-bag"></i><span>Bahan</span></a></li>
            <li><a href="manage_gizi.php" aria-label="Kelola Gizi"><i data-feather="bar-chart-2"></i><span>Gizi</span></a></li>
            <li><a href="manage_resep_bahan.php" aria-label="Kelola Resep Bahan"><i data-feather="link"></i><span>Resep Bahan</span></a></li>
            <li><a href="manage_users.php" aria-label="Kelola Users"><i data-feather="users"></i><span>Users</span></a></li>
            <li><a href="manage_admins.php" aria-label="Kelola Admins"><i data-feather="user-check"></i><span>Admins</span></a></li>
        </ul>
    </div>
    <div class="main">
        <div class="header">
            <div class="logo">
                <span class="logo-text">WellnessPlate Admin</span>
            </div>
            <div class="admin-info">
                <span class="admin-name"><?php echo htmlspecialchars($admin_name); ?></span>
                <div class="avatar">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($admin_name); ?>&background=8b5cf6&color=fff" alt="Avatar">
                    <div class="dropdown-content">
                        <a href="edit_profile.php">Edit Profil</a>
                        <a href="logout.php">Logout</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <h2>Dashboard Overview</h2>
            <p>Monitor dan kelola data WellnessPlate dengan mudah.</p>
            <div class="card-container">
                <div class="card" onclick="window.location.href='manage_kondisi.php'" data-tooltip="Kelola data kondisi kesehatan">
                    <i data-feather="heart" class="card-icon"></i>
                    <h4>Kondisi Kesehatan</h4>
                    <p class="card-value"><?php echo $counts['Kondisi Kesehatan']; ?></p>
                </div>
                <div class="card" onclick="window.location.href='manage_resep.php'" data-tooltip="Kelola resep makanan">
                    <i data-feather="book" class="card-icon"></i>
                    <h4>Resep</h4>
                    <p class="card-value"><?php echo $counts['Resep']; ?></p>
                </div>
                <div class="card" onclick="window.location.href='manage_bahan.php'" data-tooltip="Kelola bahan makanan">
                    <i data-feather="shopping-bag" class="card-icon"></i>
                    <h4>Bahan</h4>
                    <p class="card-value"><?php echo $counts['Bahan']; ?></p>
                </div>
                <div class="card" onclick="window.location.href='manage_gizi.php'" data-tooltip="Kelola informasi gizi">
                    <i data-feather="bar-chart-2" class="card-icon"></i>
                    <h4>Gizi</h4>
                    <p class="card-value"><?php echo $counts['Gizi']; ?></p>
                </div>
                <div class="card" onclick="window.location.href='manage_resep_bahan.php'" data-tooltip="Kelola hubungan resep dan bahan">
                    <i data-feather="link" class="card-icon"></i>
                    <h4>Resep Bahan</h4>
                    <p class="card-value"><?php echo $counts['Resep Bahan']; ?></p>
                </div>
                <div class="card" onclick="window.location.href='manage_users.php'" data-tooltip="Kelola akun pengguna">
                    <i data-feather="users" class="card-icon"></i>
                    <h4>Users</h4>
                    <p class="card-value"><?php echo $counts['Users']; ?></p>
                </div>
                <div class="card" onclick="window.location.href='manage_admins.php'" data-tooltip="Kelola akun admin">
                    <i data-feather="user-check" class="card-icon"></i>
                    <h4>Admins</h4>
                    <p class="card-value"><?php echo $counts['Admins']; ?></p>
                </div>
            </div>
            <div class="chart-container">
                <h3>Data Analytics</h3>
                <div class="chart-controls">
                    <button id="barChartBtn" class="chart-btn active">Bar</button>
                    <button id="doughnutChartBtn" class="chart-btn">Doughnut</button>
                </div>
                <canvas id="dataChart"></canvas>
            </div>
        </div>
    </div>
    <script src="scripts.js"></script>
    <script>
        feather.replace(); // Reinitialize icons
        const dataChartCanvas = document.getElementById('dataChart');
        let chartInstance = null;

        function renderChart(type) {
            if (chartInstance) chartInstance.destroy();
            const ctx = dataChartCanvas.getContext('2d');
            chartInstance = new Chart(ctx, {
                type: type,
                data: {
                    labels: [
                        'Kondisi',
                        'Resep',
                        'Bahan',
                        'Gizi',
                        'Resep Bahan',
                        'Users',
                        'Admins'
                    ],
                    datasets: [{
                        label: 'Jumlah Data',
                        data: [
                            <?php echo $counts['Kondisi Kesehatan']; ?>,
                            <?php echo $counts['Resep']; ?>,
                            <?php echo $counts['Bahan']; ?>,
                            <?php echo $counts['Gizi']; ?>,
                            <?php echo $counts['Resep Bahan']; ?>,
                            <?php echo $counts['Users']; ?>,
                            <?php echo $counts['Admins']; ?>
                        ],
                        backgroundColor: type === 'bar' ? 'rgba(139, 92, 246, 0.5)' : [
                            '#8b5cf6',
                            '#a78bfa',
                            '#c4b5fd',
                            '#d8b4fe',
                            '#e9d5ff',
                            '#f3e8ff',
                            '#faf5ff'
                        ],
                        borderColor: type === 'bar' ? '#8b5cf6' : '#ffffff',
                        borderWidth: 1
                    }]
                },
                options: {
                    animation: {
                        duration: 1000,
                        easing: 'easeOutQuart'
                    },
                    scales: type === 'bar' ? {
                        y: {
                            beginAtZero: true,
                            ticks: { color: '#e0e0e0', font: { size: 12 } },
                            grid: { color: '#2a3f5f' }
                        },
                        x: {
                            ticks: { color: '#e0e0e0', font: { size: 12 } },
                            grid: { display: false }
                        }
                    } : {},
                    plugins: {
                        legend: { labels: { color: '#e0e0e0', font: { size: 14 } } },
                        tooltip: { backgroundColor: '#1a2a44', titleFont: { size: 14 }, bodyFont: { size: 12 } }
                    }
                }
            });
        }

        // Initial render
        renderChart('bar');

        // Chart type switch
        document.getElementById('barChartBtn').addEventListener('click', () => {
            renderChart('bar');
            document.getElementById('barChartBtn').classList.add('active');
            document.getElementById('doughnutChartBtn').classList.remove('active');
        });

        document.getElementById('doughnutChartBtn').addEventListener('click', () => {
            renderChart('doughnut');
            document.getElementById('doughnutChartBtn').classList.add('active');
            document.getElementById('barChartBtn').classList.remove('active');
        });
    </script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>