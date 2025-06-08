<?php
require_once 'config/koneksi.php';

$keyword = isset($_GET['keyword']) ? trim(htmlspecialchars($_GET['keyword'])) : '';

$selected_kondisi = isset($_GET['kondisi']) && is_array($_GET['kondisi']) ? array_map('htmlspecialchars', $_GET['kondisi']) : [];
$selected_jenis = isset($_GET['jenis']) && is_array($_GET['jenis']) ? array_map('htmlspecialchars', $_GET['jenis']) : [];

$page_title = "Pencarian Menu Makanan";
if (!empty($keyword)) {
    $page_title .= " untuk \"$keyword\"";
}
require_once __DIR__ . '/includes/header.php';

// --- LOGIKA PENCARIAN & FILTER ---
$query = "SELECT id_resep, nama_resep, deskripsi, image, tags FROM resep WHERE 1=1";
$params = [];
$types = "";

if (!empty($keyword)) {
    $query .= " AND (nama_resep LIKE ? OR deskripsi LIKE ?)";
    $keyword_param = "%" . $keyword . "%";
    $params[] = $keyword_param;
    $params[] = $keyword_param;
    $types .= "ss";
}

// Filter berdasarkan tags (kondisi dan jenis)
if (!empty($selected_kondisi) || !empty($selected_jenis)) {
    $all_filters = array_merge($selected_kondisi, $selected_jenis);
    $tag_conditions = [];
    foreach ($all_filters as $filter) {
        $tag_conditions[] = "JSON_CONTAINS(tags, ?)";
        $params[] = json_encode($filter);
        $types .= "s";
    }
    if (!empty($tag_conditions)) {
        $query .= " AND " . implode(" AND ", $tag_conditions);
    }
}

$query .= " ORDER BY tanggal_dibuat DESC";

// Prepare and execute the statement
$stmt = mysqli_prepare($koneksi, $query);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$search_results = [];
while ($row = mysqli_fetch_assoc($result)) {
    $row['tags'] = json_decode($row['tags'], true);
    $search_results[] = $row;
}

// --- PAGINATION ---
$items_per_page = 8;
$total_items = count($search_results);
$total_pages = ceil($total_items / $items_per_page);
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$current_page = max(1, min($current_page, $total_pages == 0 ? 1 : $total_pages));

$offset = ($current_page - 1) * $items_per_page;
$paginated_results = array_slice($search_results, $offset, $items_per_page);

$kondisi_kesehatan_options = [
    'diabetes' => 'Diabetes', 'diet' => 'Diet', 'kolesterol' => 'Kolesterol', 'asam_urat' => 'Asam Urat', 'darah_tinggi' => 'Darah Tinggi',
    'jantung' => 'Jantung', 'ginjal' => 'Ginjal', 'maag' => 'Maag', 'alergi_gluten' => 'Alergi Gluten', 'rendah_garam' => 'Rendah Garam'
];
$jenis_makanan_options = [
    'mie' => 'Mie', 'jus' => 'Jus', 'sayuran' => 'Sayuran', 'daging' => 'Daging (Ikan/Ayam/Sapi)', 'seafood' => 'Seafood',
    'buah' => 'Buah', 'nasi' => 'Nasi/Karbohidrat', 'sup' => 'Sup', 'camilan' => 'Camilan', 'sarapan' => 'Sarapan/Dessert'
];

function build_filter_query_string($exclude_key = null) {
    $params = $_GET;
    if ($exclude_key && isset($params[$exclude_key])) {
        unset($params[$exclude_key]);
    }
    unset($params['manual_kondisi']);
    unset($params['manual_jenis']);
    return http_build_query($params);
}
?>

<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/search.css?v=<?= time() ?>">

<div class="search-page-container">
    <?php require_once __DIR__ . '/includes/components/search-bar.php'; ?>

    <div class="search-layout">
        <aside class="filter-sidebar">
            <form id="filterForm" method="GET" action="<?= BASE_URL ?>/search.php">
                <input type="hidden" name="keyword" value="<?= htmlspecialchars($keyword) ?>">

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
                        <?php foreach ($jenis_makanan_options as $value => $label): ?>
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
                    <br>Coba ubah filter atau kata kunci pencarian Anda.
                </p>
            <?php endif; ?>
        </main>
    </div>
</div>

<script src="<?= BASE_URL ?>/assets/js/search.js?v=<?= time() ?>"></script>
<?php
require_once __DIR__ . '/includes/footer.php';
?>