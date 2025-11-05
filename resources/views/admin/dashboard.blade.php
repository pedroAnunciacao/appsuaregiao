@extends('layouts.master')

@section('listagem')
{{-- Modern dashboard with gradient stat cards --}}
<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4>Painel Administrativo</h4>
    <div class="page-title-right">
        <ol class="breadcrumb m-0">
            <li class="breadcrumb-item"><a href="javascript: void(0);">Admin</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </div>
</div>

{{-- Fixed data array access to use $data array from controller --}}
{{-- Modern stat cards with hover effects --}}
<div class="row" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="stat-card">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <p class="text-muted mb-2" style="font-size: 0.875rem; font-weight: 500;">Total de Usuários</p>
                <h4 style="font-size: 2rem; font-weight: 700; margin: 0;">{{ $data['total_users'] ?? 0 }}</h4>
            </div>
            <div class="stat-icon primary">
                <i class="bx bx-user"></i>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <p class="text-muted mb-2" style="font-size: 0.875rem; font-weight: 500;">Usuários Banidos</p>
                <h4 style="font-size: 2rem; font-weight: 700; margin: 0;">{{ $data['banned_users'] ?? 0 }}</h4>
            </div>
            <div class="stat-icon danger">
                <i class="bx bx-block"></i>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <p class="text-muted mb-2" style="font-size: 0.875rem; font-weight: 500;">Posts Banidos</p>
                <h4 style="font-size: 2rem; font-weight: 700; margin: 0;">{{ $data['posts_banned'] ?? 0 }}</h4>
            </div>
            <div class="stat-icon warning">
                <i class="bx bx-flag"></i>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <p class="text-muted mb-2" style="font-size: 0.875rem; font-weight: 500;">Palavras Banidas</p>
                <h4 style="font-size: 2rem; font-weight: 700; margin: 0;">{{ $data['banned_words'] ?? 0 }}</h4>
            </div>
            <div class="stat-icon info">
                <i class="bx bx-message-square-x"></i>
            </div>
        </div>
    </div>
</div>

<div class="row" style="display: grid; grid-template-columns: 1fr 400px; gap: 1.5rem;">
    {{-- Modern activity feed --}}
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Atividade Recente</h4>
            <div class="table-responsive">
                <table class="table">
                    <tbody>
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">
                                Nenhuma atividade recente
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modern quick actions sidebar --}}
    <div>
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Ações Rápidas</h4>
                <div class="d-grid gap-3">
                    <a href="/users" class="btn btn-primary">
                        <i class="bx bx-user"></i> Gerenciar Usuários
                    </a>
                    <a href="/reports" class="btn btn-warning">
                        <i class="bx bx-flag"></i> Ver Reports
                    </a>
                    <a href="/banned-words" class="btn btn-info">
                        <i class="bx bx-message-square-x"></i> Palavras Banidas
                    </a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Estatísticas do Mês</h4>
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <div>
                        <p class="text-muted mb-2 small">Novos Usuários</p>
                        <h5 style="font-size: 1.5rem; font-weight: 700; margin: 0;">{{ $data['total_users'] ?? 0 }}</h5>
                    </div>
                    <div>
                        <p class="text-muted mb-2 small">Posts Banidos Recentemente</p>
                        <h5 style="font-size: 1.5rem; font-weight: 700; margin: 0;">{{ $data['posts_banned_recently'] ?? 0 }}</h5>
                    </div>
                    <div>
                        <p class="text-muted mb-2 small">Banimentos Recentes (7 dias)</p>
                        <h5 style="font-size: 1.5rem; font-weight: 700; margin: 0;">{{ $data['recent_bans'] ?? 0 }}</h5>
                    </div>
                </div>
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
