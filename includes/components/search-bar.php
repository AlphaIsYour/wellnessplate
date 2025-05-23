<?php
if (!defined('BASE_URL')) {
}
?>
<section class="search-section" style="padding: 5px 0; background-color: #fff; text-align: center; border-radius: 10px; margin-bottom: 20px;">
    <div class="container" style="max-width: 700px; margin: auto;">
        <form action="<?php echo BASE_URL; ?>/search.php" method="GET" style="display: flex; margin-top: 10px;">
            <input type="text" name="keyword" placeholder="Masukkan kata kunci pencarian..." style="flex-grow: 1; padding: 12px 15px; border: 1px solid #ccc; border-radius: 8px 0 0 8px; font-size: 1rem;" required>
            <button type="submit" style="padding: 12px 25px; background-color: #28a745; color: white; border: none; border-radius: 0 8px 8px 0; cursor: pointer; font-size: 1rem; font-weight: bold;">Cari</button>
        </form>
    </div>
</section>