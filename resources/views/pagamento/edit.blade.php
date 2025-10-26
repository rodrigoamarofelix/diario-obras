@extends('layouts.admin')

@section('title', 'Editar Pagamento')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit"></i> Editar Pagamento
                    </h3>
                </div>
                <form action="{{ route('pagamento.update', $pagamento->id) }}" method="POST">
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

                        @if($pagamento->status === 'pendente')
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Atenção:</strong> Este pagamento está pendente e pode ser editado.
                                <strong>Após alterar o status para "Aprovado", "Rejeitado" ou "Pago", não será mais possível editá-lo.</strong>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="medicao_id">Medição:</label>
                                    <select class="form-control" id="medicao_id" name="medicao_id" required>
                                        <option value="">Selecione a Medição</option>
                                        @foreach($medicoes as $medicao)
                                            <option value="{{ $medicao->id }}"
                                                    data-valor="{{ $medicao->valor_total }}"
                                                    data-catalogo="{{ $medicao->catalogo->nome }}"
                                                    data-contrato="{{ $medicao->contrato->numero }}"
                                                    data-lotacao="{{ $medicao->lotacao->nome }}"
                                                    {{ old('medicao_id', $pagamento->medicao_id) == $medicao->id ? 'selected' : '' }}>
                                                {{ $medicao->numero_medicao }} - {{ $medicao->catalogo->nome }} ({{ $medicao->contrato->numero }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> Apenas medições com status "Aprovado" e que ainda não foram pagas são exibidas
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="numero_pagamento">Número do Pagamento:</label>
                                    <input type="text" class="form-control" id="numero_pagamento" name="numero_pagamento"
                                           value="{{ old('numero_pagamento', $pagamento->numero_pagamento) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="data_pagamento">Data do Pagamento:</label>
                                    <input type="date" class="form-control" id="data_pagamento" name="data_pagamento"
                                           value="{{ old('data_pagamento', is_object($pagamento->data_pagamento) ? $pagamento->data_pagamento->format('Y-m-d') : $pagamento->data_pagamento) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="valor_pagamento">Valor do Pagamento:</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">R$</span>
                                        </div>
                                        <input type="number" class="form-control" id="valor_pagamento" name="valor_pagamento"
                                               step="0.01" min="0" value="{{ old('valor_pagamento', $pagamento->valor_pagamento) }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status:</label>
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="pendente" {{ old('status', $pagamento->status) == 'pendente' ? 'selected' : '' }}>Pendente</option>
                                        <option value="aprovado" {{ old('status', $pagamento->status) == 'aprovado' ? 'selected' : '' }}>Aprovado</option>
                                        <option value="rejeitado" {{ old('status', $pagamento->status) == 'rejeitado' ? 'selected' : '' }}>Rejeitado</option>
                                        <option value="pago" {{ old('status', $pagamento->status) == 'pago' ? 'selected' : '' }}>Pago</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="documento_redmine">Documento Redmine:</label>
                                    <input type="text" class="form-control" id="documento_redmine" name="documento_redmine"
                                           value="{{ old('documento_redmine', $pagamento->documento_redmine) }}" placeholder="URL ou ID do documento">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="observacoes">Observações:</label>
                            <textarea class="form-control" id="observacoes" name="observacoes" rows="3">{{ old('observacoes', $pagamento->observacoes) }}</textarea>
                        </div>

                        <!-- Informações da Medição Selecionada -->
                        <div id="medicao-info" class="alert alert-info" style="display: none;">
                            <h6><i class="fas fa-info-circle"></i> Informações da Medição:</h6>
                            <div id="medicao-details"></div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Atualizar
                        </button>
                        <a href="{{ route('pagamento.show', $pagamento) }}" class="btn btn-secondary">
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
    // Atualizar informações da medição quando selecionada
    document.getElementById('medicao_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const valorTotal = selectedOption.getAttribute('data-valor');
        const catalogo = selectedOption.getAttribute('data-catalogo');
        const contrato = selectedOption.getAttribute('data-contrato');
        const lotacao = selectedOption.getAttribute('data-lotacao');

        const infoDiv = document.getElementById('medicao-info');
        const detailsDiv = document.getElementById('medicao-details');

        if (valorTotal) {
            detailsDiv.innerHTML = `
                <p><strong>Catálogo:</strong> ${catalogo}</p>
                <p><strong>Contrato:</strong> ${contrato}</p>
                <p><strong>Lotação:</strong> ${lotacao}</p>
                <p><strong>Valor Total da Medição:</strong> R$ ${parseFloat(valorTotal).toFixed(2).replace('.', ',')}</p>
            `;
            infoDiv.style.display = 'block';
        } else {
            infoDiv.style.display = 'none';
        }
    });

    // Mostrar informações da medição atual ao carregar a página
    document.addEventListener('DOMContentLoaded', function() {
        const medicaoSelect = document.getElementById('medicao_id');
        if (medicaoSelect.value) {
            medicaoSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
@endsection
