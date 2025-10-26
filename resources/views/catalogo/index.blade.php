@extends('layouts.admin')

@section('title', 'Catálogos')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list"></i> Catálogos
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('catalogo.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Novo Catálogo
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Código</th>
                                    <th>Valor Unitário</th>
                                    <th>Unidade</th>
                                    <th>Status</th>
                                    <th>Criado em</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($catalogos as $catalogo)
                                    <tr class="{{ $catalogo->trashed() ? 'table-secondary' : '' }}">
                                        <td>{{ $catalogo->id }}</td>
                                        <td>{{ $catalogo->nome }}</td>
                                        <td>
                                            <span class="badge badge-info">{{ $catalogo->codigo }}</span>
                                        </td>
                                        <td>{{ $catalogo->valor_unitario_formatado }}</td>
                                        <td>{{ $catalogo->unidade_medida }}</td>
                                        <td>
                                            <span class="badge badge-{{ $catalogo->status == 'ativo' ? 'success' : 'secondary' }}">
                                                {{ $catalogo->status_name }}
                                            </span>
                                        </td>
                                        <td>{{ is_object($catalogo->created_at) ? $catalogo->created_at->format('d/m/Y H:i') : ($catalogo->created_at ?? 'N/A') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                @if($catalogo->trashed())
                                                    <a href="{{ route('catalogo.restore', $catalogo->id) }}"
                                                       class="btn btn-warning btn-sm"
                                                       onclick="return confirm('Deseja restaurar este catálogo?')">
                                                        <i class="fas fa-undo"></i>
                                                    </a>
                                                @else
                                                    <a href="{{ route('catalogo.show', $catalogo) }}"
                                                       class="btn btn-info btn-sm">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('catalogo.edit', $catalogo) }}"
                                                       class="btn btn-warning btn-sm">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('catalogo.destroy', $catalogo) }}"
                                                          method="POST"
                                                          style="display: inline-block;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="btn btn-danger btn-sm"
                                                                onclick="return confirm('Deseja excluir este catálogo?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Nenhum catálogo encontrado.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


