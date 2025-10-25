<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa o envio de e-mail de reset de senha';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        $this->info("Testando envio de e-mail para: {$email}");

        $status = Password::sendResetLink(['email' => $email]);

        if ($status === Password::RESET_LINK_SENT) {
            $this->info('✅ E-mail enviado com sucesso!');
            $this->info('Verifique o arquivo de log em: storage/logs/laravel.log');
        } else {
            $this->error('❌ Erro ao enviar e-mail: ' . $status);
        }
    }
}
