-- Hapus trigger dan fungsi yang mungkin sudah ada
DROP TRIGGER IF EXISTS after_resep_tags_insert;
DROP TRIGGER IF EXISTS after_resep_tags_delete;
DROP FUNCTION IF EXISTS update_resep_tags;

-- Buat fungsi untuk mengupdate tags dalam format JSON
DELIMITER $$
CREATE FUNCTION update_resep_tags(p_id_resep INT) 
RETURNS JSON
DETERMINISTIC
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
    
    RETURN COALESCE(tags_json, JSON_ARRAY());
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