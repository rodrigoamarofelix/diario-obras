@extends('layouts.admin')

@section('title', 'Editar Empresa')

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-edit text-primary"></i>
                        Editar Empresa
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('empresas.index') }}">Empresas</a></li>
                        <li class="breadcrumb-item active">Editar</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <form action="{{ route('empresas.update', $empresa) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Informações Básicas -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-info-circle text-primary"></i>
                                    Informações Básicas
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="nome">Nome Fantasia <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nome') is-invalid @enderror"
                                           id="nome" name="nome" value="{{ old('nome', $empresa->nome) }}" required>
                                    @error('nome')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="razao_social">Razão Social <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('razao_social') is-invalid @enderror"
                                           id="razao_social" name="razao_social" value="{{ old('razao_social', $empresa->razao_social) }}" required>
                                    @error('razao_social')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="cnpj">CNPJ <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('cnpj') is-invalid @enderror"
                                           id="cnpj" name="cnpj" value="{{ old('cnpj', $empresa->cnpj_formatado) }}"
                                           placeholder="00.000.000/0000-00" required>
                                    <small class="form-text text-muted">Digite apenas números</small>
                                    @error('cnpj')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           id="email" name="email" value="{{ old('email', $empresa->email) }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="site">Site</label>
                                    <input type="url" class="form-control @error('site') is-invalid @enderror"
                                           id="site" name="site" value="{{ old('site', $empresa->site) }}"
                                           placeholder="https://www.exemplo.com.br">
                                    @error('site')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contato -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-phone text-success"></i>
                                    Contato
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="telefone">Telefone</label>
                                    <input type="text" class="form-control @error('telefone') is-invalid @enderror"
                                           id="telefone" name="telefone" value="{{ old('telefone', $empresa->telefone_formatado) }}"
                                           placeholder="(00) 0000-0000">
                                    @error('telefone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="whatsapp">WhatsApp</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fab fa-whatsapp text-success"></i>
                                            </span>
                                        </div>
                                        <input type="text" class="form-control @error('whatsapp') is-invalid @enderror"
                                               id="whatsapp" name="whatsapp" value="{{ old('whatsapp', $empresa->whatsapp_formatado) }}"
                                               placeholder="(00) 00000-0000">
                                    </div>
                                    @error('whatsapp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="ativo" name="ativo"
                                               value="1" {{ old('ativo', $empresa->ativo) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="ativo">
                                            Empresa ativa
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Endereço -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-map-marker-alt text-warning"></i>
                                    Endereço
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="cep">CEP <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="text" class="form-control @error('cep') is-invalid @enderror"
                                                       id="cep" name="cep" value="{{ old('cep', $empresa->cep_formatado) }}"
                                                       placeholder="00000-000" required>
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-outline-primary" id="buscarCep">
                                                        <i class="fas fa-search"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            @error('cep')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="form-group">
                                            <label for="endereco">Endereço <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('endereco') is-invalid @enderror"
                                                   id="endereco" name="endereco" value="{{ old('endereco', $empresa->endereco) }}"
                                                   readonly required>
                                            @error('endereco')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="numero">Número</label>
                                            <input type="text" class="form-control @error('numero') is-invalid @enderror"
                                                   id="numero" name="numero" value="{{ old('numero', $empresa->numero) }}">
                                            @error('numero')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="complemento">Complemento</label>
                                            <input type="text" class="form-control @error('complemento') is-invalid @enderror"
                                                   id="complemento" name="complemento" value="{{ old('complemento', $empresa->complemento) }}">
                                            @error('complemento')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="bairro">Bairro <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('bairro') is-invalid @enderror"
                                                   id="bairro" name="bairro" value="{{ old('bairro', $empresa->bairro) }}"
                                                   readonly required>
                                            @error('bairro')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="cidade">Cidade <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('cidade') is-invalid @enderror"
                                                   id="cidade" name="cidade" value="{{ old('cidade', $empresa->cidade) }}"
                                                   readonly required>
                                            @error('cidade')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="estado">Estado <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('estado') is-invalid @enderror"
                                                   id="estado" name="estado" value="{{ old('estado', $empresa->estado) }}"
                                                   readonly required maxlength="2">
                                            @error('estado')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="pais">País</label>
                                            <input type="text" class="form-control @error('pais') is-invalid @enderror"
                                                   id="pais" name="pais" value="{{ old('pais', $empresa->pais) }}">
                                            @error('pais')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Observações -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-sticky-note text-info"></i>
                                    Observações
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <textarea class="form-control @error('observacoes') is-invalid @enderror"
                                              id="observacoes" name="observacoes" rows="8"
                                              placeholder="Observações adicionais...">{{ old('observacoes', $empresa->observacoes) }}</textarea>
                                    @error('observacoes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botões -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center">
                                <a href="{{ route('empresas.index') }}" class="btn btn-secondary mr-2">
                                    <i class="fas fa-arrow-left"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Atualizar Empresa
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Máscaras
    $('#cnpj').mask('00.000.000/0000-00');
    $('#cep').mask('00000-000');
    $('#telefone').mask('(00) 0000-0000');
    $('#whatsapp').mask('(00) 00000-0000');

    // Buscar CEP
    $('#buscarCep').click(function() {
        var cep = $('#cep').val().replace(/\D/g, '');

        if (cep.length !== 8) {
            alert('CEP deve ter 8 dígitos');
            return;
        }

        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        // Buscar CEP via API ViaCEP
        fetch(`https://viacep.com.br/ws/${cep}/json/`)
            .then(response => response.json())
            .then(data => {
                if (data.erro) {
                    alert('CEP não encontrado');
                } else {
                    $('#endereco').val(data.logradouro || '');
                    $('#bairro').val(data.bairro || '');
                    $('#cidade').val(data.localidade || '');
                    $('#estado').val(data.uf || '');

                    // Focar no campo de número após preencher endereço
                    if (data.logradouro) {
                        $('#numero').focus();
                    }
                }
            })
            .catch(error => {
                console.error('Erro ao buscar CEP:', error);
                alert('Erro ao buscar CEP. Tente novamente.');
            })
            .finally(() => {
                $(this).prop('disabled', false).html('<i class="fas fa-search"></i>');
            });
    });

    // Buscar CEP automaticamente quando sair do campo com 8 dígitos
    $('#cep').blur(function() {
        var cep = $(this).val().replace(/\D/g, '');
        if (cep.length === 8) {
            $('#buscarCep').click();
        }
    });

    // Buscar CEP automaticamente quando digitar 8 dígitos
    $('#cep').on('input', function() {
        var cep = $(this).val().replace(/\D/g, '');
        if (cep.length === 8) {
            // Aguardar um pouco para o usuário terminar de digitar
            setTimeout(() => {
                if ($(this).val().replace(/\D/g, '').length === 8) {
                    $('#buscarCep').click();
                }
            }, 500);
        }
    });

    // Validação de CNPJ em tempo real
    $('#cnpj').blur(function() {
        var cnpj = $(this).val().replace(/\D/g, '');

        if (cnpj.length === 14) {
            // Validação local usando JavaScript
            if (!validarCNPJ(cnpj)) {
                alert('CNPJ inválido');
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        }
    });

    // Função para validar CNPJ
    function validarCNPJ(cnpj) {
        cnpj = cnpj.replace(/[^\d]+/g,'');

        if(cnpj.length != 14) return false;

        // Elimina CNPJs invalidos conhecidos
        if (cnpj == "00000000000000" ||
            cnpj == "11111111111111" ||
            cnpj == "22222222222222" ||
            cnpj == "33333333333333" ||
            cnpj == "44444444444444" ||
            cnpj == "55555555555555" ||
            cnpj == "66666666666666" ||
            cnpj == "77777777777777" ||
            cnpj == "88888888888888" ||
            cnpj == "99999999999999")
            return false;

        // Valida DVs
        tamanho = cnpj.length - 2
        numeros = cnpj.substring(0,tamanho);
        digitos = cnpj.substring(tamanho);
        soma = 0;
        pos = tamanho - 7;
        for (i = tamanho; i >= 1; i--) {
            soma += numeros.charAt(tamanho - i) * pos--;
            if (pos < 2)
                pos = 9;
        }
        resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
        if (resultado != digitos.charAt(0))
            return false;

        tamanho = tamanho + 1;
        numeros = cnpj.substring(0,tamanho);
        soma = 0;
        pos = tamanho - 7;
        for (i = tamanho; i >= 1; i--) {
            soma += numeros.charAt(tamanho - i) * pos--;
            if (pos < 2)
                pos = 9;
        }
        resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
        if (resultado != digitos.charAt(1))
            return false;

        return true;
    }
});
</script>
@endpush
