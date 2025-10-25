@extends('layouts.admin')

@section('title', 'Materiais - Diário de Obras')

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-boxes text-info"></i>
                        Materiais de Obra
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('diario-obras.dashboard') }}">Diário de Obras</a></li>
                        <li class="breadcrumb-item active">Materiais</li>
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
                    <a href="{{ route('diario-obras.materiais.create') }}" class="btn btn-info">
                        <i class="fas fa-plus"></i>
                        Registrar Material
                    </a>
                </div>
            </div>

            <!-- Lista de Materiais -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-list text-primary"></i>
                                Movimentação de Materiais
                            </h3>
                        </div>
                        <div class="card-body">
                            @if($materiais->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Material</th>
                                                <th>Projeto</th>
                                                <th>Quantidade</th>
                                                <th>Valor</th>
                                                <th>Tipo</th>
                                                <th>Data</th>
                                                <th>Fornecedor</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($materiais as $material)
                                            <tr>
                                                <td>
                                                    <strong>{{ $material->nome_material }}</strong>
                                                    @if($material->descricao)
                                                        <br><small class="text-muted">{{ Str::limit($material->descricao, 30) }}</small>
                                                    @endif
                                                </td>
                                                <td>{{ $material->projeto->nome ?? 'N/A' }}</td>
                                                <td>
                                                    {{ number_format($material->quantidade, 3) }}
                                                    <small class="text-muted">{{ $material->unidade_medida }}</small>
                                                </td>
                                                <td>
                                                    @if($material->valor_total)
                                                        R$ {{ number_format($material->valor_total, 2, ',', '.') }}
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge badge-{{
                                                        $material->tipo_movimento == 'entrada' ? 'success' :
                                                        ($material->tipo_movimento == 'saida' ? 'danger' : 'warning')
                                                    }}">
                                                        {{ ucfirst($material->tipo_movimento) }}
                                                    </span>
                                                </td>
                                                <td>{{ $material->data_movimento->format('d/m/Y') }}</td>
                                                <td>{{ $material->fornecedor ?? 'N/A' }}</td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="{{ route('diario-obras.materiais.show', $material) }}" class="btn btn-sm btn-primary" title="Ver detalhes">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('diario-obras.materiais.edit', $material) }}" class="btn btn-sm btn-warning" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('diario-obras.materiais.destroy', $material) }}" method="POST" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja excluir este material?')">
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
                                    {{ $materiais->links() }}
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                                    <h4 class="text-muted">Nenhum material registrado</h4>
                                    <p class="text-muted">Comece registrando a movimentação de materiais.</p>
                                    <a href="{{ route('diario-obras.materiais.create') }}" class="btn btn-info">
                                        <i class="fas fa-plus"></i>
                                        Registrar Primeiro Material
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

