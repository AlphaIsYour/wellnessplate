-- Pastikan kolom tags bertipe JSON
ALTER TABLE resep MODIFY COLUMN tags JSON DEFAULT NULL; 