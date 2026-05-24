@extends('layouts.dashboard-new')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h3>Kurs Mavzulari</h3>
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
                <div class="card-header">
                    <h5 class="mb-0">Mening Fanlarim</h5>
                </div>
                <div class="card-body">
                    @if($subjects->isEmpty())
                        <div class="alert alert-info">
                            Sizga hali fan biriktirilmagan.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Fan nomi</th>
                                        <th>Guruhlar soni</th>
                                        <th>Mavzular soni</th>
                                        <th>Amallar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($subjects as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <strong>{{ $item['subject']->name }}</strong>
                                                @if($item['subject']->code)
                                                    <br>
                                                    <small class="text-muted">{{ $item['subject']->code }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ $item['groups_count'] }} guruh
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">
                                                    {{ $item['topics_count'] }} mavzu
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('teacher.topics.subject', $item['subject']->id) }}"
                                                   class="btn btn-sm btn-primary">
                                                    <i class="bi bi-list-ul"></i> Mavzular
                                                </a>
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
