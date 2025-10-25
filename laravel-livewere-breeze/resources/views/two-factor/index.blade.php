@extends('layouts.admin')

@section('title', 'Autenticação de Dois Fatores')
@section('page-title', 'Autenticação de Dois Fatores')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Autenticação 2FA</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-shield-alt text-primary"></i>
                    Autenticação de Dois Fatores
                </h3>
                <div class="card-tools">
                    <a href="{{ route('dashboard') }}" class="btn btn-tool">
                        <i class="fas fa-arrow-left"></i>
                        Voltar ao Dashboard
                    </a>
                </div>
            </div>
            <div class="card-body">
                @livewire('two-factor-component')
            </div>
        </div>
    </div>
</div>
@endsection

