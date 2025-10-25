<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Empresa extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nome',
        'razao_social',
        'cnpj',
        'email',
        'telefone',
        'whatsapp',
        'cep',
        'endereco',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'pais',
        'site',
        'observacoes',
        'ativo',
        'created_by'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relacionamentos
    public function criadoPor()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function projetos()
    {
        return $this->belongsToMany(Projeto::class, 'projeto_empresa')
                    ->withPivot(['tipo_participacao', 'observacoes', 'ativo'])
                    ->withTimestamps();
    }

    // Scopes
    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeInativas($query)
    {
        return $query->where('ativo', false);
    }

    // Accessors
    public function getCnpjFormatadoAttribute()
    {
        return $this->formatarCnpj($this->cnpj);
    }

    public function getTelefoneFormatadoAttribute()
    {
        return $this->formatarTelefone($this->telefone);
    }

    public function getWhatsappFormatadoAttribute()
    {
        return $this->formatarTelefone($this->whatsapp);
    }

    public function getEnderecoCompletoAttribute()
    {
        $endereco = $this->endereco;
        if ($this->numero) {
            $endereco .= ', ' . $this->numero;
        }
        if ($this->complemento) {
            $endereco .= ', ' . $this->complemento;
        }
        $endereco .= ' - ' . $this->bairro;
        $endereco .= ' - ' . $this->cidade . '/' . $this->estado;
        $endereco .= ' - CEP: ' . $this->formatarCep($this->cep);

        return $endereco;
    }

    // Métodos auxiliares
    private function formatarCnpj($cnpj)
    {
        $cnpj = preg_replace('/\D/', '', $cnpj);
        return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $cnpj);
    }

    private function formatarTelefone($telefone)
    {
        $telefone = preg_replace('/\D/', '', $telefone);

        if (strlen($telefone) == 11) {
            return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $telefone);
        } elseif (strlen($telefone) == 10) {
            return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $telefone);
        }

        return $telefone;
    }

    private function formatarCep($cep)
    {
        $cep = preg_replace('/\D/', '', $cep);
        return preg_replace('/(\d{5})(\d{3})/', '$1-$2', $cep);
    }

    // Validação de CNPJ
    public static function validarCnpj($cnpj)
    {
        $cnpj = preg_replace('/\D/', '', $cnpj);

        if (strlen($cnpj) != 14) {
            return false;
        }

        // Verifica se todos os dígitos são iguais
        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }

        // Validação do primeiro dígito verificador
        $soma = 0;
        $multiplicador = 5;
        for ($i = 0; $i < 12; $i++) {
            $soma += $cnpj[$i] * $multiplicador;
            $multiplicador = ($multiplicador == 2) ? 9 : $multiplicador - 1;
        }
        $resto = $soma % 11;
        $dv1 = ($resto < 2) ? 0 : 11 - $resto;

        if ($cnpj[12] != $dv1) {
            return false;
        }

        // Validação do segundo dígito verificador
        $soma = 0;
        $multiplicador = 6;
        for ($i = 0; $i < 13; $i++) {
            $soma += $cnpj[$i] * $multiplicador;
            $multiplicador = ($multiplicador == 2) ? 9 : $multiplicador - 1;
        }
        $resto = $soma % 11;
        $dv2 = ($resto < 2) ? 0 : 11 - $resto;

        return $cnpj[13] == $dv2;
    }
}
