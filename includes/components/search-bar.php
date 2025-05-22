    <section class="search-section" style="padding: 20px 0; background-color: #f8f9fa; text-align: center; border-radius: 10px;">
        <div class="container" style="max-width: 700px; margin: auto;">
            <h2>Cari Resep, Artikel, atau Produk Kesehatan</h2>
            <form action="<?php echo BASE_URL; ?>/pencarian.php" method="GET" style="display: flex; margin-top: 15px;">
                <input type="text" name="keyword" placeholder="Masukkan kata kunci..." style="flex-grow: 1; padding: 10px; border: 1px solid #ccc; border-radius: 8px 0 0 8px;" required>
                <button type="submit" style="padding: 10px 20px; background-color: #28a745; color: white; border: none; border-radius: 0 4px 4px 0; cursor: pointer;">Cari</button>
            </form>
            <!-- Kamu perlu membuat file pencarian.php untuk memproses ini -->
        </div>
    </section>