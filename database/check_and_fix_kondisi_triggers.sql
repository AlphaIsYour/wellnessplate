
-- Hapus trigger dan fungsi yang mungkin sudah ada
DROP TRIGGER IF EXISTS after_resep_kondisi_insert;
DROP TRIGGER IF EXISTS after_resep_kondisi_delete;
DROP TRIGGER IF EXISTS after_resep_kondisi_update;
DROP FUNCTION IF EXISTS update_resep_kondisi;

-- Buat fungsi untuk mengupdate id_kondisi (mengambil kondisi pertama)
DELIMITER $$
CREATE FUNCTION update_resep_kondisi(p_id_resep INT) 
RETURNS VARCHAR(10)
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE kondisi_id VARCHAR(10);
    
    -- Ambil id_kondisi pertama untuk resep tersebut
    SELECT rk.id_kondisi
    INTO kondisi_id
    FROM resep_kondisi rk
    WHERE rk.id_resep = p_id_resep
    LIMIT 1;
    
    -- Jika tidak ada kondisi, kembalikan NULL
    RETURN kondisi_id;
END$$
DELIMITER ;

-- Trigger setelah INSERT di resep_kondisi
DELIMITER $$
CREATE TRIGGER after_resep_kondisi_insert
AFTER INSERT ON resep_kondisi
FOR EACH ROW
BEGIN
    -- Update resep dengan id_kondisi baru
    UPDATE resep 
    SET id_kondisi = update_resep_kondisi(NEW.id_resep)
    WHERE id_resep = NEW.id_resep;
END$$
DELIMITER ;

-- Trigger setelah DELETE di resep_kondisi
DELIMITER $$
CREATE TRIGGER after_resep_kondisi_delete
AFTER DELETE ON resep_kondisi
FOR EACH ROW
BEGIN
    -- Update resep dengan id_kondisi yang tersisa (jika ada)
    UPDATE resep 
    SET id_kondisi = update_resep_kondisi(OLD.id_resep)
    WHERE id_resep = OLD.id_resep;
END$$
DELIMITER ;

-- Trigger setelah UPDATE di resep_kondisi
DELIMITER $$
CREATE TRIGGER after_resep_kondisi_update
AFTER UPDATE ON resep_kondisi
FOR EACH ROW
BEGIN
    -- Update resep jika id_resep berubah
    IF OLD.id_resep != NEW.id_resep THEN
        -- Update resep lama
        UPDATE resep 
        SET id_kondisi = update_resep_kondisi(OLD.id_resep)
        WHERE id_resep = OLD.id_resep;
        
        -- Update resep baru
        UPDATE resep 
        SET id_kondisi = update_resep_kondisi(NEW.id_resep)
        WHERE id_resep = NEW.id_resep;
    ELSE
        -- Update resep yang dimodifikasi
        UPDATE resep 
        SET id_kondisi = update_resep_kondisi(NEW.id_resep)
        WHERE id_resep = NEW.id_resep;
    END IF;
END$$
DELIMITER ;

-- Verifikasi trigger
SELECT TRIGGER_SCHEMA, TRIGGER_NAME, EVENT_MANIPULATION, ACTION_STATEMENT 
FROM information_schema.TRIGGERS 
WHERE TRIGGER_SCHEMA = DATABASE() 
AND (TRIGGER_NAME LIKE '%resep_kondisi%');

-- Update semua data yang ada untuk memastikan sinkronisasi
UPDATE resep r 
SET r.id_kondisi = update_resep_kondisi(r.id_resep); 