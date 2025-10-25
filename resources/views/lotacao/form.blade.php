<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label for="nome">Nome <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="nome" name="nome"
                   value="{{ isset($lotacao->nome) ? $lotacao->nome : ''}}"
                   required>
            @error('nome')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="descricao">Descrição</label>
            <textarea class="form-control" id="descricao" name="descricao" rows="4"
                      placeholder="Descrição da lotação...">{{ isset($lotacao->descricao) ? $lotacao->descricao : ''}}</textarea>
            @error('descricao')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="status">Status <span class="text-danger">*</span></label>
            <select class="form-control" name="status" id="status" required>
                <option value="">Selecione o status</option>
                <option value="ativo" {{ (isset($lotacao->status) && $lotacao->status == 'ativo') ? 'selected' : ''}}>Ativo</option>
                <option value="inativo" {{ (isset($lotacao->status) && $lotacao->status == 'inativo') ? 'selected' : ''}}>Inativo</option>
            </select>
            @error('status')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> {{ $formMode === 'edit' ? 'Atualizar' : 'Criar' }}
            </button>
            <a href="{{ route('lotacao.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancelar
            </a>
        </div>
    </div>
</div>
