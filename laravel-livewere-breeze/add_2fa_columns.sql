-- Script SQL para adicionar colunas 2FA
-- Execute este script no seu banco de dados MySQL

USE laravel_livewire_breeze;

-- Adicionar colunas 2FA se não existirem
ALTER TABLE users
ADD COLUMN IF NOT EXISTS two_factor_enabled BOOLEAN DEFAULT FALSE AFTER email_verified_at,
ADD COLUMN IF NOT EXISTS two_factor_secret VARCHAR(255) NULL AFTER two_factor_enabled,
ADD COLUMN IF NOT EXISTS two_factor_backup_codes JSON NULL AFTER two_factor_secret,
ADD COLUMN IF NOT EXISTS two_factor_enabled_at TIMESTAMP NULL AFTER two_factor_backup_codes;

-- Verificar se as colunas foram adicionadas
DESCRIBE users;

-- Mostrar mensagem de sucesso
SELECT 'Colunas 2FA adicionadas com sucesso!' as status;

