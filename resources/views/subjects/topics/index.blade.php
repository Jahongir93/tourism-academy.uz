@extends('layouts.dashboard-new')

@section('title', 'Fan mavzulari - ' . ($subject->name_uz ?? $subject->name))
@section('page-title', 'Fan mavzulari')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>{{ $subject->name_uz ?? $subject->name }}</h2>
                    <p class="text-muted">Fan kodi: {{ $subject->code }}</p>
                </div>
                <a href="{{ route('subjects.topics.create', $subject) }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Yangi mavzu qo'shish
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

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Jami mavzular</h5>
                    <h3 class="text-primary">{{ $statistics['total_topics'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Ma'ruza</h5>
                    <h3 class="text-info">{{ $statistics['lecture_topics'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Amaliyot</h5>
                    <h3 class="text-success">{{ $statistics['practice_topics'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Jami soatlar</h5>
                    <h3 class="text-warning">{{ $statistics['total_hours'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Topics Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Fan mavzulari</h5>
        </div>
        <div class="card-body">
            @if($topics->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="5%">№</th>
                                <th width="35%">Mavzu nomi</th>
                                <th width="15%">Turi</th>
                                <th width="10%">Soat</th>
                                <th width="10%">Hafta</th>
                                <th width="10%">Holati</th>
                                <th width="15%">Amallar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topics as $topic)
                                <tr>
                                    <td>{{ $topic->topic_number }}</td>
                                    <td>
                                        <strong>{{ $topic->title_uz }}</strong>
                                        @if($topic->description_uz)
                                            <br>
                                            <small class="text-muted">{{ Str::limit($topic->description_uz, 100) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($topic->topic_type == 'lecture')
                                            <span class="badge bg-primary">Ma'ruza</span>
                                        @elseif($topic->topic_type == 'practice')
                                            <span class="badge bg-success">Amaliyot</span>
                                        @elseif($topic->topic_type == 'lab')
                                            <span class="badge bg-info">Laboratoriya</span>
                                        @elseif($topic->topic_type == 'seminar')
                                            <span class="badge bg-warning">Seminar</span>
                                        @else
                                            <span class="badge bg-secondary">Mustaqil</span>
                                        @endif
                                    </td>
                                    <td>{{ $topic->hours }} soat</td>
                                    <td>{{ $topic->week_number ?? '-' }}</td>
                                    <td>
                                        @if($topic->is_active)
                                            <span class="badge bg-success">Faol</span>
                                        @else
                                            <span class="badge bg-secondary">Nofaol</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('subjects.topics.show', [$subject, $topic]) }}"
                                               class="btn btn-info" title="Ko'rish">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('subjects.topics.edit', [$subject, $topic]) }}"
                                               class="btn btn-warning" title="Tahrirlash">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('subjects.topics.destroy', [$subject, $topic]) }}"
                                                  method="POST"
                                                  style="display: inline;"
                                                  onsubmit="return confirm('Rostdan ham bu mavzuni o\'chirmoqchimisiz?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" title="O'chirish">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $topics->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    Hozircha mavzular qo'shilmagan. Yangi mavzu qo'shish uchun yuqoridagi tugmani bosing.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
