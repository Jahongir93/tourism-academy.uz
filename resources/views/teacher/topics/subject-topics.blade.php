@extends('layouts.dashboard-new')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('teacher.topics.index') }}">Fanlar</a>
                            </li>
                            <li class="breadcrumb-item active">{{ $subject->name }}</li>
                        </ol>
                    </nav>
                    <h3>{{ $subject->name }} - Mavzular</h3>
                </div>
                <a href="{{ route('teacher.topics.create', $subject->id) }}"
                   class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Yangi Mavzu
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if($topics->isEmpty())
                        <div class="alert alert-info">
                            Bu fan uchun hali mavzular qo'shilmagan.
                            <a href="{{ route('teacher.topics.create', $subject->id) }}"
                               class="alert-link">Birinchi mavzu qo'shish</a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 50px">Tartib</th>
                                        <th>Mavzu nomi</th>
                                        <th>Tavsif</th>
                                        <th style="width: 120px">Davomiyligi</th>
                                        <th style="width: 120px">Resurslar</th>
                                        <th style="width: 200px">Amallar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topics as $topic)
                                        <tr>
                                            <td>
                                                <span class="badge bg-secondary">{{ $topic->order }}</span>
                                            </td>
                                            <td>
                                                <strong>{{ $topic->title }}</strong>
                                            </td>
                                            <td>
                                                @if($topic->description)
                                                    {{ Str::limit($topic->description, 50) }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($topic->duration_hours)
                                                    <span class="badge bg-info">
                                                        {{ $topic->duration_hours }} soat
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">
                                                    {{ $topic->resources_count }} ta
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('teacher.topics.show', [$subject->id, $topic->id]) }}"
                                                       class="btn btn-info"
                                                       title="Ko'rish">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('teacher.topics.edit', [$subject->id, $topic->id]) }}"
                                                       class="btn btn-warning"
                                                       title="Tahrirlash">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('teacher.topics.destroy', [$subject->id, $topic->id]) }}"
                                                          method="POST"
                                                          class="d-inline"
                                                          onsubmit="return confirm('Rostdan ham o\'chirmoqchimisiz?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="btn btn-danger btn-sm"
                                                                title="O'chirish">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
