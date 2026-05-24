@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1>Page Builder</h1>
                <a href="{{ route('page-builder.editor') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create New Page
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Slug</th>
                                <th>Status</th>
                                <th>Created By</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pages as $page)
                            <tr>
                                <td>{{ $page->title }}</td>
                                <td>
                                    <code>{{ $page->slug }}</code>
                                    @if($page->status === 'published')
                                    <a href="{{ route('page.show', $page->slug) }}" target="_blank" class="ms-2">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                    @endif
                                </td>
                                <td>
                                    @if($page->status === 'published')
                                        <span class="badge bg-success">Published</span>
                                    @else
                                        <span class="badge bg-warning">Draft</span>
                                    @endif
                                </td>
                                <td>{{ $page->creator->name ?? 'N/A' }}</td>
                                <td>{{ $page->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('page-builder.editor', $page->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="{{ route('page-builder.preview', $page->id) }}" class="btn btn-sm btn-info" target="_blank">
                                            <i class="fas fa-eye"></i> Preview
                                        </a>
                                        <form action="{{ route('page-builder.duplicate', $page->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-secondary">
                                                <i class="fas fa-copy"></i> Duplicate
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No pages found. Create your first page!</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{ $pages->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection