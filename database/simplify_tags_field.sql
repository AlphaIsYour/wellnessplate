-- Ubah field tags menjadi TEXT biasa
ALTER TABLE resep MODIFY COLUMN tags TEXT;

-- Hapus trigger yang lama
DROP TRIGGER IF EXISTS after_resep_tags_insert;
DROP TRIGGER IF EXISTS after_resep_tags_delete;
DROP TRIGGER IF EXISTS after_resep_tags_update;
DROP FUNCTION IF EXISTS update_resep_tags;

-- Buat fungsi untuk mengupdate tags dalam format string
DELIMITER $$
CREATE FUNCTION update_resep_tags(p_id_resep INT) 
RETURNS TEXT
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE tag_list TEXT;
    
    SELECT GROUP_CONCAT(t.nama_tag ORDER BY t.nama_tag ASC SEPARATOR ', ')
    INTO tag_list
    FROM resep_tags rt
    JOIN tags t ON rt.id_tag = t.id_tag
    WHERE rt.id_resep = p_id_resep;
    
    RETURN COALESCE(tag_list, '');
END$$
DELIMITER ;

-- Trigger setelah INSERT di resep_tags
DELIMITER $$
CREATE TRIGGER after_resep_tags_insert
AFTER INSERT ON resep_tags
FOR EACH ROW
BEGIN
    UPDATE resep 
    SET tags = update_resep_tags(NEW.id_resep)
    WHERE id_resep = NEW.id_resep;
END$$
DELIMITER ;

-- Trigger setelah DELETE di resep_tags
DELIMITER $$
CREATE TRIGGER after_resep_tags_delete
AFTER DELETE ON resep_tags
FOR EACH ROW
BEGIN
    UPDATE resep 
    SET tags = update_resep_tags(OLD.id_resep)
    WHERE id_resep = OLD.id_resep;
END$$
DELIMITER ;

-- Trigger setelah UPDATE di resep_tags
DELIMITER $$
CREATE TRIGGER after_resep_tags_update
AFTER UPDATE ON resep_tags
FOR EACH ROW
BEGIN
    IF OLD.id_resep != NEW.id_resep THEN
        -- Update resep lama
        UPDATE resep 
        SET tags = update_resep_tags(OLD.id_resep)
        WHERE id_resep = OLD.id_resep;
        
        -- Update resep baru
        UPDATE resep 
        SET tags = update_resep_tags(NEW.id_resep)
        WHERE id_resep = NEW.id_resep;
    ELSE
        -- Update resep yang dimodifikasi
        UPDATE resep 
        SET tags = update_resep_tags(NEW.id_resep)
        WHERE id_resep = NEW.id_resep;
    END IF;
END$$
DELIMITER ;

-- Update semua data yang ada
UPDATE resep r 
SET r.tags = update_resep_tags(r.id_resep); 