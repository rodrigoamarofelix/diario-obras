@extends('layouts.admin')

@section('title', 'Editar Projeto/Obra - Diário de Obras')

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-edit text-warning"></i>
                        Editar Projeto/Obra
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('diario-obras.dashboard') }}">Diário de Obras</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('diario-obras.projetos.index') }}">Projetos/Obras</a></li>
                        <li class="breadcrumb-item active">Editar</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Editar: {{ $projeto->nome }}</h3>
                        </div>
                        <form action="{{ route('diario-obras.projetos.update', $projeto) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="row">
                                    <!-- Informações Básicas -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nome">Nome do Projeto/Obra *</label>
                                            <input type="text" class="form-control @error('nome') is-invalid @enderror"
                                                   id="nome" name="nome" value="{{ old('nome', $projeto->nome) }}" required>
                                            @error('nome')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="cliente">Cliente *</label>
                                            <input type="text" class="form-control @error('cliente') is-invalid @enderror"
                                                   id="cliente" name="cliente" value="{{ old('cliente', $projeto->cliente) }}" required>
                                            @error('cliente')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="descricao">Descrição</label>
                                            <textarea class="form-control @error('descricao') is-invalid @enderror"
                                                      id="descricao" name="descricao" rows="3">{{ old('descricao', $projeto->descricao) }}</textarea>
                                            @error('descricao')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Endereço -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="cep">CEP</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control @error('cep') is-invalid @enderror"
                                                       id="cep" name="cep" value="{{ old('cep', $projeto->cep) }}"
                                                       placeholder="00000-000" maxlength="9">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-outline-secondary" id="buscar-cep">
                                                        <i class="fas fa-search"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            @error('cep')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="endereco">Endereço *</label>
                                            <input type="text" class="form-control @error('endereco') is-invalid @enderror"
                                                   id="endereco" name="endereco" value="{{ old('endereco', $projeto->endereco) }}" required>
                                            @error('endereco')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="complemento">Complemento</label>
                                            <input type="text" class="form-control @error('complemento') is-invalid @enderror"
                                                   id="complemento" name="complemento" value="{{ old('complemento', $projeto->complemento) }}"
                                                   placeholder="Ex: Qd 5, Lt 10, Nº 123">
                                            @error('complemento')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="bairro">Bairro</label>
                                            <input type="text" class="form-control @error('bairro') is-invalid @enderror"
                                                   id="bairro" name="bairro" value="{{ old('bairro', $projeto->bairro) }}">
                                            @error('bairro')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="cidade">Cidade *</label>
                                            <input type="text" class="form-control @error('cidade') is-invalid @enderror"
                                                   id="cidade" name="cidade" value="{{ old('cidade', $projeto->cidade) }}" required>
                                            @error('cidade')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="estado">Estado *</label>
                                            <select class="form-control @error('estado') is-invalid @enderror"
                                                    id="estado" name="estado" required>
                                                <option value="">Selecione o estado</option>
                                                <option value="AC" {{ old('estado', $projeto->estado) == 'AC' ? 'selected' : '' }}>Acre</option>
                                                <option value="AL" {{ old('estado', $projeto->estado) == 'AL' ? 'selected' : '' }}>Alagoas</option>
                                                <option value="AP" {{ old('estado', $projeto->estado) == 'AP' ? 'selected' : '' }}>Amapá</option>
                                                <option value="AM" {{ old('estado', $projeto->estado) == 'AM' ? 'selected' : '' }}>Amazonas</option>
                                                <option value="BA" {{ old('estado', $projeto->estado) == 'BA' ? 'selected' : '' }}>Bahia</option>
                                                <option value="CE" {{ old('estado', $projeto->estado) == 'CE' ? 'selected' : '' }}>Ceará</option>
                                                <option value="DF" {{ old('estado', $projeto->estado) == 'DF' ? 'selected' : '' }}>Distrito Federal</option>
                                                <option value="ES" {{ old('estado', $projeto->estado) == 'ES' ? 'selected' : '' }}>Espírito Santo</option>
                                                <option value="GO" {{ old('estado', $projeto->estado) == 'GO' ? 'selected' : '' }}>Goiás</option>
                                                <option value="MA" {{ old('estado', $projeto->estado) == 'MA' ? 'selected' : '' }}>Maranhão</option>
                                                <option value="MT" {{ old('estado', $projeto->estado) == 'MT' ? 'selected' : '' }}>Mato Grosso</option>
                                                <option value="MS" {{ old('estado', $projeto->estado) == 'MS' ? 'selected' : '' }}>Mato Grosso do Sul</option>
                                                <option value="MG" {{ old('estado', $projeto->estado) == 'MG' ? 'selected' : '' }}>Minas Gerais</option>
                                                <option value="PA" {{ old('estado', $projeto->estado) == 'PA' ? 'selected' : '' }}>Pará</option>
                                                <option value="PB" {{ old('estado', $projeto->estado) == 'PB' ? 'selected' : '' }}>Paraíba</option>
                                                <option value="PR" {{ old('estado', $projeto->estado) == 'PR' ? 'selected' : '' }}>Paraná</option>
                                                <option value="PE" {{ old('estado', $projeto->estado) == 'PE' ? 'selected' : '' }}>Pernambuco</option>
                                                <option value="PI" {{ old('estado', $projeto->estado) == 'PI' ? 'selected' : '' }}>Piauí</option>
                                                <option value="RJ" {{ old('estado', $projeto->estado) == 'RJ' ? 'selected' : '' }}>Rio de Janeiro</option>
                                                <option value="RN" {{ old('estado', $projeto->estado) == 'RN' ? 'selected' : '' }}>Rio Grande do Norte</option>
                                                <option value="RS" {{ old('estado', $projeto->estado) == 'RS' ? 'selected' : '' }}>Rio Grande do Sul</option>
                                                <option value="RO" {{ old('estado', $projeto->estado) == 'RO' ? 'selected' : '' }}>Rondônia</option>
                                                <option value="RR" {{ old('estado', $projeto->estado) == 'RR' ? 'selected' : '' }}>Roraima</option>
                                                <option value="SC" {{ old('estado', $projeto->estado) == 'SC' ? 'selected' : '' }}>Santa Catarina</option>
                                                <option value="SP" {{ old('estado', $projeto->estado) == 'SP' ? 'selected' : '' }}>São Paulo</option>
                                                <option value="SE" {{ old('estado', $projeto->estado) == 'SE' ? 'selected' : '' }}>Sergipe</option>
                                                <option value="TO" {{ old('estado', $projeto->estado) == 'TO' ? 'selected' : '' }}>Tocantins</option>
                                            </select>
                                            @error('estado')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Informações do Projeto -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="contrato">Número do Contrato</label>
                                            <input type="text" class="form-control @error('contrato') is-invalid @enderror"
                                                   id="contrato" name="contrato" value="{{ old('contrato', $projeto->contrato) }}">
                                            @error('contrato')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="valor_total">Valor Total (R$)</label>
                                            <input type="number" step="0.01" class="form-control @error('valor_total') is-invalid @enderror"
                                                   id="valor_total" name="valor_total" value="{{ old('valor_total', $projeto->valor_total) }}">
                                            @error('valor_total')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="data_inicio">Data de Início *</label>
                                            <input type="date" class="form-control @error('data_inicio') is-invalid @enderror"
                                                   id="data_inicio" name="data_inicio" value="{{ old('data_inicio', $projeto->data_inicio->format('Y-m-d')) }}" required>
                                            @error('data_inicio')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="data_fim_prevista">Data Prevista de Fim</label>
                                            <input type="date" class="form-control @error('data_fim_prevista') is-invalid @enderror"
                                                   id="data_fim_prevista" name="data_fim_prevista" value="{{ old('data_fim_prevista', $projeto->data_fim_prevista ? $projeto->data_fim_prevista->format('Y-m-d') : '') }}">
                                            @error('data_fim_prevista')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="responsavel_id">Responsável *</label>
                                            <select class="form-control @error('responsavel_id') is-invalid @enderror"
                                                    id="responsavel_id" name="responsavel_id" required>
                                                <option value="">Selecione o responsável</option>
                                                @foreach($usuarios as $usuario)
                                                    <option value="{{ $usuario->id }}" {{ old('responsavel_id', $projeto->responsavel_id) == $usuario->id ? 'selected' : '' }}>
                                                        {{ $usuario->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('responsavel_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="status">Status *</label>
                                            <select class="form-control @error('status') is-invalid @enderror"
                                                    id="status" name="status" required>
                                                <option value="planejamento" {{ old('status', $projeto->status) == 'planejamento' ? 'selected' : '' }}>Planejamento</option>
                                                <option value="em_andamento" {{ old('status', $projeto->status) == 'em_andamento' ? 'selected' : '' }}>Em Andamento</option>
                                                <option value="pausado" {{ old('status', $projeto->status) == 'pausado' ? 'selected' : '' }}>Pausado</option>
                                                <option value="concluido" {{ old('status', $projeto->status) == 'concluido' ? 'selected' : '' }}>Concluído</option>
                                                <option value="cancelado" {{ old('status', $projeto->status) == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="prioridade">Prioridade *</label>
                                            <select class="form-control @error('prioridade') is-invalid @enderror"
                                                    id="prioridade" name="prioridade" required>
                                                <option value="baixa" {{ old('prioridade', $projeto->prioridade) == 'baixa' ? 'selected' : '' }}>Baixa</option>
                                                <option value="media" {{ old('prioridade', $projeto->prioridade) == 'media' ? 'selected' : '' }}>Média</option>
                                                <option value="alta" {{ old('prioridade', $projeto->prioridade) == 'alta' ? 'selected' : '' }}>Alta</option>
                                                <option value="urgente" {{ old('prioridade', $projeto->prioridade) == 'urgente' ? 'selected' : '' }}>Urgente</option>
                                            </select>
                                            @error('prioridade')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="observacoes">Observações</label>
                                            <textarea class="form-control @error('observacoes') is-invalid @enderror"
                                                      id="observacoes" name="observacoes" rows="3">{{ old('observacoes', $projeto->observacoes) }}</textarea>
                                            @error('observacoes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save"></i>
                                    Atualizar Projeto/Obra
                                </button>
                                <a href="{{ route('diario-obras.projetos.show', $projeto) }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i>
                                    Voltar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cepInput = document.getElementById('cep');
    const buscarCepBtn = document.getElementById('buscar-cep');
    const enderecoInput = document.getElementById('endereco');
    const complementoInput = document.getElementById('complemento');
    const bairroInput = document.getElementById('bairro');
    const cidadeInput = document.getElementById('cidade');
    const estadoSelect = document.getElementById('estado');

    // Máscara para CEP
    cepInput.addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        value = value.replace(/(\d{5})(\d)/, '$1-$2');
        this.value = value;
    });

    // Função para buscar CEP
    function buscarCEP() {
        const cep = cepInput.value.replace(/\D/g, '');

        if (cep.length !== 8) {
            alert('CEP deve ter 8 dígitos');
            return;
        }

        // Mostrar loading
        buscarCepBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        buscarCepBtn.disabled = true;

        // Buscar CEP via API ViaCEP
        fetch(`https://viacep.com.br/ws/${cep}/json/`)
            .then(response => response.json())
            .then(data => {
                if (data.erro) {
                    alert('CEP não encontrado');
                } else {
                    enderecoInput.value = data.logradouro || '';
                    bairroInput.value = data.bairro || '';
                    cidadeInput.value = data.localidade || '';
                    estadoSelect.value = data.uf || '';

                    // Focar no campo de complemento após preencher endereço
                    if (data.logradouro) {
                        complementoInput.focus();
                    }
                }
            })
            .catch(error => {
                console.error('Erro ao buscar CEP:', error);
                alert('Erro ao buscar CEP. Tente novamente.');
            })
            .finally(() => {
                // Restaurar botão
                buscarCepBtn.innerHTML = '<i class="fas fa-search"></i>';
                buscarCepBtn.disabled = false;
            });
    }

    // Busca de CEP ao clicar no botão
    buscarCepBtn.addEventListener('click', buscarCEP);

    // Buscar CEP automaticamente quando sair do campo com 8 dígitos
    cepInput.addEventListener('blur', function() {
        const cep = this.value.replace(/\D/g, '');
        if (cep.length === 8) {
            buscarCEP();
        }
    });

    // Buscar CEP automaticamente quando digitar 8 dígitos
    cepInput.addEventListener('input', function() {
        const cep = this.value.replace(/\D/g, '');
        if (cep.length === 8) {
            // Aguardar um pouco para o usuário terminar de digitar
            setTimeout(() => {
                if (this.value.replace(/\D/g, '').length === 8) {
                    buscarCEP();
                }
            }, 500);
        }
    });
});
</script>
@endpush
