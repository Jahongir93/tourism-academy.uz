@extends('layouts.dashboard-new')

@section('title', 'Mavzu tafsilotlari')
@section('page-title', 'Mavzu tafsilotlari')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Mavzu № {{ $topic->topic_number }}</h2>
                    <p class="text-muted">Fan: {{ $subject->name_uz ?? $subject->name }} ({{ $subject->code }})</p>
                </div>
                <div>
                    <a href="{{ route('subjects.topics.edit', [$subject, $topic]) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Tahrirlash
                    </a>
                    <a href="{{ route('subjects.topics.index', $subject) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Orqaga
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Asosiy ma'lumotlar</h5>
                </div>
                <div class="card-body">
                    <h4>{{ $topic->title_uz }}</h4>

                    @if($topic->title_ru)
                        <p class="text-muted"><strong>Ruscha:</strong> {{ $topic->title_ru }}</p>
                    @endif

                    @if($topic->title_en)
                        <p class="text-muted"><strong>Inglizcha:</strong> {{ $topic->title_en }}</p>
                    @endif

                    @if($topic->description_uz)
                        <hr>
                        <h6>Tavsif:</h6>
                        <p>{{ $topic->description_uz }}</p>
                    @endif

                    @if($topic->learning_outcomes)
                        <hr>
                        <h6>O'quv natijalari:</h6>
                        <pre class="bg-light p-3 rounded">{{ $topic->learning_outcomes }}</pre>
                    @endif

                    @if($topic->keywords)
                        <hr>
                        <h6>Kalit so'zlar:</h6>
                        <p>
                            @foreach(explode(',', $topic->keywords) as $keyword)
                                <span class="badge bg-secondary">{{ trim($keyword) }}</span>
                            @endforeach
                        </p>
                    @endif

                    @if($topic->references)
                        <hr>
                        <h6>Adabiyotlar:</h6>
                        <pre class="bg-light p-3 rounded">{{ $topic->references }}</pre>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">Qo'shimcha ma'lumotlar</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>Mavzu raqami:</th>
                            <td>{{ $topic->topic_number }}</td>
                        </tr>
                        <tr>
                            <th>Mashg'ulot turi:</th>
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
                        </tr>
                        <tr>
                            <th>Soat miqdori:</th>
                            <td>{{ $topic->hours }} soat</td>
                        </tr>
                        <tr>
                            <th>Hafta raqami:</th>
                            <td>{{ $topic->week_number ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Holati:</th>
                            <td>
                                @if($topic->is_active)
                                    <span class="badge bg-success">Faol</span>
                                @else
                                    <span class="badge bg-secondary">Nofaol</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Yaratilgan:</th>
                            <td>{{ $topic->created_at->format('d.m.Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Yangilangan:</th>
                            <td>{{ $topic->updated_at->format('d.m.Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Amallar</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('subjects.topics.edit', [$subject, $topic]) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Tahrirlash
                        </a>

                        <form action="{{ route('subjects.topics.destroy', [$subject, $topic]) }}"
                              method="POST"
                              onsubmit="return confirm('Rostdan ham bu mavzuni o\'chirmoqchimisiz?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-trash"></i> O'chirish
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
