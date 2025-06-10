-- Buat tabel tags
CREATE TABLE IF NOT EXISTS tags (
    id_tag INT AUTO_INCREMENT PRIMARY KEY,
    nama_tag VARCHAR(50) NOT NULL,
    slug VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Buat tabel junction untuk resep_tags
CREATE TABLE IF NOT EXISTS resep_tags (
    id_resep_tag INT AUTO_INCREMENT PRIMARY KEY,
    id_resep INT NOT NULL,
    id_tag INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_resep) REFERENCES resep(id_resep) ON DELETE CASCADE,
    FOREIGN KEY (id_tag) REFERENCES tags(id_tag) ON DELETE CASCADE,
    UNIQUE KEY unique_resep_tag (id_resep, id_tag)
) ENGINE=InnoDB;

-- Masukkan beberapa contoh tags
INSERT INTO tags (nama_tag, slug) VALUES
('Mie', 'mie'),
('Nasi', 'nasi'),
('Sayur', 'sayur'),
('Daging', 'daging'),
('Ayam', 'ayam'),
('Ikan', 'ikan'),
('Sup', 'sup'),
('Goreng', 'goreng'),
('Tumis', 'tumis'),
('Rebus', 'rebus'),
('Kukus', 'kukus'),
('Panggang', 'panggang'),
('Berkuah', 'berkuah'),
('Kering', 'kering'),
('Pedas', 'pedas'),
('Manis', 'manis'),
('Asam', 'asam'),
('Asin', 'asin'); 