<?php
require_once 'config/koneksi.php';

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

// Ambil tags makanan yang unik dari tabel resep
$query_tags = "SELECT DISTINCT JSON_UNQUOTE(JSON_EXTRACT(tags, '$[*]')) as tag 
               FROM resep 
               WHERE JSON_EXTRACT(tags, '$[*]') IS NOT NULL 
               AND JSON_EXTRACT(tags, '$[*]') != ''";
$result_tags = mysqli_query($koneksi, $query_tags);
$all_tags = [];
while ($row = mysqli_fetch_assoc($result_tags)) {
    if (!empty($row['tag'])) {
        $tag = trim($row['tag'], '"'); // Hapus tanda kutip jika ada
        if (!in_array($tag, array_keys($kondisi_kesehatan_options))) {
            $all_tags[] = $tag;
        }
    }
}

// Filter dan kelompokkan tags untuk jenis makanan
$jenis_makanan_options = [];
$common_food_categories = [
    'mie' => ['mie', 'noodle', 'pasta'],
    'nasi' => ['nasi', 'rice', 'karbohidrat'],
    'sayuran' => ['sayur', 'vegetable', 'sayuran'],
    'daging' => ['daging', 'meat', 'ayam', 'chicken', 'sapi', 'beef'],
    'seafood' => ['seafood', 'ikan', 'fish', 'udang', 'shrimp'],
    'sup' => ['sup', 'soup', 'soto', 'kuah'],
    'jus' => ['jus', 'juice', 'minuman', 'drink'],
    'camilan' => ['snack', 'camilan', 'kue'],
    'sarapan' => ['breakfast', 'sarapan', 'pagi'],
    'buah' => ['buah', 'fruit']
];

foreach ($all_tags as $tag) {
    foreach ($common_food_categories as $category => $keywords) {
        foreach ($keywords as $keyword_check) {
            if (stripos($tag, $keyword_check) !== false) {
                $jenis_makanan_options[$category] = ucwords(str_replace('_', ' ', $category));
                break 2;
            }
        }
    }
}

// --- LOGIKA PENCARIAN & FILTER ---
$base_query = "SELECT DISTINCT r.* FROM resep r";

// Jika ada filter kondisi kesehatan, tambahkan join
if (!empty($selected_kondisi)) {
    $base_query .= " INNER JOIN resep_kondisi rk ON r.id_resep = rk.id_resep";
    $base_query .= " INNER JOIN kondisi_kesehatan k ON rk.id_kondisi = k.id_kondisi";
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

// Filter berdasarkan jenis makanan
if (!empty($selected_jenis)) {
    $jenis_conditions = [];
    foreach ($selected_jenis as $jenis) {
        $jenis_conditions[] = "r.tags LIKE ?";
        $params[] = "%\"$jenis\"%";
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
    // Konversi string tags menjadi array jika ada
    if (!empty($row['tags'])) {
        $row['tags'] = json_decode($row['tags'], true) ?: [];
    } else {
        $row['tags'] = [];
    }
    
    // Ambil kondisi kesehatan untuk resep ini
    $query_kondisi_resep = "SELECT k.nama_kondisi, k.slug 
                           FROM kondisi_kesehatan k 
                           JOIN resep_kondisi rk ON k.id_kondisi = rk.id_kondisi 
                           WHERE rk.id_resep = ?";
    $stmt_kondisi = mysqli_prepare($koneksi, $query_kondisi_resep);
    mysqli_stmt_bind_param($stmt_kondisi, "i", $row['id_resep']);
    mysqli_stmt_execute($stmt_kondisi);
    $result_kondisi_resep = mysqli_stmt_get_result($stmt_kondisi);
    
    $kondisi_list = [];
    while ($kondisi = mysqli_fetch_assoc($result_kondisi_resep)) {
        $kondisi_list[] = $kondisi['nama_kondisi'];
        // Tambahkan juga slug ke tags untuk memudahkan filtering di frontend
        $row['tags'][] = $kondisi['slug'];
    }
    mysqli_stmt_close($stmt_kondisi);
    
    // Tambahkan kondisi kesehatan ke tags
    $row['tags'] = array_merge($row['tags'], $kondisi_list);
    
    $search_results[] = $row;
}

// Filter hasil berdasarkan kondisi kesehatan jika dipilih
if (!empty($selected_kondisi)) {
    $filtered_results = [];
    foreach ($search_results as $result) {
        $match = false;
        foreach ($selected_kondisi as $kondisi) {
            // Cek apakah kondisi ada dalam tags
            if (!empty($result['tags']) && in_array($kondisi, $result['tags'])) {
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
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$current_page = max(1, min($current_page, $total_pages == 0 ? 1 : $total_pages));

$offset = ($current_page - 1) * $items_per_page;
$paginated_results = array_slice($search_results, $offset, $items_per_page);

function build_filter_query_string($exclude_key = null) {
    $params = $_GET;
    if ($exclude_key && isset($params[$exclude_key])) {
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
            <form id="filterForm" method="GET" action="<?= BASE_URL ?>/search.php">
                <?php if (!empty($keyword)): ?>
                <input type="hidden" name="keyword" value="<?= htmlspecialchars($keyword) ?>">
                <?php endif; ?>

                <div class="filter-group">
                    <h4>Kondisi Kesehatan</h4>
                    <div class="checkbox-list" id="kondisiKesehatanList">
                        <?php foreach ($kondisi_kesehatan_options as $value => $label): ?>
                            <label>
                                <input type="checkbox" name="kondisi[]" value="<?= $value ?>" <?= in_array($value, $selected_kondisi) ? 'checked' : '' ?>>
                                <?= $label ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="filter-group">
                    <h4>Jenis Makanan</h4>
                    <div class="checkbox-list" id="jenisMakananList">
                        <?php 
                        $jenis_makanan_options = [
                            'mie' => 'Mie',
                            'nasi' => 'Nasi',
                            'sayuran' => 'Sayuran',
                            'daging' => 'Daging',
                            'seafood' => 'Seafood',
                            'sup' => 'Sup',
                            'jus' => 'Jus',
                            'camilan' => 'Camilan',
                            'sarapan' => 'Sarapan',
                            'buah' => 'Buah'
                        ];
                        foreach ($jenis_makanan_options as $value => $label): 
                        ?>
                            <label>
                                <input type="checkbox" name="jenis[]" value="<?= $value ?>" <?= in_array($value, $selected_jenis) ? 'checked' : '' ?>>
                                <?= $label ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <button type="submit" class="btn-apply-filter">Terapkan Filter</button>
            </form>
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

            <?php if (!empty($paginated_results)): ?>
                <div class="menu-grid">
                    <?php foreach ($paginated_results as $menu): ?>
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

<?php require_once __DIR__ . '/includes/footer.php'; ?>