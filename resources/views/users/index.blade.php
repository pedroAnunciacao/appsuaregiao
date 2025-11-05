@extends('layouts.master')

@section('listagem')
{{-- Modern page header --}}
<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4>Gerenciar Usuários</h4>
    <div class="page-title-right">
        <ol class="breadcrumb m-0">
            <li class="breadcrumb-item"><a href="/admin/dashboard">Admin</a></li>
            <li class="breadcrumb-item active">Usuários</li>
        </ol>
    </div>
</div>

{{-- Modern alert messages --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bx bx-check-circle"></i>
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
        <i class="bx bx-x"></i>
    </button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bx bx-error-circle"></i>
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
        <i class="bx bx-x"></i>
    </button>
</div>
@endif

{{-- Modern card with action button --}}
<div class="card">
    <div class="card-body">
        {{-- <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="card-title mb-0">Lista de Usuários</h4>
            <a href="/users/create" class="btn btn-primary">
                <i class="bx bx-plus"></i> Novo Usuário
            </a>
        </div> --}}

        {{-- Modern table with better spacing --}}
        <div class="table-responsive">
            <table id="datatable-buttons" class="table w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Data Cadastro</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users ?? [] as $user)
                    <tr>
                        <td><span class="text-muted">#{{ $user->id }}</span></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-xs bg-soft-primary text-primary">
                                    <span class="avatar-title">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </span>
                                </div>
                                <span style="font-weight: 500;">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td><span class="text-muted">{{ $user->email }}</span></td>
                        <td>
                            @if($user->deleted_at)
                                <span class="badge bg-secondary">Deletado</span>
                            @elseif($user->is_banned)
                                <span class="badge bg-danger">Banido</span>
                                @if($user->ban_expires_at)
                                    <small class="d-block text-muted" style="margin-top: 0.25rem;">Até: {{ \Carbon\Carbon::parse($user->ban_expires_at)->format('d/m/Y H:i') }}</small>
                                @endif
                            @else
                                <span class="badge bg-success">Ativo</span>
                            @endif
                        </td>
                        <td><span class="text-muted">{{ \Carbon\Carbon::parse($user->created_at)->format('d/m/Y H:i') }}</span></td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="/users/edit/{{ $user->id }}" class="btn btn-sm btn-primary" title="Editar">
                                    <i class="bx bx-edit"></i>
                                </a>
                                
                                @if(!$user->deleted_at)
                                    @if($user->is_banned)
                                        <form action="/users/unban/{{ $user->id }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-success" title="Desbanir" onclick="return confirm('Deseja desbanir este usuário?')">
                                                <i class="bx bx-check-circle"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button type="button" class="btn btn-sm btn-warning" title="Banir" data-bs-toggle="modal" data-bs-target="#banModal{{ $user->id }}">
                                            <i class="bx bx-block"></i>
                                        </button>
                                    @endif

                                    <form action="/users/delete/{{ $user->id }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Deletar" onclick="return confirm('Deseja deletar este usuário?')">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                @else
                                    <form action="/users/restore/{{ $user->id }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-info" title="Restaurar" onclick="return confirm('Deseja restaurar este usuário?')">
                                            <i class="bx bx-revision"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>

                    {{-- Modern modal design --}}
                    <div class="modal fade" id="banModal{{ $user->id }}" tabindex="-1" aria-labelledby="banModalLabel{{ $user->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="/users/ban/{{ $user->id }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="banModalLabel{{ $user->id }}">Banir Usuário</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                            <i class="bx bx-x"></i>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Usuário</label>
                                            <input type="text" class="form-control" value="{{ $user->name }} ({{ $user->email }})" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label for="ban_reason{{ $user->id }}" class="form-label">Motivo do Banimento <span style="color: var(--color-danger);">*</span></label>
                                            <textarea class="form-control" id="ban_reason{{ $user->id }}" name="ban_reason" rows="3" required placeholder="Descreva o motivo do banimento..."></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="ban_expires_at{{ $user->id }}" class="form-label">Data de Expiração (opcional)</label>
                                            <input type="datetime-local" class="form-control" id="ban_expires_at{{ $user->id }}" name="ban_expires_at">
                                            <small class="text-muted">Deixe em branco para banimento permanente</small>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-danger">Banir Usuário</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="bx bx-user" style="font-size: 3rem; opacity: 0.3;"></i>
                            <p style="margin-top: 1rem;">Nenhum usuário encontrado</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
