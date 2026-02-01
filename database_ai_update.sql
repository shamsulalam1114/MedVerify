ALTER TABLE medicine_verifications 
ADD COLUMN ai_analysis TEXT AFTER verification_notes,
ADD COLUMN image_hash VARCHAR(32) AFTER image_uploaded;
