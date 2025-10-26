@extends('layouts.admin')

@section('title', 'Atividades - Diário de Obras')

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-tasks text-success"></i>
                        Atividades de Obra
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('diario-obras.dashboard') }}">Diário de Obras</a></li>
                        <li class="breadcrumb-item active">Atividades</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Ações -->
            <div class="row mb-3">
                <div class="col-12">
                    <a href="{{ route('diario-obras.atividades.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i>
                        Nova Atividade
                    </a>
                </div>
            </div>

            <!-- Lista de Atividades -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-list text-primary"></i>
                                Lista de Atividades
                            </h3>
                        </div>
                        <div class="card-body">
                            @if($atividades->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Título</th>
                                                <th>Projeto</th>
                                                <th>Data</th>
                                                <th>Tipo</th>
                                                <th>Status</th>
                                                <th>Responsável</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($atividades as $atividade)
                                            <tr>
                                                <td>
                                                    <strong>{{ $atividade->titulo }}</strong>
                                                    @if($atividade->descricao)
                                                        <br><small class="text-muted">{{ Str::limit($atividade->descricao, 50) }}</small>
                                                    @endif
                                                </td>
                                                <td>{{ $atividade->projeto->nome ?? 'N/A' }}</td>
                                                <td>{{ $atividade->data_atividade->format('d/m/Y') }}</td>
                                                <td>
                                                    <span class="badge badge-info">
                                                        {{ ucfirst($atividade->tipo) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-{{
                                                        $atividade->status == 'concluido' ? 'success' :
                                                        ($atividade->status == 'em_andamento' ? 'warning' :
                                                        ($atividade->status == 'planejado' ? 'secondary' : 'danger'))
                                                    }}">
                                                        {{ ucfirst(str_replace('_', ' ', $atividade->status)) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($atividade->responsavel)
                                                        {{ $atividade->responsavel->nome }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="{{ route('diario-obras.atividades.show', $atividade) }}" class="btn btn-sm btn-primary" title="Ver detalhes">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('diario-obras.atividades.edit', $atividade) }}" class="btn btn-sm btn-warning" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('diario-obras.atividades.destroy', $atividade) }}" method="POST" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja excluir esta atividade?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" title="Excluir">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Paginação -->
                                <div class="d-flex justify-content-center">
                                    {{ $atividades->links() }}
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                                    <h4 class="text-muted">Nenhuma atividade registrada</h4>
                                    <p class="text-muted">Comece registrando as atividades da obra.</p>
                                    <a href="{{ route('diario-obras.atividades.create') }}" class="btn btn-success">
                                        <i class="fas fa-plus"></i>
                                        Registrar Primeira Atividade
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

