@extends('layouts.admin')

@section('title', 'Registrar Equipe - Diário de Obras')

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-user-plus text-warning"></i>
                        Registrar Equipe
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('diario-obras.dashboard') }}">Diário de Obras</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('diario-obras.equipe.index') }}">Equipe</a></li>
                        <li class="breadcrumb-item active">Registrar</li>
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
                            <h3 class="card-title">
                                <i class="fas fa-users text-primary"></i>
                                Dados da Equipe
                            </h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('diario-obras.equipe.store') }}" method="POST">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="projeto_id">Projeto *</label>
                                            <select class="form-control @error('projeto_id') is-invalid @enderror" id="projeto_id" name="projeto_id" required>
                                                <option value="">Selecione um projeto</option>
                                                @foreach($projetos as $projeto)
                                                    <option value="{{ $projeto->id }}" {{ old('projeto_id') == $projeto->id ? 'selected' : '' }}>
                                                        {{ $projeto->nome }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('projeto_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="data_trabalho">Data do Trabalho *</label>
                                            <input type="date" class="form-control @error('data_trabalho') is-invalid @enderror"
                                                   id="data_trabalho" name="data_trabalho"
                                                   value="{{ old('data_trabalho', now()->format('Y-m-d')) }}" required>
                                            @error('data_trabalho')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Seleção de Pessoas -->
                                <div class="form-group">
                                    <label for="pessoa_select">Pessoas da Equipe *</label>
                                    <div class="pessoa-select-container">
                                        <div class="pessoa-chips" id="pessoa-chips">
                                            <!-- Chips das pessoas selecionadas aparecerão aqui -->
                                        </div>
                                        <select class="form-control" id="pessoa_select">
                                            <option value="">Selecione uma pessoa para adicionar</option>
                                            @foreach($pessoas as $pessoa)
                                                <option value="{{ $pessoa->id }}" data-nome="{{ $pessoa->nome }}" data-funcao="{{ $pessoa->funcao ? $pessoa->funcao->nome : 'Sem função' }}">
                                                    {{ $pessoa->nome }} - {{ $pessoa->funcao ? $pessoa->funcao->nome : 'Sem função' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <small class="form-text text-muted">
                                        Selecione uma pessoa para adicionar à equipe
                                    </small>
                                </div>

                                <!-- Campos para cada pessoa selecionada -->
                                <div id="campos-pessoas">
                                    <!-- Será preenchido dinamicamente via JavaScript -->
                                </div>

                                <!-- Campo oculto para enviar lista de pessoas selecionadas -->
                                <input type="hidden" id="pessoas_selecionadas_input" name="pessoas_selecionadas" value="">

                                <div class="form-group">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save"></i>
                                        Salvar Registro
                                    </button>
                                    <a href="{{ route('diario-obras.equipe.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i>
                                        Voltar
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('styles')
<style>
.pessoa-select-container {
    position: relative;
}

.pessoa-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    margin-bottom: 10px;
    min-height: 40px;
    padding: 5px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    background-color: #fff;
}

.pessoa-chip {
    display: inline-flex;
    align-items: center;
    background-color: #007bff;
    color: white;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 14px;
    margin: 2px;
}

.pessoa-chip .remove-chip {
    background: none;
    border: none;
    color: white;
    margin-left: 8px;
    cursor: pointer;
    font-size: 16px;
    font-weight: bold;
    padding: 0;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

.pessoa-chip .remove-chip:hover {
    background-color: rgba(255, 255, 255, 0.2);
}

#pessoa_select {
    border: none;
    box-shadow: none;
}

#pessoa_select:focus {
    border: none;
    box-shadow: none;
}
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const pessoaSelect = document.getElementById('pessoa_select');
    const pessoaChips = document.getElementById('pessoa-chips');
    const camposPessoas = document.getElementById('campos-pessoas');

    // Dados das pessoas (vindos do PHP)
    const pessoas = @json($pessoas);
    let pessoasSelecionadas = [];

    // Adicionar pessoa quando selecionada
    pessoaSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value && !pessoasSelecionadas.includes(selectedOption.value)) {
            adicionarPessoa(selectedOption.value, selectedOption.dataset.nome, selectedOption.dataset.funcao);
            this.selectedIndex = 0; // Reset para primeira opção
        }
    });

    function adicionarPessoa(pessoaId, nome, funcao) {
        pessoasSelecionadas.push(pessoaId);

        // Criar chip
        const chip = document.createElement('div');
        chip.className = 'pessoa-chip';
        chip.innerHTML = `
            ${nome} - ${funcao}
            <button type="button" class="remove-chip" onclick="removerPessoa('${pessoaId}')">
                ×
            </button>
        `;
        pessoaChips.appendChild(chip);

        // Criar campos para a pessoa
        criarCamposPessoa(pessoaId, nome, funcao);

        // Atualizar campo oculto
        atualizarCampoOculto();

        // Remover opção do select
        const option = pessoaSelect.querySelector(`option[value="${pessoaId}"]`);
        if (option) {
            option.style.display = 'none';
        }
    }

    function removerPessoa(pessoaId) {
        // Remover da lista
        pessoasSelecionadas = pessoasSelecionadas.filter(id => id !== pessoaId);

        // Remover chip
        const chip = pessoaChips.querySelector(`button[onclick="removerPessoa('${pessoaId}')"]`).parentElement;
        chip.remove();

        // Remover campos
        const campos = document.getElementById(`campos-pessoa-${pessoaId}`);
        if (campos) {
            campos.remove();
        }

        // Atualizar campo oculto
        atualizarCampoOculto();

        // Mostrar opção no select novamente
        const option = pessoaSelect.querySelector(`option[value="${pessoaId}"]`);
        if (option) {
            option.style.display = 'block';
        }
    }

    function atualizarCampoOculto() {
        const campoOculto = document.getElementById('pessoas_selecionadas_input');
        campoOculto.value = JSON.stringify(pessoasSelecionadas);
    }

    function criarCamposPessoa(pessoaId, nome, funcao) {
        const card = document.createElement('div');
        card.className = 'card mb-3';
        card.id = `campos-pessoa-${pessoaId}`;
        card.innerHTML = `
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user text-primary"></i>
                    ${nome} - ${funcao}
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Hora de Entrada</label>
                            <input type="time" class="form-control hora-entrada"
                                   name="pessoas[${pessoaId}][hora_entrada]"
                                   data-pessoa="${pessoaId}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Hora Saída Almoço</label>
                            <input type="time" class="form-control hora-saida-almoco"
                                   name="pessoas[${pessoaId}][hora_saida_almoco]"
                                   data-pessoa="${pessoaId}">
                            <div class="invalid-feedback hora-saida-almoco-error" style="display: none;">
                                Deve estar entre entrada e saída
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Hora Retorno Almoço</label>
                            <input type="time" class="form-control hora-retorno-almoco"
                                   name="pessoas[${pessoaId}][hora_retorno_almoco]"
                                   data-pessoa="${pessoaId}">
                            <div class="invalid-feedback hora-retorno-almoco-error" style="display: none;">
                                Deve estar entre saída do almoço e saída
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Hora de Saída</label>
                            <input type="time" class="form-control hora-saida"
                                   name="pessoas[${pessoaId}][hora_saida]"
                                   data-pessoa="${pessoaId}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Horas Trabalhadas</label>
                            <input type="number" class="form-control horas-trabalhadas"
                                   name="pessoas[${pessoaId}][horas_trabalhadas]"
                                   data-pessoa="${pessoaId}"
                                   min="0" max="24" step="0.5" readonly>
                            <small class="form-text text-muted">Calculado automaticamente</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tipo de Almoço</label>
                            <select class="form-control tipo-almoco" name="pessoas[${pessoaId}][tipo_almoco]" data-pessoa="${pessoaId}">
                                <option value="integral">Integral</option>
                                <option value="reduzido">Reduzido</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"
                               name="pessoas[${pessoaId}][presente]" value="1" checked>
                        <label class="form-check-label">
                            Funcionário presente
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label>Atividades Realizadas</label>
                    <textarea class="form-control" rows="2"
                              name="pessoas[${pessoaId}][atividades_realizadas]"
                              placeholder="Descreva as atividades realizadas por ${nome}..."></textarea>
                </div>
                <div class="form-group">
                    <label>Observações</label>
                    <textarea class="form-control" rows="2"
                              name="pessoas[${pessoaId}][observacoes]"
                              placeholder="Observações sobre ${nome}..."></textarea>
                </div>
            </div>
        `;
        camposPessoas.appendChild(card);

        // Adicionar event listeners para validação e cálculo
        adicionarEventListenersPessoa(pessoaId);
    }

    function adicionarEventListenersPessoa(pessoaId) {
        const horaEntrada = document.querySelector(`input[name="pessoas[${pessoaId}][hora_entrada]"]`);
        const horaSaida = document.querySelector(`input[name="pessoas[${pessoaId}][hora_saida]"]`);
        const horaSaidaAlmoco = document.querySelector(`input[name="pessoas[${pessoaId}][hora_saida_almoco]"]`);
        const horaRetornoAlmoco = document.querySelector(`input[name="pessoas[${pessoaId}][hora_retorno_almoco]"]`);
        const horasTrabalhadas = document.querySelector(`input[name="pessoas[${pessoaId}][horas_trabalhadas]"]`);

        // Event listeners para validação e cálculo
        [horaEntrada, horaSaida, horaSaidaAlmoco, horaRetornoAlmoco].forEach(input => {
            if (input) {
                input.addEventListener('change', () => {
                    validarHorasAlmoco(pessoaId);
                    calcularHorasTrabalhadas(pessoaId);
                });
            }
        });
    }

    function validarHorasAlmoco(pessoaId) {
        const horaEntrada = document.querySelector(`input[name="pessoas[${pessoaId}][hora_entrada]"]`).value;
        const horaSaida = document.querySelector(`input[name="pessoas[${pessoaId}][hora_saida]"]`).value;
        const horaSaidaAlmoco = document.querySelector(`input[name="pessoas[${pessoaId}][hora_saida_almoco]"]`);
        const horaRetornoAlmoco = document.querySelector(`input[name="pessoas[${pessoaId}][hora_retorno_almoco]"]`);

        let isValid = true;

        // Validar hora saída almoço
        if (horaSaidaAlmoco.value && horaEntrada && horaSaida) {
            if (horaSaidaAlmoco.value < horaEntrada || horaSaidaAlmoco.value > horaSaida) {
                horaSaidaAlmoco.classList.add('is-invalid');
                horaSaidaAlmoco.nextElementSibling.style.display = 'block';
                isValid = false;
            } else {
                horaSaidaAlmoco.classList.remove('is-invalid');
                horaSaidaAlmoco.nextElementSibling.style.display = 'none';
            }
        }

        // Validar hora retorno almoço
        if (horaRetornoAlmoco.value && horaSaidaAlmoco.value && horaSaida) {
            if (horaRetornoAlmoco.value < horaSaidaAlmoco.value || horaRetornoAlmoco.value > horaSaida) {
                horaRetornoAlmoco.classList.add('is-invalid');
                horaRetornoAlmoco.nextElementSibling.style.display = 'block';
                isValid = false;
            } else {
                horaRetornoAlmoco.classList.remove('is-invalid');
                horaRetornoAlmoco.nextElementSibling.style.display = 'none';
            }
        }

        return isValid;
    }

    function calcularHorasTrabalhadas(pessoaId) {
        const horaEntrada = document.querySelector(`input[name="pessoas[${pessoaId}][hora_entrada]"]`).value;
        const horaSaida = document.querySelector(`input[name="pessoas[${pessoaId}][hora_saida]"]`).value;
        const horaSaidaAlmoco = document.querySelector(`input[name="pessoas[${pessoaId}][hora_saida_almoco]"]`).value;
        const horaRetornoAlmoco = document.querySelector(`input[name="pessoas[${pessoaId}][hora_retorno_almoco]"]`).value;
        const horasTrabalhadas = document.querySelector(`input[name="pessoas[${pessoaId}][horas_trabalhadas]"]`);

        if (!horaEntrada || !horaSaida) {
            horasTrabalhadas.value = '';
            return;
        }

        // Converter para minutos
        const entradaMinutos = timeToMinutes(horaEntrada);
        const saidaMinutos = timeToMinutes(horaSaida);

        let totalMinutos = saidaMinutos - entradaMinutos;

        // Subtrair tempo de almoço se informado
        if (horaSaidaAlmoco && horaRetornoAlmoco) {
            const saidaAlmocoMinutos = timeToMinutes(horaSaidaAlmoco);
            const retornoAlmocoMinutos = timeToMinutes(horaRetornoAlmoco);
            const tempoAlmocoMinutos = retornoAlmocoMinutos - saidaAlmocoMinutos;
            totalMinutos -= tempoAlmocoMinutos;
        }

        // Converter de volta para horas
        const horas = totalMinutos / 60;
        horasTrabalhadas.value = horas.toFixed(1);
    }

    function timeToMinutes(timeString) {
        if (!timeString) return 0;
        const [hours, minutes] = timeString.split(':').map(Number);
        return hours * 60 + minutes;
    }

    // Tornar função global para o onclick
    window.removerPessoa = removerPessoa;
});
</script>
@endsection

