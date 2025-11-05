@extends('layouts.master')

@section('listagem')
{{-- Modernized page header and changed all route() to hardcoded URLs --}}
<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4>Cadastrar Palavra Banida</h4>
    <div class="page-title-right">
        <ol class="breadcrumb m-0">
            <li class="breadcrumb-item"><a href="/admin/dashboard">Admin</a></li>
            <li class="breadcrumb-item"><a href="/banned-words">Palavras Banidas</a></li>
            <li class="breadcrumb-item active">Cadastrar</li>
        </ol>
    </div>
</div>

{{-- Modern alert messages --}}
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bx bx-error-circle"></i>
    <ul class="mb-0" style="padding-left: 1.5rem;">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
        <i class="bx bx-x"></i>
    </button>
</div>
@endif

<div class="row">
    <div class="col-lg-6 offset-lg-3">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Nova Palavra Banida</h4>

                {{-- Changed route() to hardcoded URL --}}
                <form action="/api/banned-words/store" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="word" class="form-label">Palavra <span style="color: var(--color-danger);">*</span></label>
                        <input type="text" class="form-control" id="word" name="word" value="{{ old('word') }}" required>
                        <small class="text-muted">Digite a palavra que deseja banir do sistema</small>
                    </div>

                    <div class="alert alert-info">
                        <i class="bx bx-info-circle"></i>
                        Esta palavra será bloqueada em posts, comentários e outras áreas do sistema.
                    </div>

                    <div class="d-flex justify-content-between" style="margin-top: 2rem;">
                        <a href="/banned-words" class="btn btn-secondary">
                            <i class="bx bx-arrow-back"></i> Voltar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save"></i> Cadastrar Palavra
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
