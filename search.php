<?php
require_once 'config/koneksi.php';

// Redirect if not logged in
if (!isset($_SESSION['id_user'])) {
    header("Location: " . BASE_URL . "/pages/auth/index.php");
    exit();
}

// Debug untuk melihat isi POST/GET
error_reporting(E_ALL);
ini_set('display_errors', 1);

$keyword = isset($_GET['keyword']) ? trim(htmlspecialchars($_GET['keyword'])) : '';
$selected_kondisi = isset($_GET['kondisi']) && is_array($_GET['kondisi']) ? array_map('htmlspecialchars', $_GET['kondisi']) : [];
$selected_jenis = isset($_GET['jenis']) && is_array($_GET['jenis']) ? array_map('htmlspecialchars', $_GET['jenis']) : [];

$page_title = "Pencarian Menu Makanan";
if (!empty($keyword)) {
    $page_title .= " untuk \"$keyword\"";
}
require_once __DIR__ . '/includes/header.php';

// Debug untuk melihat parameter yang diterima
echo "<!-- Debug GET params: " . print_r($_GET, true) . " -->";

// Fungsi untuk membuat slug
function create_slug($string) {
    $string = strtolower(trim($string));
    $string = str_replace(' ', '_', $string);
    $string = preg_replace('/[^a-z0-9_]/', '', $string);
    return $string;
}

// Ambil data kondisi kesehatan dari database
$query_kondisi = "SELECT id_kondisi, nama_kondisi FROM kondisi_kesehatan ORDER BY nama_kondisi ASC";
$result_kondisi = mysqli_query($koneksi, $query_kondisi);
$kondisi_kesehatan_options = [];
while ($row = mysqli_fetch_assoc($result_kondisi)) {
    $slug = create_slug($row['nama_kondisi']);
    $kondisi_kesehatan_options[$slug] = $row['nama_kondisi'];
}

// Ambil semua tags dari database
$query_tags = "SELECT id_tag, nama_tag, slug FROM tags ORDER BY nama_tag ASC";
$result_tags = mysqli_query($koneksi, $query_tags);
$jenis_makanan_options = [];
while ($row = mysqli_fetch_assoc($result_tags)) {
    $jenis_makanan_options[$row['slug']] = $row['nama_tag'];
}

// --- LOGIKA PENCARIAN & FILTER ---
$base_query = "SELECT DISTINCT r.* FROM resep r";

// Jika ada filter kondisi kesehatan, tambahkan join
if (!empty($selected_kondisi)) {
    $base_query .= " INNER JOIN resep_kondisi rk ON r.id_resep = rk.id_resep";
    $base_query .= " INNER JOIN kondisi_kesehatan k ON rk.id_kondisi = k.id_kondisi";
}

// Jika ada filter jenis makanan, tambahkan join dengan resep_tags
if (!empty($selected_jenis)) {
    $base_query .= " INNER JOIN resep_tags rt ON r.id_resep = rt.id_resep";
    $base_query .= " INNER JOIN tags t ON rt.id_tag = t.id_tag";
}

$base_query .= " WHERE 1=1";
$params = [];
$types = "";

// Filter berdasarkan keyword
if (!empty($keyword)) {
    $base_query .= " AND (r.nama_resep LIKE ? OR r.deskripsi LIKE ?)";
    $keyword_param = "%" . $keyword . "%";
    $params[] = $keyword_param;
    $params[] = $keyword_param;
    $types .= "ss";
}

// Filter berdasarkan kondisi kesehatan
if (!empty($selected_kondisi)) {
    $kondisi_conditions = [];
    foreach ($selected_kondisi as $kondisi_slug) {
        $kondisi_conditions[] = "k.slug = ?";
        $params[] = $kondisi_slug;
        $types .= "s";
    }
    if (!empty($kondisi_conditions)) {
        $base_query .= " AND (" . implode(" OR ", $kondisi_conditions) . ")";
    }
}

// Filter berdasarkan jenis makanan (tags)
if (!empty($selected_jenis)) {
    $jenis_conditions = [];
    foreach ($selected_jenis as $jenis) {
        $jenis_conditions[] = "t.slug = ?";
        $params[] = $jenis;
        $types .= "s";
    }
    if (!empty($jenis_conditions)) {
        $base_query .= " AND (" . implode(" OR ", $jenis_conditions) . ")";
    }
}

$base_query .= " ORDER BY r.tanggal_dibuat DESC";

// Debug query
echo "<!-- Debug Query: " . $base_query . " -->";
echo "<!-- Debug Params: " . print_r($params, true) . " -->";
echo "<!-- Debug Selected Kondisi: " . print_r($selected_kondisi, true) . " -->";

// Prepare and execute the statement
$stmt = mysqli_prepare($koneksi, $base_query);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Debug hasil query
echo "<!-- Debug Result Rows: " . mysqli_num_rows($result) . " -->";

$search_results = [];
while ($row = mysqli_fetch_assoc($result)) {
    // Ambil tags untuk resep ini
    $query_tags_resep = "SELECT t.nama_tag, t.slug 
                        FROM tags t 
                        JOIN resep_tags rt ON t.id_tag = rt.id_tag 
                        WHERE rt.id_resep = ?";
    $stmt_tags = mysqli_prepare($koneksi, $query_tags_resep);
    mysqli_stmt_bind_param($stmt_tags, "s", $row['id_resep']);
    mysqli_stmt_execute($stmt_tags);
    $result_tags_resep = mysqli_stmt_get_result($stmt_tags);
    
    $row['tags'] = [];
    while ($tag = mysqli_fetch_assoc($result_tags_resep)) {
        $row['tags'][] = $tag['nama_tag'];
        $row['tag_slugs'][] = $tag['slug'];
    }
    mysqli_stmt_close($stmt_tags);
    
    // Ambil kondisi kesehatan untuk resep ini
    $query_kondisi_resep = "SELECT k.nama_kondisi, k.slug 
                           FROM kondisi_kesehatan k 
                           JOIN resep_kondisi rk ON k.id_kondisi = rk.id_kondisi 
                           WHERE rk.id_resep = ?";
    $stmt_kondisi = mysqli_prepare($koneksi, $query_kondisi_resep);
    mysqli_stmt_bind_param($stmt_kondisi, "s", $row['id_resep']);
    mysqli_stmt_execute($stmt_kondisi);
    $result_kondisi_resep = mysqli_stmt_get_result($stmt_kondisi);
    
    $kondisi_list = [];
    $kondisi_slugs = [];
    while ($kondisi = mysqli_fetch_assoc($result_kondisi_resep)) {
        $kondisi_list[] = $kondisi['nama_kondisi'];
        $kondisi_slugs[] = $kondisi['slug'];
    }
    mysqli_stmt_close($stmt_kondisi);
    
    // Tambahkan kondisi kesehatan ke tags
    $row['tags'] = array_merge($row['tags'], $kondisi_list);
    $row['tag_slugs'] = array_merge($row['tag_slugs'] ?? [], $kondisi_slugs);
    
    $search_results[] = $row;
}

// Filter hasil berdasarkan kondisi kesehatan jika dipilih
if (!empty($selected_kondisi)) {
    $filtered_results = [];
    foreach ($search_results as $result) {
        $match = false;
        foreach ($selected_kondisi as $kondisi) {
            if (in_array($kondisi, $result['tag_slugs'])) {
                $match = true;
                break;
            }
        }
        if ($match) {
            $filtered_results[] = $result;
        }
    }
    $search_results = $filtered_results;
}

// --- PAGINATION ---
$items_per_page = 8;
$total_items = count($search_results);
$total_pages = ceil($total_items / $items_per_page);
$current_page = isset($_GET['page']) ? max(1, min($total_pages, intval($_GET['page']))) : 1;
$offset = ($current_page - 1) * $items_per_page;
$current_items = array_slice($search_results, $offset, $items_per_page);

// Fungsi untuk membangun query string
function build_filter_query_string($exclude_key = null) {
    $params = $_GET;
    unset($params['page']); // Reset pagination when changing filters
    if ($exclude_key) {
        unset($params[$exclude_key]);
    }
    return http_build_query($params);
}
?>

<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/search.css?v=<?= time() ?>">

<div class="search-page-container">
    <?php require_once __DIR__ . '/includes/components/search-bar.php'; ?>

    <div class="search-layout">
        <aside class="filter-sidebar">
            <div class="search-filters">
                <form action="" method="GET" id="filterForm">
                    <!-- Preserve search keyword if exists -->
                    <?php if (!empty($keyword)): ?>
                        <input type="hidden" name="keyword" value="<?php echo htmlspecialchars($keyword); ?>">
                    <?php endif; ?>

                    <h3>Filter Pencarian</h3>
                    
                    <!-- Filter Kondisi Kesehatan -->
                    <div class="filter-section">
                        <h4>Kondisi Kesehatan</h4>
                        <?php foreach ($kondisi_kesehatan_options as $slug => $nama): ?>
                            <div class="filter-item">
                                <label>
                                    <input type="checkbox" name="kondisi[]" value="<?php echo $slug; ?>"
                                           <?php echo in_array($slug, $selected_kondisi) ? 'checked' : ''; ?>>
                                    <?php echo htmlspecialchars($nama); ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Filter Jenis Makanan -->
                    <div class="filter-section">
                        <h4>Jenis Makanan</h4>
                        <?php foreach ($jenis_makanan_options as $slug => $nama): ?>
                            <div class="filter-item">
                                <label>
                                    <input type="checkbox" name="jenis[]" value="<?php echo $slug; ?>"
                                           <?php echo in_array($slug, $selected_jenis) ? 'checked' : ''; ?>>
                                    <?php echo htmlspecialchars($nama); ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Tombol Terapkan Filter -->
                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                        <?php if (!empty($selected_kondisi) || !empty($selected_jenis)): ?>
                            <a href="?<?php echo !empty($keyword) ? 'keyword=' . urlencode($keyword) : ''; ?>" class="btn btn-secondary">Reset Filter</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </aside>

        <main class="search-results-area">
            <h1>
                <?php
                if (!empty($keyword)) {
                    echo "Hasil Pencarian untuk: <em>" . htmlspecialchars($keyword) . "</em>";
                } elseif (!empty($selected_kondisi) || !empty($selected_jenis)) { 
                    echo "Hasil Pencarian Berdasarkan Filter";
                } else {
                    echo "Semua Menu Makanan";
                }
                ?>
            </h1>

            <p class="results-info">Ditemukan <?= $total_items ?> hasil menu makanan.</p>

            <?php
            $active_filters_exist = !empty($selected_kondisi) || !empty($selected_jenis); 
            if ($active_filters_exist):
            ?>
                <div class="selected-filters-container">
                    <span>Filter aktif:</span>
                    <?php foreach ($selected_kondisi as $sk): ?>
                        <span class="filter-tag"><?= htmlspecialchars($kondisi_kesehatan_options[$sk] ?? $sk) ?>
                            <button class="remove-filter-btn" data-filter-type="kondisi" data-filter-value="<?= $sk ?>">×</button>
                        </span>
                    <?php endforeach; ?>

                    <?php foreach ($selected_jenis as $sj): ?>
                        <span class="filter-tag"><?= htmlspecialchars($jenis_makanan_options[$sj] ?? $sj) ?>
                            <button class="remove-filter-btn" data-filter-type="jenis" data-filter-value="<?= $sj ?>">×</button>
                        </span>
                    <?php endforeach; ?>
                    <button id="clearAllFiltersBtn">Clear All Filters</button>
                </div>
            <?php endif; ?>

            <?php if (!empty($current_items)): ?>
                <div class="menu-grid">
                    <?php foreach ($current_items as $menu): ?>
                        <div class="menu-card">
                            <a href="<?= BASE_URL . '/menu/detail.php?id=' . $menu['id_resep'] ?>">
                                <img src="<?= BASE_URL . '/assets/images/menu/' . htmlspecialchars($menu['image']) ?>" alt="<?= htmlspecialchars($menu['nama_resep']) ?>">
                            </a>
                            <div class="menu-card-content">
                                <div class="menu-info">
                                    <h3>
                                        <a href="<?= BASE_URL . '/menu/detail.php?id=' . $menu['id_resep'] ?>" title="<?= htmlspecialchars($menu['nama_resep']) ?>">
                                            <?= htmlspecialchars($menu['nama_resep']) ?>
                                        </a>
                                    </h3>
                                    <?php if (!empty($menu['tags'])): ?>
                                    <div class="menu-tags">
                                        <?php 
                                        $tag_limit = 2;
                                        $displayed_tags = 0;
                                        foreach ($menu['tags'] as $tag_text): 
                                            if ($displayed_tags >= $tag_limit) break;
                                        ?>
                                            <span class="tag"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $tag_text))) ?></span>
                                        <?php 
                                            $displayed_tags++;
                                        endforeach; 

                                        $relevant_tags_count = 0;
                                        foreach($menu['tags'] as $t){
                                            if (!(in_array($t, $selected_kondisi) || in_array($t, $selected_jenis))) {
                                                 $relevant_tags_count++;
                                            }
                                        }
                                        if ($relevant_tags_count > $tag_limit) echo '<span class="tag">...</span>';
                                        ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <a href="<?= BASE_URL . '/menu/detail.php?id=' . $menu['id_resep'] ?>" class="btn-details">Lihat Detail</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($total_pages > 1): ?>
                    <nav class="pagination">
                        <?php
                        if ($current_page > 1) {
                            echo '<a href="?page=' . ($current_page - 1) . '&' . build_filter_query_string('page') . '">« Prev</a>';
                        } else {
                            echo '<span class="disabled">« Prev</span>';
                        }

                        $num_links = 2; 
                        if ($current_page > ($num_links + 1) ) {
                            echo '<a href="?page=1&' . build_filter_query_string('page') . '">1</a>';
                            if ($current_page > ($num_links + 2) ) {
                                echo '<span class="disabled">...</span>';
                            }
                        }
                        for ($i = max(1, $current_page - $num_links); $i <= min($total_pages, $current_page + $num_links); $i++) {
                            if ($i == $current_page) {
                                echo '<span class="current-page">' . $i . '</span>';
                            } else {
                                echo '<a href="?page=' . $i . '&' . build_filter_query_string('page') . '">' . $i . '</a>';
                            }
                        }
                        if ($current_page < ($total_pages - $num_links) ) {
                            if ($current_page < ($total_pages - $num_links - 1) ) {
                                 echo '<span class="disabled">...</span>';
                            }
                            echo '<a href="?page=' . $total_pages . '&' . build_filter_query_string('page') . '">' . $total_pages . '</a>';
                        }

                        if ($current_page < $total_pages) {
                            echo '<a href="?page=' . ($current_page + 1) . '&' . build_filter_query_string('page') . '">Next »</a>';
                        } else {
                            echo '<span class="disabled">Next »</span>';
                        }
                        ?>
                    </nav>
                <?php endif; ?>

            <?php else: ?>
                <p class="no-results">
                    <?php if (!empty($keyword) || !empty($selected_kondisi) || !empty($selected_jenis)): ?>
                        Maaf, tidak ada menu makanan yang cocok dengan kata kunci dan filter yang dipilih.
                    <?php else: ?>
                        Silakan masukkan kata kunci atau pilih filter untuk mencari menu.
                    <?php endif; ?>
                </p>
            <?php endif; ?>
        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle filter removal buttons
    document.querySelectorAll('.remove-filter-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const filterType = this.dataset.filterType;
            const filterValue = this.dataset.filterValue;
            
            // Uncheck the corresponding checkbox
            const checkbox = document.querySelector(`input[type="checkbox"][name="${filterType}[]"][value="${filterValue}"]`);
            if (checkbox) {
                checkbox.checked = false;
            }
            
            // Submit the form
            document.getElementById('filterForm').submit();
        });
    });

    // Handle clear all filters button
    const clearAllBtn = document.getElementById('clearAllFiltersBtn');
    if (clearAllBtn) {
        clearAllBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Uncheck all checkboxes
            document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                checkbox.checked = false;
            });
            
            // Get current URL
            const url = new URL(window.location.href);
            
            // Keep only the keyword parameter if it exists
            const keyword = url.searchParams.get('keyword');
            url.search = keyword ? `?keyword=${encodeURIComponent(keyword)}` : '';
            
            // Redirect to the filtered URL
            window.location.href = url.toString();
        });
    }
});
</script>

<style>
/* Enhanced Search Page Styles */
:root {
    --primary-green: #28a745;
    --light-green: #e8f5e8;
    --medium-green: #d4edda;
    --dark-green: #155724;
    --border-green: #c3e6cb;
    --hover-green: #b8dcc8;
}

.search-page-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

.search-layout {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 30px;
    margin-top: 20px;
}

/* Filter Sidebar Styling */
.filter-sidebar {
    background: var(--light-green);
    border-radius: 12px;
    border: 2px solid var(--border-green);
    height: fit-content;
    position: sticky;
    top: 20px;
}

.search-filters {
    padding: 0;
}

.search-filters h3 {
    background: var(--primary-green);
    color: white;
    margin: 0;
    padding: 16px 20px;
    border-radius: 10px 10px 0 0;
    font-size: 18px;
    font-weight: 600;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.filter-section {
    margin: 0;
    padding: 20px;
    border-bottom: 1px solid var(--border-green);
}

.filter-section:last-of-type {
    border-bottom: none;
}

.filter-section h4 {
    color: var(--dark-green);
    font-size: 16px;
    font-weight: 600;
    margin: 0 0 15px 0;
    padding-bottom: 8px;
    border-bottom: 2px solid var(--medium-green);
}

/* Add max-height and scrolling for jenis makanan filter section */
.filter-section:nth-of-type(2) {
    max-height: 300px;
    overflow-y: auto;
}

/* Enhanced Filter Items */
.filter-item {
    margin: 10px 0;
}

.filter-item label {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    background: white;
    border: 2px solid var(--border-green);
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 500;
    color: var(--dark-green);
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.filter-item label:hover {
    background: var(--hover-green);
    border-color: var(--primary-green);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.filter-item input[type="checkbox"] {
    margin-right: 12px;
    width: 18px;
    height: 18px;
    accent-color: var(--primary-green);
    cursor: pointer;
}

.filter-item input[type="checkbox"]:checked + span {
    font-weight: 600;
    color: var(--dark-green);
}

.filter-item label:has(input[type="checkbox"]:checked) {
    background: var(--medium-green);
    border-color: var(--primary-green);
    box-shadow: 0 4px 8px rgba(40, 167, 69, 0.2);
}

/* Filter Actions */
.filter-actions {
    margin: 0;
    padding: 20px;
    border-top: 2px solid var(--border-green);
    background: var(--medium-green);
    border-radius: 0 0 10px 10px;
    text-align: center;
}

.filter-actions .btn {
    display: inline-block;
    padding: 12px 20px;
    margin: 5px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.btn-primary {
    background: var(--primary-green);
    color: white;
}

.btn-primary:hover {
    background: #218838;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #5a6268;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

/* Search Results Area */
.search-results-area {
    background: white;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
}

.search-results-area h1 {
    color: var(--dark-green);
    margin-bottom: 10px;
}

.results-info {
    color: #666;
    margin-bottom: 20px;
    font-style: italic;
}

/* Selected Filters */
.selected-filters-container {
    background: var(--light-green);
    padding: 15px;
    border-radius: 8px;
    border: 1px solid var(--border-green);
    margin-bottom: 25px;
}

.selected-filters-container span:first-child {
    font-weight: 600;
    color: var(--dark-green);
    margin-right: 10px;
}

.filter-tag {
    display: inline-block;
    background: var(--primary-green);
    color: white;
    padding: 6px 12px;
    margin: 4px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 500;
}

.filter-tag .remove-filter-btn {
    background: none;
    border: none;
    color: white;
    margin-left: 8px;
    cursor: pointer;
    font-weight: bold;
    padding: 0;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    transition: background 0.2s;
}

.filter-tag .remove-filter-btn:hover {
    background: rgba(255,255,255,0.2);
}

#clearAllFiltersBtn {
    background: #dc3545;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    cursor: pointer;
    margin-left: 10px;
    font-weight: 500;
    transition: background 0.2s;
}

#clearAllFiltersBtn:hover {
    background: #c82333;
}

/* Menu Grid */
.menu-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 25px;
    margin-bottom: 30px;
}

.menu-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    width: 100%;
}

.menu-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.menu-card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.menu-card-content {
    padding: 20px;
}

.menu-info h3 {
    margin: 0 0 10px 0;
    color: var(--dark-green);
}

.menu-info h3 a {
    text-decoration: none;
    color: inherit;
}

.menu-info h3 a:hover {
    color: var(--primary-green);
}

.menu-tags {
    margin: 10px 0;
}

.menu-tags .tag {
    display: inline-block;
    background: var(--light-green);
    color: var(--dark-green);
    padding: 4px 8px;
    margin: 2px;
    border-radius: 12px;
    font-size: 12px;
    border: 1px solid var(--border-green);
}

.btn-details {
    display: inline-block;
    background: var(--primary-green);
    color: white;
    padding: 10px 20px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 500;
    margin-top: 10px;
    transition: background 0.2s;
}

.btn-details:hover {
    background: #218838;
}

/* Pagination */
.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    margin-top: 30px;
}

.pagination a, .pagination span {
    padding: 10px 15px;
    border-radius: 6px;
    text-decoration: none;
    transition: all 0.2s;
}

.pagination a {
    background: var(--light-green);
    color: var(--dark-green);
    border: 1px solid var(--border-green);
}

.pagination a:hover {
    background: var(--primary-green);
    color: white;
}

.pagination .current-page {
    background: var(--primary-green);
    color: white;
    font-weight: 600;
}

.pagination .disabled {
    background: #f8f9fa;
    color: #6c757d;
    border: 1px solid #dee2e6;
}

/* No Results */
.no-results {
    text-align: center;
    color: #666;
    font-style: italic;
    margin: 40px 0;
    padding: 40px;
    background: var(--light-green);
    border-radius: 12px;
    border: 2px dashed var(--border-green);
}

/* Responsive Design */
@media (max-width: 768px) {
    .search-layout {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .filter-sidebar {
        position: static;
    }
    
    .menu-grid {
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }
    
    .search-page-container {
        padding: 15px;
    }
}

@media (max-width: 480px) {
    .menu-grid {
        grid-template-columns: 1fr;
    }
    
    .filter-actions .btn {
        display: block;
        margin: 5px 0;
        text-align: center;
    }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>