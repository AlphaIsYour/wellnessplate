-- Periksa dan tambah kolom yang hilang
ALTER TABLE resep 
ADD COLUMN IF NOT EXISTS bahan_bahan TEXT AFTER deskripsi,
ADD COLUMN IF NOT EXISTS langkah_langkah TEXT AFTER bahan_bahan; 