@extends('layouts.admin')

@section('title', 'Editar Lotação')
@section('page-title', 'Editar Lotação')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('lotacao.index') }}">Lotações</a></li>
<li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-edit"></i> Editar Lotação: {{ $lotacao->nome }}
                </h3>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert" id="error-message">
                        <i class="icon fas fa-ban"></i>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('lotacao.update', $lotacao) }}">
                    @method('PATCH')
                    @csrf
                    @include ('lotacao.form', ['formMode' => 'edit'])
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto-hide error message after 8 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const errorMessage = document.getElementById('error-message');
        if (errorMessage) {
            setTimeout(function() {
                errorMessage.style.transition = 'opacity 0.5s ease-out';
                errorMessage.style.opacity = '0';
                setTimeout(function() {
                    errorMessage.remove();
                }, 500);
            }, 8000); // 8 seconds
        }
    });
</script>
@endsection
