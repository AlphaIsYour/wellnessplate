<?php
if (!defined('BASE_URL')) {
}
?>
<div class="search-bar">
    <form action="<?= BASE_URL ?>/search.php" method="GET" class="search-form">
        <div class="search-input-wrapper">
            <input type="text" 
                   name="keyword" 
                   placeholder="Cari menu makanan..." 
                   value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>"
                   class="search-input">
            <button type="submit" class="search-button">
                <i class="fas fa-search"></i>
            </button>
        </div>
        
        <?php
        // Preserve selected filters when searching
        if (!empty($_GET['kondisi']) && is_array($_GET['kondisi'])) {
            foreach ($_GET['kondisi'] as $kondisi) {
                echo '<input type="hidden" name="kondisi[]" value="' . htmlspecialchars($kondisi) . '">';
            }
        }
        if (!empty($_GET['jenis']) && is_array($_GET['jenis'])) {
            foreach ($_GET['jenis'] as $jenis) {
                echo '<input type="hidden" name="jenis[]" value="' . htmlspecialchars($jenis) . '">';
            }
        }
        ?>
    </form>
</div>