@extends('layouts.master')

@section('listagem')
{{-- Modern page header --}}
<div class="page-title-box d-sm-flex align-items-center justify-content-between">
    <h4>Palavras Banidas</h4>
    <div class="page-title-right">
        <ol class="breadcrumb m-0">
            <li class="breadcrumb-item"><a href="/admin/dashboard">Admin</a></li>
            <li class="breadcrumb-item active">Palavras Banidas</li>
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="card-title mb-0">Lista de Palavras Banidas</h4>
            <a href="/banned-words/create" class="btn btn-primary">
                <i class="bx bx-plus"></i> Nova Palavra
            </a>
        </div>

        {{-- Modern table --}}
        <div class="table-responsive">
            <table id="datatable-buttons" class="table w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Palavra</th>
                        <th>Data de Cadastro</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bannedWords ?? [] as $word)
                    <tr>
                        <td><span class="text-muted">#{{ $word->id }}</span></td>
                        <td>
                            <span class="badge bg-danger" style="font-size: 0.9375rem; padding: 0.5rem 1rem;">
                                {{ $word->word }}
                            </span>
                        </td>
                        <td><span class="text-muted">{{ \Carbon\Carbon::parse($word->created_at)->format('d/m/Y H:i') }}</span></td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="/banned-words/{{ $word->id }}/edit" class="btn btn-sm btn-primary" title="Editar">
                                    <i class="bx bx-edit"></i>
                                </a>
                                <form action="/banned-words/{{ $word->id }}" method="delete" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Deletar" onclick="return confirm('Deseja deletar esta palavra?')">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    {{-- Empty state will be handled outside the table --}}
                    @endforelse
                </tbody>
            </table>
            {{-- Added empty state outside table to avoid DataTables errors --}}
            @if(count($bannedWords ?? []) === 0)
            <div class="text-center text-muted py-4">
                <i class="bx bx-message-square-x" style="font-size: 3rem; opacity: 0.3;"></i>
                <p style="margin-top: 1rem;">Nenhuma palavra banida cadastrada</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
