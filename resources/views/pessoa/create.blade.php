@extends('layouts.admin')

@section('title', 'Nova Pessoa')
@section('page-title', 'Cadastrar Nova Pessoa')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('pessoa.index') }}">Pessoas</a></li>
<li class="breadcrumb-item active">Nova</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Cadastrar Nova Pessoa</h3>
                <div class="card-tools">
                    <a href="{{ route('pessoa.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
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

                <form method="POST" action="{{ route('pessoa.store') }}" id="formPessoa">
                    @csrf

                    <!-- Primeira etapa: Consulta CPF -->
                    <div id="etapa-cpf" class="etapa">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="cpf">CPF *</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control @error('cpf') is-invalid @enderror"
                                               id="cpf" name="cpf" value="{{ old('cpf') }}"
                                               placeholder="000.000.000-00" required>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-primary" id="btnConsultarCpf">
                                                <i class="fas fa-search"></i> Consultar
                                            </button>
                                        </div>
                                    </div>
                                    @error('cpf')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                    <small class="form-text text-muted">
                        Digite o CPF e clique em "Consultar" para verificar na Receita Federal
                        <br><strong>API Oficial:</strong> Sistema integrado com a API CBC (Cadastro Base do Cidadão) da Receita Federal
                        <br><em>Em desenvolvimento: usando dados simulados para testes</em>
                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Resultado da consulta -->
                        <div id="resultado-consulta" class="mt-3" style="display: none;">
                            <div class="alert" id="alert-resultado">
                                <div id="mensagem-resultado"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Segunda etapa: Dados da pessoa (inicialmente oculta) -->
                    <div id="etapa-dados" class="etapa" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nome">Nome Completo *</label>
                                    <input type="text" class="form-control @error('nome') is-invalid @enderror"
                                           id="nome" name="nome" value="{{ old('nome') }}" readonly>
                                    @error('nome')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Nome obtido automaticamente da Receita Federal
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cpf-display">CPF</label>
                                    <input type="text" class="form-control" id="cpf-display" readonly>
                                    <small class="form-text text-muted">
                                        CPF validado na Receita Federal
                                    </small>
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
                                                        {{ old('lotacao_id') == $lotacao->id ? 'selected' : '' }}>
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
                                        <option value="ativo" {{ old('status') == 'ativo' ? 'selected' : 'selected' }}>Ativo</option>
                                        <option value="inativo" {{ old('status') == 'inativo' ? 'selected' : '' }}>Inativo</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           id="email" name="email" value="{{ old('email') }}"
                                           placeholder="email@exemplo.com">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="perfil">Perfil</label>
                                    <select class="form-control @error('perfil') is-invalid @enderror"
                                            id="perfil" name="perfil">
                                        <option value="user" {{ old('perfil') == 'user' ? 'selected' : 'selected' }}>Usuário</option>
                                        <option value="visualizador" {{ old('perfil') == 'visualizador' ? 'selected' : '' }}>Visualizador</option>
                                        <option value="construtor" {{ old('perfil') == 'construtor' ? 'selected' : '' }}>Construtor</option>
                                        <option value="fiscal" {{ old('perfil') == 'fiscal' ? 'selected' : '' }}>Fiscal</option>
                                        <option value="gestor" {{ old('perfil') == 'gestor' ? 'selected' : '' }}>Gestor</option>
                                        @if(auth()->user()->isMaster() || auth()->user()->isAdmin())
                                        <option value="admin" {{ old('perfil') == 'admin' ? 'selected' : '' }}>Administrador</option>
                                        @endif
                                    </select>
                                    @error('perfil')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Senha</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                           id="password" name="password"
                                           placeholder="Deixe em branco para não alterar">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Se preencher, a senha será gerada automaticamente
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Salvar Pessoa
                            </button>
                            <button type="button" class="btn btn-secondary" id="btnVoltarCpf">
                                <i class="fas fa-arrow-left"></i> Voltar para CPF
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let cpfConsultado = null;

    // Máscara para CPF
    document.getElementById('cpf').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');

        if (value.length > 11) {
            value = value.substring(0, 11);
        }

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

    // Consultar CPF
    document.getElementById('btnConsultarCpf').addEventListener('click', function() {
        const cpfInput = document.getElementById('cpf');
        const cpf = cpfInput.value.replace(/\D/g, '');

        if (cpf.length !== 11) {
            mostrarResultado('CPF deve ter 11 dígitos', 'danger');
            return;
        }

        if (!validarCPF(cpf)) {
            mostrarResultado('CPF inválido', 'danger');
            return;
        }

        // Mostrar loading
        const btn = this;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Consultando...';
        btn.disabled = true;

        // Fazer requisição AJAX
        fetch('{{ route("pessoa.consultar-cpf") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ cpf: cpf })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                cpfConsultado = cpf;
                document.getElementById('nome').value = data.nome;
                document.getElementById('cpf-display').value = formatarCPF(cpf);

                mostrarResultado(data.message, 'success');

                // Mostrar segunda etapa após 1 segundo
                setTimeout(() => {
                    document.getElementById('etapa-cpf').style.display = 'none';
                    document.getElementById('etapa-dados').style.display = 'block';
                }, 1000);
            } else {
                if (data.permite_cadastro_pendente) {
                    // API fora - permite cadastro pendente
                    cpfConsultado = cpf; // Definir como consultado para permitir salvamento
                    mostrarResultado(data.message + ' - Cadastro será realizado com status pendente.', 'warning');

                    // Mostrar segunda etapa após 2 segundos
                    setTimeout(() => {
                        document.getElementById('etapa-cpf').style.display = 'none';
                        document.getElementById('etapa-dados').style.display = 'block';
                        document.getElementById('nome').readOnly = false;
                        document.getElementById('nome').placeholder = 'Digite o nome completo';
                        document.getElementById('cpf-display').value = formatarCPF(cpf);

                        // Definir status como pendente e tornar readonly
                        document.getElementById('status').value = 'pendente';
                        document.getElementById('status').readOnly = true;
                        document.getElementById('status').style.backgroundColor = '#f8f9fa';
                        document.getElementById('status').style.cursor = 'not-allowed';

                        // Mostrar aviso sobre cadastro pendente
                        const aviso = document.createElement('div');
                        aviso.className = 'alert alert-warning mt-3';
                        aviso.innerHTML = '<i class="fas fa-exclamation-triangle"></i> <strong>Atenção:</strong> Este cadastro será realizado com status pendente e será validado posteriormente.';
                        document.getElementById('etapa-dados').insertBefore(aviso, document.getElementById('etapa-dados').firstChild);
                    }, 2000);
                } else {
                    mostrarResultado(data.message, 'danger');
                }
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            mostrarResultado('Erro ao consultar CPF. Tente novamente.', 'danger');
        })
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    });

    // Voltar para etapa do CPF
    document.getElementById('btnVoltarCpf').addEventListener('click', function() {
        document.getElementById('etapa-dados').style.display = 'none';
        document.getElementById('etapa-cpf').style.display = 'block';
        document.getElementById('resultado-consulta').style.display = 'none';

        // Reabilitar campos
        document.getElementById('nome').readOnly = true;
        document.getElementById('nome').placeholder = '';
        document.getElementById('status').readOnly = false;
        document.getElementById('status').style.backgroundColor = '';
        document.getElementById('status').style.cursor = '';

        // Remover aviso se existir
        const aviso = document.querySelector('.alert-warning');
        if (aviso) {
            aviso.remove();
        }

        cpfConsultado = null;
    });

    // Função para mostrar resultado
    function mostrarResultado(mensagem, tipo) {
        const resultado = document.getElementById('resultado-consulta');
        const alert = document.getElementById('alert-resultado');
        const mensagemDiv = document.getElementById('mensagem-resultado');

        alert.className = `alert alert-${tipo}`;
        mensagemDiv.innerHTML = mensagem;
        resultado.style.display = 'block';
    }

    // Função para formatar CPF
    function formatarCPF(cpf) {
        return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
    }

    // Função para validar CPF
    function validarCPF(cpf) {
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

    // Validar antes do envio
    document.getElementById('formPessoa').addEventListener('submit', function(e) {
        // Se não consultou CPF, não permite envio
        if (!cpfConsultado) {
            e.preventDefault();
            alert('É necessário consultar o CPF antes de salvar.');
            return false;
        }

        // Garantir que o CPF seja enviado apenas com números
        document.getElementById('cpf').value = cpfConsultado;

        // Se o campo nome estiver readonly (CPF validado), não precisa validar
        // Se estiver editável (cadastro pendente), validar se preenchido
        const nomeInput = document.getElementById('nome');
        if (!nomeInput.readOnly && !nomeInput.value.trim()) {
            e.preventDefault();
            alert('É necessário preencher o nome completo.');
            return false;
        }
    });

    // Permitir consulta com Enter
    document.getElementById('cpf').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('btnConsultarCpf').click();
        }
    });
</script>
@endsection