@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Dars materiallari</h1>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('teacher.lms.upload') }}" class="btn btn-primary">
                <i class="fas fa-upload mr-2"></i>Yangi material yuklash
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Mavjud materiallar</h6>
                </div>
                <div class="card-body">
                    @if(empty($materials))
                    <div class="text-center py-5">
                        <i class="fas fa-folder-open fa-4x text-gray-300 mb-3"></i>
                        <p class="text-muted">Hozircha material mavjud emas</p>
                        <a href="{{ route('teacher.lms.upload') }}" class="btn btn-sm btn-primary">
                            Material yuklash
                        </a>
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nomi</th>
                                    <th>Turi</th>
                                    <th>Hajmi</th>
                                    <th>Yuklangan</th>
                                    <th>Harakatlar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($materials as $material)
                                <tr>
                                    <td>{{ $material->name ?? 'Material' }}</td>
                                    <td>{{ $material->type ?? 'PDF' }}</td>
                                    <td>{{ $material->size ?? '1.2 MB' }}</td>
                                    <td>{{ $material->created_at ?? now()->format('d.m.Y') }}</td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
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