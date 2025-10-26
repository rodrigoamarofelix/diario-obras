@extends('layouts.admin')

@section('title', 'Detalhes da Empresa')

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-building text-primary"></i>
                        {{ $empresa->nome }}
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('empresas.index') }}">Empresas</a></li>
                        <li class="breadcrumb-item active">Detalhes</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
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
                            <dl class="row">
                                <dt class="col-sm-4">Nome Fantasia:</dt>
                                <dd class="col-sm-8"><strong>{{ $empresa->nome }}</strong></dd>

                                <dt class="col-sm-4">Razão Social:</dt>
                                <dd class="col-sm-8">{{ $empresa->razao_social }}</dd>

                                <dt class="col-sm-4">CNPJ:</dt>
                                <dd class="col-sm-8">{{ $empresa->cnpj_formatado }}</dd>

                                <dt class="col-sm-4">Email:</dt>
                                <dd class="col-sm-8">
                                    @if($empresa->email)
                                        <a href="mailto:{{ $empresa->email }}">{{ $empresa->email }}</a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </dd>

                                <dt class="col-sm-4">Site:</dt>
                                <dd class="col-sm-8">
                                    @if($empresa->site)
                                        <a href="{{ $empresa->site }}" target="_blank">{{ $empresa->site }}</a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </dd>

                                <dt class="col-sm-4">Status:</dt>
                                <dd class="col-sm-8">
                                    <span class="badge badge-{{ $empresa->ativo ? 'success' : 'secondary' }}">
                                        {{ $empresa->ativo ? 'Ativa' : 'Inativa' }}
                                    </span>
                                </dd>
                            </dl>
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
                            <dl class="row">
                                <dt class="col-sm-4">Telefone:</dt>
                                <dd class="col-sm-8">
                                    @if($empresa->telefone)
                                        <a href="tel:{{ $empresa->telefone }}">{{ $empresa->telefone_formatado }}</a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </dd>

                                <dt class="col-sm-4">WhatsApp:</dt>
                                <dd class="col-sm-8">
                                    @if($empresa->whatsapp)
                                        <a href="https://wa.me/55{{ $empresa->whatsapp }}" target="_blank" class="text-success">
                                            <i class="fab fa-whatsapp"></i> {{ $empresa->whatsapp_formatado }}
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </dd>
                            </dl>
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
                            <dl class="row">
                                <dt class="col-sm-2">CEP:</dt>
                                <dd class="col-sm-4">{{ $empresa->cep_formatado }}</dd>

                                <dt class="col-sm-2">Endereço:</dt>
                                <dd class="col-sm-4">{{ $empresa->endereco }}</dd>

                                <dt class="col-sm-2">Número:</dt>
                                <dd class="col-sm-4">{{ $empresa->numero ?? 'N/A' }}</dd>

                                <dt class="col-sm-2">Complemento:</dt>
                                <dd class="col-sm-4">{{ $empresa->complemento ?? 'N/A' }}</dd>

                                <dt class="col-sm-2">Bairro:</dt>
                                <dd class="col-sm-4">{{ $empresa->bairro }}</dd>

                                <dt class="col-sm-2">Cidade:</dt>
                                <dd class="col-sm-4">{{ $empresa->cidade }}</dd>

                                <dt class="col-sm-2">Estado:</dt>
                                <dd class="col-sm-4">{{ $empresa->estado }}</dd>

                                <dt class="col-sm-2">País:</dt>
                                <dd class="col-sm-4">{{ $empresa->pais }}</dd>
                            </dl>

                            <div class="mt-3">
                                <strong>Endereço Completo:</strong>
                                <p class="text-muted">{{ $empresa->endereco_completo }}</p>
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
                            @if($empresa->observacoes)
                                <p>{{ $empresa->observacoes }}</p>
                            @else
                                <p class="text-muted">Nenhuma observação registrada.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informações do Sistema -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info text-secondary"></i>
                                Informações do Sistema
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Criado por:</strong>
                                    <p class="text-muted">{{ $empresa->criadoPor->name ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-3">
                                    <strong>Data de Criação:</strong>
                                    <p class="text-muted">{{ is_object($empresa->created_at) ? $empresa->created_at->format('d/m/Y H:i') : ($empresa->created_at ?? 'N/A') }}</p>
                                </div>
                                <div class="col-md-3">
                                    <strong>Última Atualização:</strong>
                                    <p class="text-muted">{{ is_object($empresa->updated_at) ? $empresa->updated_at->format('d/m/Y H:i') : ($empresa->updated_at ?? 'N/A') }}</p>
                                </div>
                                <div class="col-md-3">
                                    <strong>Projetos:</strong>
                                    <p class="text-muted">{{ $empresa->projetos->count() }} projeto(s)</p>
                                </div>
                                <div class="col-md-3">
                                    <strong>Status:</strong>
                                    <p class="text-muted">
                                        @if($empresa->trashed())
                                            <span class="badge badge-danger">Excluída</span>
                                        @else
                                            <span class="badge badge-{{ $empresa->ativo ? 'success' : 'secondary' }}">
                                                {{ $empresa->ativo ? 'Ativa' : 'Inativa' }}
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Projetos Relacionados -->
            @if($empresa->projetos->count() > 0)
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-building text-primary"></i>
                                Projetos Relacionados ({{ $empresa->projetos->count() }})
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Projeto</th>
                                            <th>Tipo de Participação</th>
                                            <th>Status</th>
                                            <th>Cliente</th>
                                            <th>Valor</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($empresa->projetos as $projeto)
                                        <tr>
                                            <td>
                                                <strong>{{ $projeto->nome }}</strong>
                                                @if($projeto->descricao)
                                                    <br><small class="text-muted">{{ Str::limit($projeto->descricao, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-info">
                                                    {{ ucfirst(str_replace('_', ' ', $projeto->pivot->tipo_participacao)) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{
                                                    $projeto->status == 'em_andamento' ? 'success' :
                                                    ($projeto->status == 'planejamento' ? 'warning' :
                                                    ($projeto->status == 'concluido' ? 'info' :
                                                    ($projeto->status == 'pausado' ? 'secondary' : 'danger')))
                                                }}">
                                                    {{ ucfirst(str_replace('_', ' ', $projeto->status)) }}
                                                </span>
                                            </td>
                                            <td>{{ $projeto->cliente }}</td>
                                            <td>
                                                @if($projeto->valor_total)
                                                    R$ {{ number_format($projeto->valor_total, 2, ',', '.') }}
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('diario-obras.projetos.show', $projeto) }}"
                                                       class="btn btn-sm btn-primary" title="Ver projeto">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('diario-obras.projetos.edit', $projeto) }}"
                                                       class="btn btn-sm btn-warning" title="Editar projeto">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Ações -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center">
                            <a href="{{ route('empresas.index') }}" class="btn btn-secondary mr-2">
                                <i class="fas fa-arrow-left"></i> Voltar
                            </a>
                            @if($empresa->trashed())
                                {{-- Empresa excluída - mostrar opções de restauração/exclusão permanente --}}
                                <form method="POST" action="{{ route('empresas.restore', $empresa->id) }}" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success mr-2"
                                            onclick="return confirm('Tem certeza que deseja restaurar esta empresa?')">
                                        <i class="fas fa-undo"></i> Restaurar Empresa
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('empresas.force-delete', $empresa->id) }}" class="d-inline"
                                      onsubmit="return confirm('Tem certeza que deseja excluir PERMANENTEMENTE esta empresa? Esta ação não pode ser desfeita!')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash"></i> Excluir Permanentemente
                                    </button>
                                </form>
                            @else
                                {{-- Empresa ativa - mostrar opções normais --}}
                                <a href="{{ route('empresas.edit', $empresa) }}" class="btn btn-warning mr-2">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <form action="{{ route('empresas.toggle-status', $empresa) }}" method="GET" class="d-inline">
                                    <button type="submit"
                                            class="btn btn-{{ $empresa->ativo ? 'secondary' : 'success' }} mr-2"
                                            onclick="return confirm('Tem certeza que deseja {{ $empresa->ativo ? 'inativar' : 'ativar' }} esta empresa?')">
                                        <i class="fas fa-{{ $empresa->ativo ? 'pause' : 'play' }}"></i>
                                        {{ $empresa->ativo ? 'Inativar' : 'Ativar' }}
                                    </button>
                                </form>
                                <form action="{{ route('empresas.destroy', $empresa) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Tem certeza que deseja excluir esta empresa? Ela poderá ser restaurada posteriormente.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash"></i> Excluir (Soft Delete)
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
