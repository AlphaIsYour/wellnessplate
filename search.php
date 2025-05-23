<?php
require_once 'includes/config.php';
// require_once 'includes/functions.php';

$keyword = isset($_GET['keyword']) ? trim(htmlspecialchars($_GET['keyword'])) : '';

// Ambil filter dari GET request
$selected_kondisi = isset($_GET['kondisi']) && is_array($_GET['kondisi']) ? array_map('htmlspecialchars', $_GET['kondisi']) : [];
$selected_jenis = isset($_GET['jenis']) && is_array($_GET['jenis']) ? array_map('htmlspecialchars', $_GET['jenis']) : [];
// HAPUS: $manual_kondisi dan $manual_jenis dari GET params dan logika

$page_title = "Pencarian Menu Makanan";
if (!empty($keyword)) {
    $page_title .= " untuk \"$keyword\"";
}
require_once __DIR__ . '/includes/header.php';

// --- DATA MENU MAKANAN (Contoh, idealnya dari database) ---
// Pastikan deskripsi cukup panjang untuk menguji ellipsis
$all_food_menus = [
    ['id' => 1, 'name' => 'Salad Ayam Panggang Rendah Kalori', 'description' => 'Salad segar dengan potongan ayam panggang tanpa kulit, sayuran hijau, tomat ceri, dan dressing lemon rendah lemak. Pilihan tepat untuk diet dan menjaga gula darah. Sangat direkomendasikan bagi Anda yang aktif.', 'image' => BASE_URL . '/assets/images/menu/1.jpg', 'tags' => ['sayuran', 'daging', 'diet', 'diabetes', 'rendah_kalori', 'protein_tinggi']],
    ['id' => 2, 'name' => 'Smoothie Bayam Pisang Antioksidan', 'description' => 'Smoothie hijau kaya serat dan vitamin dari bayam, pisang, dan sedikit jahe. Cocok untuk sarapan atau camilan sehat. Memberikan energi tahan lama sepanjang hari.', 'image' => BASE_URL . '/assets/images/menu/2.jpg', 'tags' => ['jus', 'sayuran', 'buah', 'diet', 'vegetarian', 'antioksidan']],
    ['id' => 3, 'name' => 'Ikan Salmon Panggang Omega-3', 'description' => 'Fillet ikan salmon dipanggang dengan bumbu minimalis, disajikan dengan brokoli kukus. Sumber omega-3 yang baik untuk jantung dan otak. Rasanya lezat dan mudah dibuat.', 'image' => BASE_URL . '/assets/images/menu/3.jpg', 'tags' => ['daging', 'kolesterol', 'darah_tinggi', 'jantung', 'omega_3']],
    ['id' => 4, 'name' => 'Mie Shirataki Goreng Seafood Lezat', 'description' => 'Mie shirataki rendah kalori digoreng dengan udang, cumi, dan sayuran segar pilihan. Alternatif mie yang lebih sehat dan aman untuk penderita diabetes. Kenyang lebih lama.', 'image' => BASE_URL . '/assets/images/menu/4.jpg', 'tags' => ['mie', 'seafood', 'diet', 'diabetes', 'rendah_karbo']],
    ['id' => 5, 'name' => 'Bubur Quinoa Apel Kayu Manis Hangat', 'description' => 'Bubur hangat dari quinoa dengan potongan apel segar dan taburan kayu manis. Sarapan sehat bebas gluten dan tinggi serat, sangat baik untuk pencernaan Anda.', 'image' => BASE_URL . '/assets/images/menu/5.jpg', 'tags' => ['sarapan', 'buah', 'diet', 'vegetarian', 'alergi_gluten', 'serat_tinggi']],
    // Tambahkan lebih banyak menu jika perlu
    ['id' => 6, 'name' => 'Sop Buntut Rempah Spesial', 'description' => 'Sop buntut sapi pilihan dengan kuah kaldu kaya rempah, wortel, dan kentang. Cocok untuk penderita asam urat jika dikonsumsi dalam porsi terkontrol dan tanpa emping.', 'image' => BASE_URL . '/assets/images/menu/6.jpg', 'tags' => ['daging', 'sup', 'rempah', 'asam_urat_moderat']],
    ['id' => 7, 'name' => 'Jus Tiga Diva Segar Menyehatkan', 'description' => 'Kombinasi jus wortel, tomat, dan apel yang menyegarkan dan kaya vitamin. Baik untuk kesehatan mata dan kulit. Minuman detoksifikasi alami yang nikmat.', 'image' => BASE_URL . '/assets/images/menu/7.jpg', 'tags' => ['jus', 'sayuran', 'buah', 'vitamin_a', 'antioksidan']],
    ['id' => 8, 'name' => 'Nasi Goreng Beras Merah Komplit', 'description' => 'Nasi goreng menggunakan beras merah yang lebih kaya serat, ditambah aneka sayuran segar, telur, dan potongan ayam. Pilihan karbohidrat kompleks yang mengenyangkan.', 'image' => BASE_URL . '/assets/images/menu/8.jpg', 'tags' => ['nasi', 'sayuran', 'diet', 'serat_tinggi', 'daging']],
    ['id' => 9, 'name' => 'Tumis Kangkung Bawang Putih Praktis', 'description' => 'Tumis kangkung sederhana dengan aroma bawang putih yang menggugah selera. Sumber zat besi yang baik dan cepat disajikan untuk keluarga.', 'image' => BASE_URL . '/assets/images/menu/9.jpg', 'tags' => ['sayuran', 'vegetarian', 'zat_besi', 'rendah_garam_opsi']],
    ['id' => 10, 'name' => 'Ayam Popcorn Krispi Rendah Garam', 'description' => 'Potongan ayam fillet dibalut tepung tipis dan digoreng hingga renyah sempurna, dengan penggunaan garam minimal. Camilan lebih sehat dan tinggi protein.', 'image' => BASE_URL . '/assets/images/menu/10.jpg', 'tags' => ['daging', 'camilan', 'rendah_garam', 'protein_tinggi']],
    ['id' => 11, 'name' => 'Puding Chia Seed Mangga Lembut', 'description' => 'Puding sehat dari chia seed dengan puree mangga segar dan manis. Kaya serat dan omega-3, cocok untuk dessert atau sarapan. Teksturnya lembut dan nikmat.', 'image' => BASE_URL . '/assets/images/menu/4.jpg', 'tags' => ['dessert', 'buah', 'diet', 'vegetarian', 'serat_tinggi', 'omega_3']],
    ['id' => 12, 'name' => 'Steak Tempe Lada Hitam Gurih', 'description' => 'Steak dari tempe pilihan dengan saus lada hitam kental yang gurih. Alternatif protein nabati yang lezat dan mengenyangkan. Cocok untuk vegetarian dan vegan.', 'image' => BASE_URL . '/assets/images/menu/8.jpg', 'tags' => ['protein_nabati', 'vegetarian', 'diet', 'rendah_kolesterol']],
];


// --- LOGIKA PENCARIAN & FILTER ---
$search_results = $all_food_menus;

if (!empty($keyword)) {
    $search_results = array_filter($search_results, function ($menu) use ($keyword) {
        return stripos($menu['name'], $keyword) !== false || stripos($menu['description'], $keyword) !== false;
    });
}

// Fungsi untuk mengecek tag (disederhanakan karena tidak ada manual filter dari UI)
function check_tags_simple($menu_tags, $selected_filters) {
    if (empty($selected_filters)) return true; // Jika tidak ada filter dipilih, semua lolos
    foreach ($selected_filters as $filter) {
        $filter_found_in_tags = false;
        foreach ($menu_tags as $menu_tag) {
            if (strtolower($filter) === strtolower($menu_tag)) {
                $filter_found_in_tags = true;
                break;
            }
        }
        if (!$filter_found_in_tags) {
            return false; // Jika satu filter WAJIB tidak ada di tags, menu tidak cocok
        }
    }
    return true; // Semua filter yang dipilih ada di tags menu
}


if (!empty($selected_kondisi)) {
    $search_results = array_filter($search_results, function ($menu) use ($selected_kondisi) {
        return check_tags_simple($menu['tags'], $selected_kondisi);
    });
}

if (!empty($selected_jenis)) {
    $search_results = array_filter($search_results, function ($menu) use ($selected_jenis) {
        return check_tags_simple($menu['tags'], $selected_jenis);
    });
}

// --- PAGINATION ---
$items_per_page = 8;
$total_items = count($search_results);
$total_pages = ceil($total_items / $items_per_page);
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$current_page = max(1, min($current_page, $total_pages == 0 ? 1 : $total_pages));

$offset = ($current_page - 1) * $items_per_page;
$paginated_results = array_slice($search_results, $offset, $items_per_page);

// Data untuk filter checkboxes
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
    // HAPUS: Parameter manual_kondisi dan manual_jenis jika ada dari URL lama
    unset($params['manual_kondisi']);
    unset($params['manual_jenis']);
    return http_build_query($params);
}
?>

<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/search.css?v=<?= time() ?>"> <!-- Tambah versi untuk cache busting -->

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
                    <!-- HAPUS: Tombol Show More -->
                    <!-- HAPUS: Input Manual -->
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
                    <!-- HAPUS: Tombol Show More -->
                    <!-- HAPUS: Input Manual -->
                </div>
                
                <button type="submit" class="btn-apply-filter">Terapkan Filter</button> <!-- PERUBAHAN: Tombol submit -->
            </form>
        </aside>

        <main class="search-results-area">
            <h1>
                <?php
                if (!empty($keyword)) {
                    echo "Hasil Pencarian untuk: <em>" . htmlspecialchars($keyword) . "</em>";
                } elseif (!empty($selected_kondisi) || !empty($selected_jenis)) { // Disederhanakan
                    echo "Hasil Pencarian Berdasarkan Filter";
                } else {
                    echo "Semua Menu Makanan";
                }
                ?>
            </h1>

            <p class="results-info">Ditemukan <?= $total_items ?> hasil menu makanan.</p>

            <?php
            $active_filters_exist = !empty($selected_kondisi) || !empty($selected_jenis); // Disederhanakan
            if ($active_filters_exist):
            ?>
                <div class="selected-filters-container">
                    <span>Filter aktif:</span>
                    <?php foreach ($selected_kondisi as $sk): ?>
                        <span class="filter-tag"><?= htmlspecialchars($kondisi_kesehatan_options[$sk] ?? $sk) ?>
                            <button class="remove-filter-btn" data-filter-type="kondisi" data-filter-value="<?= $sk ?>">×</button>
                        </span>
                    <?php endforeach; ?>
                    <!-- HAPUS: Tag untuk manual_kondisi -->

                    <?php foreach ($selected_jenis as $sj): ?>
                        <span class="filter-tag"><?= htmlspecialchars($jenis_makanan_options[$sj] ?? $sj) ?>
                            <button class="remove-filter-btn" data-filter-type="jenis" data-filter-value="<?= $sj ?>">×</button>
                        </span>
                    <?php endforeach; ?>
                    <!-- HAPUS: Tag untuk manual_jenis -->
                    <button id="clearAllFiltersBtn">Clear All Filters</button>
                </div>
            <?php endif; ?>


            <?php if (!empty($paginated_results)): ?>
    <div class="menu-grid">
        <?php foreach ($paginated_results as $menu): ?>
            <div class="menu-card">
                <a href="<?= BASE_URL . '/menu/detail.php?id=' . $menu['id'] ?>"> <!-- Link gambar -->
                    <img src="<?= htmlspecialchars($menu['image']) ?>" alt="<?= htmlspecialchars($menu['name']) ?>">
                </a>
                <div class="menu-card-content">
                    <div class="menu-info"> <!-- Wrapper untuk judul dan tags -->
                        <h3>
                            <a href="<?= BASE_URL . '/menu/detail.php?id=' . $menu['id'] ?>" title="<?= htmlspecialchars($menu['name']) ?>"> <!-- Tambahkan title attribute untuk full name on hover -->
                                <?= htmlspecialchars($menu['name']) ?>
                            </a>
                        </h3>
                        <?php if (!empty($menu['tags'])): ?>
                        <div class="menu-tags">
                            <?php 
                            $tag_limit = 2; // Atau 3 jika masih ada ruang
                            $displayed_tags = 0;
                            foreach ($menu['tags'] as $tag_text): 
                                // Opsi: Jangan tampilkan tag yang sudah jadi filter aktif (bisa di-uncomment jika mau)
                                // if (in_array($tag_text, $selected_kondisi) || in_array($tag_text, $selected_jenis)) {
                                //     continue; 
                                // }
                                if ($displayed_tags >= $tag_limit) break;
                            ?>
                                <span class="tag"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $tag_text))) ?></span>
                            <?php 
                                $displayed_tags++;
                            endforeach; 
                            
                            // Logika untuk menampilkan '...' jika ada lebih banyak tag (tidak termasuk yang sudah difilter)
                            $relevant_tags_count = 0;
                            foreach($menu['tags'] as $t){
                                // Hitung tag yang relevan (bukan yang sedang aktif sebagai filter)
                                if (!(in_array($t, $selected_kondisi) || in_array($t, $selected_jenis))) {
                                     $relevant_tags_count++;
                                }
                            }
                            if ($relevant_tags_count > $tag_limit) echo '<span class="tag">...</span>';
                            ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <a href="<?= BASE_URL . '/menu/detail.php?id=' . $menu['id'] ?>" class="btn-details">Lihat Detail</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($total_pages > 1): ?>
        <nav class="pagination">
            <?php
            // Previous page
            if ($current_page > 1) {
                echo '<a href="?page=' . ($current_page - 1) . '&' . build_filter_query_string('page') . '">« Prev</a>';
            } else {
                echo '<span class="disabled">« Prev</span>';
            }

            // Page numbers
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
            
            // Next page
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

<script src="<?= BASE_URL ?>/assets/js/search.js?v=<?= time() ?>"></script> <!-- Tambah versi untuk cache busting -->
<?php
require_once __DIR__ . '/includes/footer.php';
?>