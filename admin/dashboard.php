<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    $base_url = "/wellnessplate"; 
    header("Location: " . $base_url . "/admin/index.php?error=Silakan login terlebih dahulu coy.");
    exit;
}

if (!isset($base_url)) {
    $base_url = "/wellnessplate"; 
}

require_once __DIR__ . '/../config/koneksi.php';

// Get statistics
$stats = [];

// Total recipes
$query_resep = "SELECT COUNT(*) as total FROM resep";
$result_resep = mysqli_query($koneksi, $query_resep);
$stats['total_resep'] = mysqli_fetch_assoc($result_resep)['total'];

// Total health conditions
$query_kondisi = "SELECT COUNT(*) as total FROM kondisi_kesehatan";
$result_kondisi = mysqli_query($koneksi, $query_kondisi);
$stats['total_kondisi'] = mysqli_fetch_assoc($result_kondisi)['total'];

// Total tags
$query_tags = "SELECT COUNT(*) as total FROM tags";
$result_tags = mysqli_query($koneksi, $query_tags);
$stats['total_tags'] = mysqli_fetch_assoc($result_tags)['total'];

// Total ingredients
$query_bahan = "SELECT COUNT(*) as total FROM bahan";
$result_bahan = mysqli_query($koneksi, $query_bahan);
$stats['total_bahan'] = mysqli_fetch_assoc($result_bahan)['total'];

// Get recipes per health condition
$query_resep_per_kondisi = "SELECT k.nama_kondisi, COUNT(r.id_resep) as total 
                           FROM kondisi_kesehatan k 
                           LEFT JOIN resep r ON k.id_kondisi = r.id_kondisi 
                           GROUP BY k.id_kondisi, k.nama_kondisi";
$result_resep_per_kondisi = mysqli_query($koneksi, $query_resep_per_kondisi);
$resep_per_kondisi = [];
while ($row = mysqli_fetch_assoc($result_resep_per_kondisi)) {
    $resep_per_kondisi['labels'][] = $row['nama_kondisi'];
    $resep_per_kondisi['data'][] = $row['total'];
}

// Get recent recipes
$query_recent_resep = "SELECT r.nama_resep, r.tanggal_dibuat, k.nama_kondisi 
                       FROM resep r 
                       LEFT JOIN kondisi_kesehatan k ON r.id_kondisi = k.id_kondisi 
                       ORDER BY r.tanggal_dibuat DESC LIMIT 5";
$result_recent_resep = mysqli_query($koneksi, $query_recent_resep);

$page_title = isset($page_title) ? $page_title : 'Admin WellnessPlate';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link rel="stylesheet" href="./style.css">
    <link rel="stylesheet" href="./layout.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-card h3 {
            color: #666;
            margin-bottom: 10px;
        }
        .stat-card .number {
            font-size: 2em;
            font-weight: bold;
            color: #4CAF50;
        }
        .charts-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .chart-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .recent-recipes {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .recent-recipes table {
            width: 100%;
            border-collapse: collapse;
        }
        .recent-recipes th, .recent-recipes td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .recent-recipes th {
            background-color: #f5f5f5;
        }
    </style>
</head>
<body class="dashboard-body">
    <header class="page-header">
        <div class="logo-area">
            <h2><a href="<?php echo $base_url; ?>/dashboard.php" style="color: inherit; text-decoration: none;">WellnessPlate Admin</a></h1>
        </div>
        <div class="admin-info">
            <span class="welcome-admin" style="margin-right: 10px;">Selamat datang, <?php echo htmlspecialchars($_SESSION['admin_nama']); ?>!</span>
            <a href="/logout.php" class="logout-btn">Logout</a>
        </div>
    </header>
    <div class="main-wrapper">
        <?php include_once __DIR__ . '/templates/sidebar.php'; ?>
        <main class="content-area">
            <div class="card">
                <div class="card-header">
                    <h2>Dashboard WellnessPlate</h2>
                </div>
                <div class="card-body">
                    <!-- Statistik -->
                    <div class="stats-container">
                        <div class="stat-card">
                            <h3>Total Resep</h3>
                            <div class="number"><?php echo $stats['total_resep']; ?></div>
                        </div>
                        <div class="stat-card">
                            <h3>Kondisi Kesehatan</h3>
                            <div class="number"><?php echo $stats['total_kondisi']; ?></div>
                        </div>
                        <div class="stat-card">
                            <h3>Total Tags</h3>
                            <div class="number"><?php echo $stats['total_tags']; ?></div>
                        </div>
                        <div class="stat-card">
                            <h3>Total Bahan</h3>
                            <div class="number"><?php echo $stats['total_bahan']; ?></div>
                        </div>
                    </div>

                    <!-- Grafik -->
                    <div class="charts-container">
                        <div class="chart-card">
                            <h3>Resep per Kondisi Kesehatan</h3>
                            <canvas id="resepPerKondisiChart"></canvas>
                        </div>
                        <div class="chart-card">
                            <h3>Statistik Bulanan</h3>
                            <canvas id="monthlyStatsChart"></canvas>
                        </div>
                    </div>

                    <!-- Resep Terbaru -->
                    <div class="recent-recipes">
                        <h3>Resep Terbaru</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Nama Resep</th>
                                    <th>Kondisi Kesehatan</th>
                                    <th>Tanggal Dibuat</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result_recent_resep)) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['nama_resep']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nama_kondisi']); ?></td>
                                    <td><?php echo date('d M Y', strtotime($row['tanggal_dibuat'])); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main> 
    </div> 
    <footer>
        <div style="background-color:rgb(98, 98, 98);">
            <p style="text-align: right; margin-right: 10px; color: #fff;">© <?php echo date("Y"); ?> WellnessPlate Admin. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Chart untuk Resep per Kondisi Kesehatan
        const resepPerKondisiCtx = document.getElementById('resepPerKondisiChart').getContext('2d');
        new Chart(resepPerKondisiCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($resep_per_kondisi['labels'] ?? []); ?>,
                datasets: [{
                    label: 'Jumlah Resep',
                    data: <?php echo json_encode($resep_per_kondisi['data'] ?? []); ?>,
                    backgroundColor: 'rgba(76, 175, 80, 0.5)',
                    borderColor: 'rgba(76, 175, 80, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Chart untuk Statistik Bulanan (Dummy data untuk contoh)
        const monthlyStatsCtx = document.getElementById('monthlyStatsChart').getContext('2d');
        new Chart(monthlyStatsCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                datasets: [{
                    label: 'Resep Ditambahkan',
                    data: [5, 8, 12, 7, 15, 10],
                    fill: false,
                    borderColor: 'rgb(76, 175, 80)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>