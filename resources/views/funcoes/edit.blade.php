@extends('layouts.admin')

@section('title', 'Editar Função - Sistema')

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-edit text-warning"></i>
                        Editar Função
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('funcoes.index') }}">Funções</a></li>
                        <li class="breadcrumb-item active">Editar Função</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-edit text-warning"></i>
                                Editar Função: {{ $funcao->nome }}
                            </h3>
                        </div>

                        <form action="{{ route('funcoes.update', $funcao->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <!-- Nome da Função -->
                                <div class="form-group">
                                    <label for="nome" class="required">Nome da Função</label>
                                    <input type="text"
                                           class="form-control @error('nome') is-invalid @enderror"
                                           id="nome"
                                           name="nome"
                                           value="{{ old('nome', $funcao->nome) }}"
                                           placeholder="Ex: Pedreiro, Eletricista, Engenheiro..."
                                           required>
                                    @error('nome')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Nome único da função que será utilizada no sistema
                                    </small>
                                </div>

                                <!-- Categoria -->
                                <div class="form-group">
                                    <label for="categoria" class="required">Categoria</label>
                                    <select class="form-control @error('categoria') is-invalid @enderror"
                                            id="categoria"
                                            name="categoria"
                                            required>
                                        <option value="">Selecione uma categoria</option>
                                        <option value="construcao" {{ old('categoria', $funcao->categoria) == 'construcao' ? 'selected' : '' }}>
                                            Construção
                                        </option>
                                        <option value="tecnica" {{ old('categoria', $funcao->categoria) == 'tecnica' ? 'selected' : '' }}>
                                            Técnica
                                        </option>
                                        <option value="supervisao" {{ old('categoria', $funcao->categoria) == 'supervisao' ? 'selected' : '' }}>
                                            Supervisão
                                        </option>
                                        <option value="administrativa" {{ old('categoria', $funcao->categoria) == 'administrativa' ? 'selected' : '' }}>
                                            Administrativa
                                        </option>
                                        <option value="outros" {{ old('categoria', $funcao->categoria) == 'outros' ? 'selected' : '' }}>
                                            Outros
                                        </option>
                                    </select>
                                    @error('categoria')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Categoria que melhor define esta função
                                    </small>
                                </div>

                                <!-- Descrição -->
                                <div class="form-group">
                                    <label for="descricao">Descrição</label>
                                    <textarea class="form-control @error('descricao') is-invalid @enderror"
                                              id="descricao"
                                              name="descricao"
                                              rows="4"
                                              placeholder="Descreva as principais responsabilidades e atividades desta função...">{{ old('descricao', $funcao->descricao) }}</textarea>
                                    @error('descricao')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Descrição detalhada das responsabilidades desta função (opcional)
                                    </small>
                                </div>

                                <!-- Status -->
                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox"
                                               class="form-check-input @error('ativo') is-invalid @enderror"
                                               id="ativo"
                                               name="ativo"
                                               value="1"
                                               {{ old('ativo', $funcao->ativo) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="ativo">
                                            Função Ativa
                                        </label>
                                    </div>
                                    @error('ativo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Funções ativas podem ser utilizadas em novos cadastros
                                    </small>
                                </div>
                            </div>

                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-md-6">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-save"></i>
                                            Salvar Alterações
                                        </button>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <a href="{{ route('funcoes.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left"></i>
                                            Voltar
                                        </a>
                                        <a href="{{ route('funcoes.show', $funcao->id) }}" class="btn btn-info">
                                            <i class="fas fa-eye"></i>
                                            Ver Detalhes
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Sidebar com informações -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle text-info"></i>
                                Informações da Função
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <strong>Status:</strong><br>
                                    <span class="badge badge-{{ $funcao->ativo ? 'success' : 'secondary' }}">
                                        {{ $funcao->ativo ? 'Ativa' : 'Inativa' }}
                                    </span>
                                </div>
                                <div class="col-6">
                                    <strong>Categoria:</strong><br>
                                    <span class="badge badge-info">{{ ucfirst($funcao->categoria) }}</span>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-6">
                                    <strong>Pessoas:</strong><br>
                                    <span class="badge badge-primary">{{ $funcao->pessoas->count() }}</span>
                                </div>
                                <div class="col-6">
                                    <strong>Criada em:</strong><br>
                                    <small>{{ is_object($funcao->created_at) ? $funcao->created_at->format('d/m/Y H:i') : ($funcao->created_at ?? 'N/A') }}</small>
                                </div>
                            </div>

                            @if($funcao->updated_at != $funcao->created_at)
                            <hr>
                            <div class="row">
                                <div class="col-12">
                                    <strong>Última atualização:</strong><br>
                                    <small>{{ is_object($funcao->updated_at) ? $funcao->updated_at->format('d/m/Y H:i') : ($funcao->updated_at ?? 'N/A') }}</small>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Ações rápidas -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-bolt text-warning"></i>
                                Ações Rápidas
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="btn-group-vertical w-100">
                                <form action="{{ route('funcoes.toggle-status', $funcao->id) }}" method="GET" class="mb-2">
                                    <button type="submit" class="btn btn-{{ $funcao->ativo ? 'secondary' : 'success' }} btn-block">
                                        <i class="fas fa-{{ $funcao->ativo ? 'pause' : 'play' }}"></i>
                                        {{ $funcao->ativo ? 'Inativar' : 'Ativar' }} Função
                                    </button>
                                </form>

                                <a href="{{ route('funcoes.show', $funcao->id) }}" class="btn btn-info btn-block mb-2">
                                    <i class="fas fa-eye"></i>
                                    Ver Detalhes
                                </a>

                                <form action="{{ route('funcoes.destroy', $funcao->id) }}" method="POST"
                                      onsubmit="return confirm('Tem certeza que deseja excluir esta função? Ela poderá ser restaurada posteriormente.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-block">
                                        <i class="fas fa-trash"></i>
                                        Excluir Função
                                    </button>
                                </form>
                            </div>
                        </div>
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
    // Validação em tempo real do nome
    const nomeInput = document.getElementById('nome');
    nomeInput.addEventListener('input', function() {
        const value = this.value.trim();
        if (value.length > 0) {
            this.classList.remove('is-invalid');
        }
    });

    // Validação da categoria
    const categoriaSelect = document.getElementById('categoria');
    categoriaSelect.addEventListener('change', function() {
        if (this.value) {
            this.classList.remove('is-invalid');
        }
    });

    // Auto-capitalização do nome
    nomeInput.addEventListener('input', function() {
        const words = this.value.split(' ');
        const capitalizedWords = words.map(word => {
            if (word.length > 0) {
                return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
            }
            return word;
        });
        this.value = capitalizedWords.join(' ');
    });
});
</script>
@endpush




