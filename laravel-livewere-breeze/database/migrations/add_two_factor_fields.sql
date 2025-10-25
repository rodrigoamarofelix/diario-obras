-- Adicionar campos de 2FA na tabela users
ALTER TABLE users
ADD COLUMN two_factor_enabled BOOLEAN DEFAULT FALSE AFTER email_verified_at,
ADD COLUMN two_factor_secret VARCHAR(255) NULL AFTER two_factor_enabled,
ADD COLUMN two_factor_backup_codes JSON NULL AFTER two_factor_secret,
ADD COLUMN two_factor_enabled_at TIMESTAMP NULL AFTER two_factor_backup_codes;


