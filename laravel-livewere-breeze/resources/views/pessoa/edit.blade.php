@extends('layouts.admin')

@section('title', 'Editar Pessoa')
@section('page-title', 'Editar Pessoa')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('pessoa.index') }}">Pessoas</a></li>
<li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Editar Dados da Pessoa</h3>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('pessoa.update', $pessoa->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nome">Nome Completo *</label>
                                <input type="text" class="form-control @error('nome') is-invalid @enderror"
                                       id="nome" name="nome" value="{{ old('nome', $pessoa->nome) }}" required>
                                @error('nome')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cpf">CPF *</label>
                                <input type="text" class="form-control @error('cpf') is-invalid @enderror"
                                       id="cpf" name="cpf" value="{{ old('cpf', $pessoa->cpf_formatado) }}"
                                       placeholder="000.000.000-00" required>
                                @error('cpf')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lotacao_id">Lotação *</label>
                                <select class="form-control @error('lotacao_id') is-invalid @enderror"
                                        id="lotacao_id" name="lotacao_id" required>
                                    <option value="">Selecione uma lotação</option>
                                    @if(isset($lotacoes))
                                        @foreach($lotacoes as $lotacao)
                                            <option value="{{ $lotacao->id }}"
                                                    {{ old('lotacao_id', $pessoa->lotacao_id) == $lotacao->id ? 'selected' : '' }}>
                                                {{ $lotacao->nome }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('lotacao_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status *</label>
                                <select class="form-control @error('status') is-invalid @enderror"
                                        id="status" name="status" required>
                                    <option value="ativo" {{ old('status', $pessoa->status) == 'ativo' ? 'selected' : '' }}>Ativo</option>
                                    <option value="inativo" {{ old('status', $pessoa->status) == 'inativo' ? 'selected' : '' }}>Inativo</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Atualizar
                        </button>
                        <a href="{{ route('pessoa.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Máscara para CPF com limite de 11 dígitos
    document.getElementById('cpf').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');

        // Limitar a 11 dígitos
        if (value.length > 11) {
            value = value.substring(0, 11);
        }

        // Aplicar máscara apenas se tiver mais de 3 dígitos
        if (value.length > 3) {
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
        }
        if (value.length > 6) {
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
        }
        if (value.length > 9) {
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        }

        e.target.value = value;
    });

    // Função para validar CPF
    function validarCPF(cpf) {
        cpf = cpf.replace(/\D/g, '');

        if (cpf.length !== 11) return false;
        if (/^(\d)\1{10}$/.test(cpf)) return false;

        let soma = 0;
        for (let i = 0; i < 9; i++) {
            soma += parseInt(cpf[i]) * (10 - i);
        }
        let resto = soma % 11;
        let digito1 = resto < 2 ? 0 : 11 - resto;

        if (parseInt(cpf[9]) !== digito1) return false;

        soma = 0;
        for (let i = 0; i < 10; i++) {
            soma += parseInt(cpf[i]) * (11 - i);
        }
        resto = soma % 11;
        let digito2 = resto < 2 ? 0 : 11 - resto;

        return parseInt(cpf[10]) === digito2;
    }

    // Validar CPF antes do envio
    document.querySelector('form').addEventListener('submit', function(e) {
        const cpfInput = document.getElementById('cpf');
        const cpf = cpfInput.value.replace(/\D/g, '');

        // Atualizar o campo com apenas números antes do envio
        cpfInput.value = cpf;

        if (!validarCPF(cpf)) {
            e.preventDefault();
            alert('O CPF informado é inválido. Verifique os dígitos.');
            return false;
        }
    });
</script>
@endsection