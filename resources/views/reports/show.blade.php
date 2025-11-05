@extends('layouts.master')

@section('listagem')
{{-- Modernized page header and changed all route() to hardcoded URLs --}}
<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4>Detalhes do Report #{{ $report->id }}</h4>
    <div class="page-title-right">
        <ol class="breadcrumb m-0">
            <li class="breadcrumb-item"><a href="/admin/dashboard">Admin</a></li>
            <li class="breadcrumb-item"><a href="/reports">Reports</a></li>
            <li class="breadcrumb-item active">Detalhes</li>
        </ol>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bx bx-check-circle"></i>
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
        <i class="bx bx-x"></i>
    </button>
</div>
@endif

<div class="row" style="display: grid; grid-template-columns: 1fr 400px; gap: 1.5rem;">
    {{-- Report Details --}}
    <div>
        <div class="card">
            <div class="card-body">
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="card-title mb-0">Informações do Report</h4>
    @if($report->status == 'pending')
        <span class="badge bg-warning">Pendente</span>
    @elseif($report->status == 'reviewed')
        <span class="badge bg-success">Aprovado</span>
    @elseif($report->status == 'dismissed')
        <span class="badge bg-danger">Rejeitado</span>
    @else
        <span class="badge bg-secondary">Desconhecido</span>
    @endif
</div>

                <div class="mb-4">
                    <h5 style="font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Motivo da Denúncia:</h5>
                    <div class="alert alert-info mb-0">
                        <i class="bx bx-info-circle"></i>
                        {{ ucfirst($report->reason) }}
                    </div>
                </div>

                <div class="mb-3">
                    <h5 style="font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Data do Report:</h5>
                    <p class="text-muted mb-0">
                        <i class="bx bx-calendar"></i>
                        {{ \Carbon\Carbon::parse($report->created_at)->format('d/m/Y H:i:s') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Post Details --}}
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Post Reportado</h4>

                @if($report->post->title ?? false)
                <div class="mb-3">
                    <h5 style="font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Título:</h5>
                    <p class="mb-0" style="font-weight: 500;">{{ $report->post->title }}</p>
                </div>
                @endif
                <div class="mb-4">
                    <h5 style="font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Conteúdo:</h5>
                    <div style="background: var(--color-card-hover); border-radius: 0.5rem; padding: 1rem;">
                        {{ $report->post->text ?? 'Conteúdo não disponível' }}
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <h5 style="font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Autor do Post:</h5>
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-sm bg-soft-primary text-primary">
                                <span class="avatar-title">
                                    {{ strtoupper(substr($report->post->user->name ?? 'U', 0, 1)) }}
                                </span>
                            </div>
                            <div>
                                <p class="mb-0" style="font-weight: 500;">{{ $report->post->user->name ?? 'N/A' }}</p>
                                <p class="mb-0 text-muted small">{{ $report->post->user->email ?? '' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h5 style="font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Data de Criação:</h5>
                        <p class="text-muted mb-0">
                            <i class="bx bx-calendar"></i>
                            {{ \Carbon\Carbon::parse($report->post->created_at)->format('d/m/Y H:i') ?? 'N/A' }}
                        </p>
                    </div>
                </div>

                @if($report->post->image_url ?? false)
                <div class="mt-3">
                    <h5 style="font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Imagem do Post:</h5>
                    <img src="{{ $report->post->image_url }}" alt="Post Image" class="img-fluid rounded"
                        style="max-height: 300px;">
                </div>
                @endif
            </div>
        </div>
    </div>

    <div>
        {{-- User Info --}}
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Denunciante</h4>

                <div class="text-center mb-4">
                    <div class="avatar-lg mx-auto mb-3 bg-soft-warning text-warning">
                        <span class="avatar-title" style="font-size: 1.5rem;">
                            {{ strtoupper(substr($report->user->name ?? 'U', 0, 1)) }}
                        </span>
                    </div>
                    <h5 style="font-size: 1rem; font-weight: 600; margin-bottom: 0.25rem;">{{ $report->user->name ??
                        'Usuário desconhecido' }}</h5>
                    <p class="text-muted mb-2">{{ $report->user->email ?? 'N/A' }}</p>

                    @if($report->user->is_banned ?? false)
                    <span class="badge bg-danger">Banido</span>
                    @else
                    <span class="badge bg-success">Ativo</span>
                    @endif
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-borderless mb-0">
                        <tbody>
                            <tr>
                                <td class="text-muted">ID:</td>
                                <td class="text-end">#{{ $report->user->id ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Cadastro:</td>
                                <td class="text-end">{{
                                    \Carbon\Carbon::parse($report->user->created_at)->format('d/m/Y') ?? 'N/A' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Ações</h4>

                @if($report->status == 'pending')
                <div class="d-grid gap-2">
                    {{-- Changed route() to hardcoded URLs --}}
                    <form action="/reports/approve/{{ $report->id }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success w-100"
                            onclick="return confirm('Deseja aprovar este report e remover o post?')">
                            <i class="bx bx-check-circle"></i> Aprovar Report
                        </button>
                    </form>

                    <form action="/reports/reject/{{ $report->id }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-danger w-100"
                            onclick="return confirm('Deseja rejeitar este report?')">
                            <i class="bx bx-x-circle"></i> Rejeitar Report
                        </button>
                    </form>

                    <a href="/reports" class="btn btn-secondary w-100">
                        <i class="bx bx-arrow-back"></i> Voltar
                    </a>
                </div>
                @else
                <div class="text-center">
                    <div class="alert alert-info">
                        <i class="bx bx-info-circle"></i>
                        Este report já foi processado
                    </div>
                    <a href="/reports" class="btn btn-secondary w-100">
                        <i class="bx bx-arrow-back"></i> Voltar para Reports
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
<style>
    @media (max-width: 768px) {
        .row[style*="grid-template-columns: 1fr 400px"] {
            grid-template-columns: 1fr !important;
        }
    }
</style>

@endsection