@extends('layouts.admin')

@section('title', 'Editar Medição')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit"></i> Editar Medição
                    </h3>
                </div>
                <form action="{{ route('medicao.update', $medicao->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if($medicao->status === 'pendente')
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Atenção:</strong> Esta medição está pendente e pode ser editada.
                                <strong>Após alterar o status para "Aprovado" ou "Rejeitado", não será mais possível editá-la.</strong>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="catalogo_id">Catálogo:</label>
                                    <select class="form-control" id="catalogo_id" name="catalogo_id" required>
                                        <option value="">Selecione o Catálogo</option>
                                        @foreach($catalogos as $catalogo)
                                            <option value="{{ $catalogo->id }}"
                                                    data-valor="{{ $catalogo->valor_unitario }}"
                                                    data-unidade="{{ $catalogo->unidade_medida }}"
                                                    {{ old('catalogo_id', $medicao->catalogo_id) == $catalogo->id ? 'selected' : '' }}>
                                                {{ $catalogo->nome }} - {{ $catalogo->codigo }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contrato_id">Contrato:</label>
                                    <select class="form-control" id="contrato_id" name="contrato_id" required>
                                        <option value="">Selecione o Contrato</option>
                                        @foreach($contratos as $contrato)
                                            <option value="{{ $contrato->id }}" {{ old('contrato_id', $medicao->contrato_id) == $contrato->id ? 'selected' : '' }}>
                                                {{ $contrato->numero }} - {{ $contrato->descricao }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lotacao_id">Lotação:</label>
                                    <select class="form-control" id="lotacao_id" name="lotacao_id" required>
                                        <option value="">Selecione a Lotação</option>
                                        @foreach($lotacoes as $lotacao)
                                            <option value="{{ $lotacao->id }}" {{ old('lotacao_id', $medicao->lotacao_id) == $lotacao->id ? 'selected' : '' }}>
                                                {{ $lotacao->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="numero_medicao">Número da Medição:</label>
                                    <input type="text" class="form-control" id="numero_medicao" name="numero_medicao"
                                           value="{{ old('numero_medicao', $medicao->numero_medicao) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="data_medicao">Data da Medição:</label>
                                    <input type="date" class="form-control" id="data_medicao" name="data_medicao"
                                           value="{{ old('data_medicao', is_object($medicao->data_medicao) ? $medicao->data_medicao->format('Y-m-d') : $medicao->data_medicao) }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="quantidade">Quantidade:</label>
                                    <input type="number" class="form-control" id="quantidade" name="quantidade"
                                           step="0.001" min="0.001" value="{{ old('quantidade', $medicao->quantidade) }}" required>
                                    <small class="form-text text-muted" id="unidade-text"></small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="valor_unitario">Valor Unitário:</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">R$</span>
                                        </div>
                                        <input type="number" class="form-control" id="valor_unitario" name="valor_unitario"
                                               step="0.01" min="0" value="{{ old('valor_unitario', $medicao->valor_unitario) }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status:</label>
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="pendente" {{ old('status', $medicao->status) == 'pendente' ? 'selected' : '' }}>Pendente</option>
                                        <option value="aprovado" {{ old('status', $medicao->status) == 'aprovado' ? 'selected' : '' }}>Aprovado</option>
                                        <option value="rejeitado" {{ old('status', $medicao->status) == 'rejeitado' ? 'selected' : '' }}>Rejeitado</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="valor_total">Valor Total:</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">R$</span>
                                        </div>
                                        <input type="text" class="form-control" id="valor_total" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="observacoes">Observações:</label>
                            <textarea class="form-control" id="observacoes" name="observacoes" rows="3">{{ old('observacoes', $medicao->observacoes) }}</textarea>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Atualizar
                        </button>
                        <a href="{{ route('medicao.show', $medicao) }}" class="btn btn-secondary">
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
    // Atualizar valor unitário e unidade quando catálogo for selecionado
    document.getElementById('catalogo_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const valorUnitario = selectedOption.getAttribute('data-valor');
        const unidade = selectedOption.getAttribute('data-unidade');

        if (valorUnitario) {
            document.getElementById('valor_unitario').value = valorUnitario;
            document.getElementById('unidade-text').textContent = 'Unidade: ' + unidade;
            calcularValorTotal();
        } else {
            document.getElementById('valor_unitario').value = '';
            document.getElementById('unidade-text').textContent = '';
        }
    });

    // Calcular valor total automaticamente
    function calcularValorTotal() {
        const quantidade = parseFloat(document.getElementById('quantidade').value) || 0;
        const valorUnitario = parseFloat(document.getElementById('valor_unitario').value) || 0;
        const valorTotal = quantidade * valorUnitario;

        document.getElementById('valor_total').value = valorTotal.toFixed(2);
    }

    // Event listeners para calcular valor total
    document.getElementById('quantidade').addEventListener('input', calcularValorTotal);
    document.getElementById('valor_unitario').addEventListener('input', calcularValorTotal);

    // Calcular valor total ao carregar a página
    document.addEventListener('DOMContentLoaded', function() {
        calcularValorTotal();
    });
</script>
@endsection


