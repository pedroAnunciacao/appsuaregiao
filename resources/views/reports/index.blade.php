@extends('layouts.master')

@section('listagem')
{{-- Modern page header --}}
<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4>Reports de Posts</h4>
    <div class="page-title-right">
        <ol class="breadcrumb m-0">
            <li class="breadcrumb-item"><a href="/admin/dashboard">Admin</a></li>
            <li class="breadcrumb-item active">Reports</li>
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

{{-- Modern filter card --}}
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-body">
        <form method="GET" action="/reports" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Filtrar por Status</label>
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Todos</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendentes</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Aprovados</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejeitados</option>
                </select>
            </div>
        </form>
    </div>
</div>

{{-- Modern reports table --}}
<div class="card">
    <div class="card-body">
        <h4 class="card-title mb-4">Lista de Reports</h4>

        <div class="table-responsive">
            <table id="datatable-buttons" class="table w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Post</th>
                        <th>Autor do Post</th>
                        <th>Denunciante</th>
                        <th>Motivo</th>
                        <th>Status</th>
                        <th>Data</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports ?? [] as $report)
                    <tr>
                        <td><span class="text-muted">#{{ $report->id }}</span></td>
                        <td>
                            <div style="max-width: 250px;">
                                <strong style="font-weight: 600;">{{ $report->post->title ?? 'Sem título' }}</strong>
                                <p class="text-muted mb-0 small text-truncate">{{ Str::limit($report->post->content ?? '', 50) }}</p>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-xs bg-soft-primary text-primary">
                                    <span class="avatar-title">
                                        {{ strtoupper(substr($report->post->user->name ?? 'U', 0, 1)) }}
                                    </span>
                                </div>
                                <div>
                                    <div style="font-weight: 500;">{{ $report->post->user->name ?? 'Usuário desconhecido' }}</div>
                                    <small class="text-muted">{{ $report->post->user->email ?? '' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-xs bg-soft-warning text-warning">
                                    <span class="avatar-title">
                                        {{ strtoupper(substr($report->user->name ?? 'U', 0, 1)) }}
                                    </span>
                                </div>
                                <div>
                                    <div style="font-weight: 500;">{{ $report->user->name ?? 'Usuário desconhecido' }}</div>
                                    <small class="text-muted">{{ $report->user->email ?? '' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-info">{{ ucfirst($report->reason) }}</span>
                        </td>
                        <td>
                            @if($report->status == 'pending')
                                <span class="badge bg-warning">Pendente</span>
                            @elseif($report->status == 'reviewed ')
                                <span class="badge bg-success">Aprovado</span>
                            @else
                                <span class="badge bg-danger">Rejeitado</span>
                            @endif
                        </td>
                        <td><span class="text-muted">{{ \Carbon\Carbon::parse($report->created_at)->format('d/m/Y H:i') }}</span></td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="/reports/{{ $report->id }}" class="btn btn-sm btn-info" title="Ver Detalhes">
                                    <i class="bx bx-show"></i>
                                </a>
                                
                                @if($report->status == 'pending')
                                    <form action="/reports/approve/{{ $report->id }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-success" title="Aprovar" onclick="return confirm('Deseja aprovar este report e remover o post?')">
                                            <i class="bx bx-check"></i>
                                        </button>
                                    </form>

                                    <form action="/reports/reject/{{ $report->id }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Rejeitar" onclick="return confirm('Deseja rejeitar este report?')">
                                            <i class="bx bx-x"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="bx bx-flag" style="font-size: 3rem; opacity: 0.3;"></i>
                            <p style="margin-top: 1rem;">Nenhum report encontrado</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
