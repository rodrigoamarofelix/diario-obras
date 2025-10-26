@extends('layouts.admin')

@section('title', 'Nova Função - Sistema')

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-briefcase text-warning"></i>
                        Nova Função
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('funcoes.index') }}">Funções</a></li>
                        <li class="breadcrumb-item active">Nova Função</li>
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
                                <i class="fas fa-plus text-success"></i>
                                Cadastrar Nova Função
                            </h3>
                        </div>

                        <form action="{{ route('funcoes.store') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <!-- Nome da Função -->
                                <div class="form-group">
                                    <label for="nome" class="required">Nome da Função</label>
                                    <input type="text"
                                           class="form-control @error('nome') is-invalid @enderror"
                                           id="nome"
                                           name="nome"
                                           value="{{ old('nome') }}"
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
                                        <option value="construcao" {{ old('categoria') == 'construcao' ? 'selected' : '' }}>
                                            Construção
                                        </option>
                                        <option value="tecnica" {{ old('categoria') == 'tecnica' ? 'selected' : '' }}>
                                            Técnica
                                        </option>
                                        <option value="supervisao" {{ old('categoria') == 'supervisao' ? 'selected' : '' }}>
                                            Supervisão
                                        </option>
                                        <option value="administrativa" {{ old('categoria') == 'administrativa' ? 'selected' : '' }}>
                                            Administrativa
                                        </option>
                                        <option value="outros" {{ old('categoria') == 'outros' ? 'selected' : '' }}>
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
                                              placeholder="Descreva as principais responsabilidades e atividades desta função...">{{ old('descricao') }}</textarea>
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
                                               {{ old('ativo', true) ? 'checked' : '' }}>
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
                                            Salvar Função
                                        </button>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <a href="{{ route('funcoes.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left"></i>
                                            Voltar
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
                                Informações
                            </h3>
                        </div>
                        <div class="card-body">
                            <h5>Categorias Disponíveis:</h5>
                            <ul class="list-unstyled">
                                <li><span class="badge badge-primary">Construção</span> - Funções relacionadas à construção civil</li>
                                <li><span class="badge badge-info">Técnica</span> - Funções técnicas especializadas</li>
                                <li><span class="badge badge-warning">Supervisão</span> - Funções de supervisão e coordenação</li>
                                <li><span class="badge badge-secondary">Administrativa</span> - Funções administrativas</li>
                                <li><span class="badge badge-dark">Outros</span> - Outras funções não categorizadas</li>
                            </ul>

                            <hr>

                            <h5>Dicas:</h5>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success"></i> Use nomes claros e específicos</li>
                                <li><i class="fas fa-check text-success"></i> Evite duplicatas de nomes</li>
                                <li><i class="fas fa-check text-success"></i> Descreva as responsabilidades quando necessário</li>
                                <li><i class="fas fa-check text-success"></i> Mantenha apenas funções ativas em uso</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Exemplos de funções -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-lightbulb text-warning"></i>
                                Exemplos
                            </h3>
                        </div>
                        <div class="card-body">
                            <h6>Construção:</h6>
                            <ul class="list-unstyled">
                                <li>• Pedreiro</li>
                                <li>• Eletricista</li>
                                <li>• Encanador</li>
                                <li>• Pintor</li>
                                <li>• Carpinteiro</li>
                                <li>• Ajudante</li>
                            </ul>

                            <h6>Técnica:</h6>
                            <ul class="list-unstyled">
                                <li>• Engenheiro</li>
                                <li>• Arquiteto</li>
                                <li>• Técnico em Segurança</li>
                            </ul>

                            <h6>Supervisão:</h6>
                            <ul class="list-unstyled">
                                <li>• Supervisor</li>
                                <li>• Coordenador</li>
                                <li>• Mestre de Obras</li>
                            </ul>
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




