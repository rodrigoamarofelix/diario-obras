<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AddTwoFactorFields extends Command
{
    protected $signature = 'two-factor:add-fields';
    protected $description = 'Adiciona campos do 2FA na tabela users';

    public function handle()
    {
        try {
            // Verificar se os campos já existem
            $columns = DB::select("SHOW COLUMNS FROM users LIKE 'two_factor_enabled'");

            if (count($columns) > 0) {
                $this->info('Campos do 2FA já existem na tabela users.');
                return;
            }

            // Executar migration
            DB::statement("
                ALTER TABLE users
                ADD COLUMN two_factor_enabled BOOLEAN DEFAULT FALSE AFTER email_verified_at,
                ADD COLUMN two_factor_secret VARCHAR(255) NULL AFTER two_factor_enabled,
                ADD COLUMN two_factor_backup_codes JSON NULL AFTER two_factor_secret,
                ADD COLUMN two_factor_enabled_at TIMESTAMP NULL AFTER two_factor_backup_codes
            ");

            $this->info('Campos do 2FA adicionados com sucesso!');

        } catch (\Exception $e) {
            $this->error('Erro ao adicionar campos do 2FA: ' . $e->getMessage());
        }
    }
}


