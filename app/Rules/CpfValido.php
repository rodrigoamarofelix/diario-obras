<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CpfValido implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Remove todos os caracteres não numéricos
        $cpf = preg_replace('/\D/', '', $value);

        // Verifica se tem 11 dígitos
        if (strlen($cpf) !== 11) {
            $fail('O CPF deve ter exatamente 11 dígitos.');
            return;
        }

        // Verifica se todos os dígitos são iguais (CPF inválido)
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            $fail('O CPF informado é inválido.');
            return;
        }

        // Validação do primeiro dígito verificador
        $soma = 0;
        for ($i = 0; $i < 9; $i++) {
            $soma += intval($cpf[$i]) * (10 - $i);
        }
        $resto = $soma % 11;
        $digito1 = $resto < 2 ? 0 : 11 - $resto;

        if (intval($cpf[9]) !== $digito1) {
            $fail('O CPF informado é inválido.');
            return;
        }

        // Validação do segundo dígito verificador
        $soma = 0;
        for ($i = 0; $i < 10; $i++) {
            $soma += intval($cpf[$i]) * (11 - $i);
        }
        $resto = $soma % 11;
        $digito2 = $resto < 2 ? 0 : 11 - $resto;

        if (intval($cpf[10]) !== $digito2) {
            $fail('O CPF informado é inválido.');
            return;
        }
    }
}
