<div>
    <label for="nome" class="block font-medium text-sm text-gray-700">{{ 'Nome' }}</label>
    <input class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" id="nome" name="nome" type="text" value="{{ isset($pessoa->nome) ? $pessoa->nome : ''}}" >
    {!! $errors->first('nome', '<p>:message</p>') !!}
</div>
<div>
    <label for="cpf" class="block font-medium text-sm text-gray-700">{{ 'Cpf' }}</label>
    <input class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" id="cpf" name="cpf" type="text" value="{{ isset($pessoa->cpf) ? $pessoa->cpf : ''}}" >
    {!! $errors->first('cpf', '<p>:message</p>') !!}
</div>
<div>
    <label for="lotacao_id" class="block font-medium text-sm text-gray-700">{{ 'Lotacao Id' }}</label>
    <select name="lotacao_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" id="lotacao_id" >
    @foreach (json_decode('lotacoes,nome', true) as $optionKey => $optionValue)
        <option value="{{ $optionKey }}" {{ (isset($pessoa->lotacao_id) && $pessoa->lotacao_id == $optionKey) ? 'selected' : ''}}>{{ $optionValue }}</option>
    @endforeach
</select>
    {!! $errors->first('lotacao_id', '<p>:message</p>') !!}
</div>
<div>
    <label for="status" class="block font-medium text-sm text-gray-700">{{ 'Status' }}</label>
    <select name="status" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" id="status" >
    @foreach (json_decode('['ativo'=>'Ativo','inativo'=>'Inativo']', true) as $optionKey => $optionValue)
        <option value="{{ $optionKey }}" {{ (isset($pessoa->status) && $pessoa->status == $optionKey) ? 'selected' : ''}}>{{ $optionValue }}</option>
    @endforeach
</select>
    {!! $errors->first('status', '<p>:message</p>') !!}
</div>


<div class="flex items-center gap-4">
    <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
        {{ $formMode === 'edit' ? 'Update' : 'Create' }}
    </button>
</div>
