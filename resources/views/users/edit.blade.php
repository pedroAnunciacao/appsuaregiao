@extends('layouts.master')
@section('listagem')
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Editar Usuário</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="/admin/dashboard">Admin</a></li>
                            <li class="breadcrumb-item"><a href="/users/index/">Usuários</a></li>
                            <li class="breadcrumb-item active">Editar</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Dados do Usuário</h4>

                        @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form action="/users/{{$user->id}}" method="POST" data-parsley-validate>
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="name" class="form-label">Nome Completo <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ old('name', $user->name) }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">E-mail <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="{{ old('email', $user->email) }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Nova Senha</label>
                                <input type="password" class="form-control" id="password" name="password">
                                <small class="text-muted">Deixe em branco para manter a senha atual</small>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Telefone</label>
                                <input type="text" class="form-control telefone" id="phone" name="phone"
                                    value="{{ old('phone', $user->phone) }}">
                            </div>

                            @if($user->is_banned)
                            <div class="alert alert-warning">
                                <h5 class="alert-heading">Usuário Banido</h5>
                                <p class="mb-1"><strong>Motivo:</strong> {{ $user->ban_reason }}</p>
                                @if($user->ban_expires_at)
                                <p class="mb-0"><strong>Expira em:</strong> {{
                                    \Carbon\Carbon::parse($user->ban_expires_at)->format('d/m/Y H:i') }}</p>
                                @else
                                <p class="mb-0"><strong>Banimento:</strong> Permanente</p>
                                @endif
                            </div>
                            @endif

                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-secondary" onclick="window.location.href='/users'">
                                    <i class="bx bx-arrow-back me-1"></i> Voltar
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save me-1"></i> Atualizar Usuário
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection