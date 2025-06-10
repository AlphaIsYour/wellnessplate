-- Hapus trigger dan fungsi yang mungkin sudah ada
DROP TRIGGER IF EXISTS after_resep_tags_insert;
DROP TRIGGER IF EXISTS after_resep_tags_delete;
DROP TRIGGER IF EXISTS after_resep_tags_update;
DROP FUNCTION IF EXISTS update_resep_tags;

-- Buat fungsi untuk mengupdate tags dalam format JSON
DELIMITER $$
CREATE FUNCTION update_resep_tags(p_id_resep INT) 
RETURNS JSON
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE tags_json JSON;
    
    SELECT JSON_ARRAYAGG(
        JSON_OBJECT(
            'id_tag', t.id_tag,
            'nama_tag', t.nama_tag,
            'slug', t.slug
        )
    )
    INTO tags_json
    FROM resep_tags rt
    JOIN tags t ON rt.id_tag = t.id_tag
    WHERE rt.id_resep = p_id_resep;
    
    -- Jika tidak ada tags, kembalikan array kosong
    IF tags_json IS NULL THEN
        RETURN JSON_ARRAY();
    END IF;
    
    RETURN tags_json;
END$$
DELIMITER ;

-- Trigger setelah INSERT di resep_tags
DELIMITER $$
CREATE TRIGGER after_resep_tags_insert
AFTER INSERT ON resep_tags
FOR EACH ROW
BEGIN
    -- Update resep dengan tags baru
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
    -- Update resep dengan tags yang tersisa
    UPDATE resep 
    SET tags = update_resep_tags(OLD.id_resep)
    WHERE id_resep = OLD.id_resep;
END$$
DELIMITER ;

-- Trigger setelah UPDATE di resep_tags (untuk jaga-jaga)
DELIMITER $$
CREATE TRIGGER after_resep_tags_update
AFTER UPDATE ON resep_tags
FOR EACH ROW
BEGIN
    -- Update resep jika id_resep berubah
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

-- Verifikasi trigger
SELECT TRIGGER_SCHEMA, TRIGGER_NAME, EVENT_MANIPULATION, ACTION_STATEMENT 
FROM information_schema.TRIGGERS 
WHERE TRIGGER_SCHEMA = DATABASE() 
AND (TRIGGER_NAME LIKE '%resep_tags%');

-- Update semua data yang ada untuk memastikan sinkronisasi
UPDATE resep r 
SET r.tags = update_resep_tags(r.id_resep); 