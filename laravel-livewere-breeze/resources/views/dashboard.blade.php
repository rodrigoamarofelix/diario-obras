@extends('layouts.admin')

@section('title', 'Dashboard Analytics')
@section('page-title', 'Dashboard Analytics')

@section('breadcrumb')
<li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-message">
        <i class="icon fas fa-check"></i>
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert" id="error-message">
        <i class="icon fas fa-ban"></i>
        {{ session('error') }}
    </div>
@endif

<!-- Novo Dashboard Analytics -->
<livewire:dashboard-component />
@endsection
