@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Teste de Upload de Arquivos</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('test.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="test_file">Selecione um arquivo para teste:</label>
                            <input type="file" class="form-control-file" id="test_file" name="test_file" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Testar Upload</button>
                    </form>

                    @if(session('test_result'))
                        <div class="mt-4">
                            <h5>Resultado do Teste:</h5>
                            <pre>{{ json_encode(session('test_result'), JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection