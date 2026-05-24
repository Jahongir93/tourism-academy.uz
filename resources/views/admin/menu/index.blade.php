@extends('layouts.dashboard-new')

@section('title', 'Menyu boshqaruvi')
@section('page-title', 'Menyu boshqaruvi')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-0">Sayt menyularini boshqaring</p>
                </div>
                <a href="{{ route('admin.menu.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Yangi menyu
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

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0">
            <h5 class="mb-0">Menyular</h5>
        </div>
        <div class="card-body">
            @if($menuItems->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th>Tartib</th>
                                <th>Nomi (UZ)</th>
                                <th>URL</th>
                                <th>Ikon</th>
                                <th>Holat</th>
                                <th>Pastki menyular</th>
                                <th class="text-end">Amallar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($menuItems as $menu)
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary">{{ $menu->order }}</span>
                                    </td>
                                    <td>
                                        @if($menu->icon)
                                            <i class="{{ $menu->icon }} me-2"></i>
                                        @endif
                                        <strong>{{ $menu->label_uz }}</strong>
                                        @if($menu->open_in_new_tab)
                                            <i class="fas fa-external-link-alt text-muted small ms-1"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <code class="small">{{ $menu->url }}</code>
                                    </td>
                                    <td>
                                        @if($menu->icon)
                                            <code class="small">{{ $menu->icon }}</code>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($menu->is_active)
                                            <span class="badge bg-success">Faol</span>
                                        @else
                                            <span class="badge bg-secondary">Nofaol</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($menu->children->count() > 0)
                                            <span class="badge bg-info">{{ $menu->children->count() }} ta</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.menu.edit', $menu) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.menu.destroy', $menu) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Rostdan ham o\'chirmoqchimisiz?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                @if($menu->children->count() > 0)
                                    @foreach($menu->children->sortBy('order') as $child)
                                        <tr class="table-light">
                                            <td>
                                                <span class="badge bg-light text-dark">{{ $menu->order }}.{{ $child->order }}</span>
                                            </td>
                                            <td class="ps-5">
                                                <i class="fas fa-level-up-alt fa-rotate-90 text-muted me-2"></i>
                                                @if($child->icon)
                                                    <i class="{{ $child->icon }} me-2"></i>
                                                @endif
                                                {{ $child->label_uz }}
                                            </td>
                                            <td>
                                                <code class="small">{{ $child->url }}</code>
                                            </td>
                                            <td>
                                                @if($child->icon)
                                                    <code class="small">{{ $child->icon }}</code>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($child->is_active)
                                                    <span class="badge bg-success">Faol</span>
                                                @else
                                                    <span class="badge bg-secondary">Nofaol</span>
                                                @endif
                                            </td>
                                            <td>-</td>
                                            <td class="text-end">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.menu.edit', $child) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.menu.destroy', $child) }}" method="POST" class="d-inline"
                                                          onsubmit="return confirm('Rostdan ham o\'chirmoqchimisiz?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-bars fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Menyular mavjud emas</h5>
                    <p class="text-muted mb-4">Sayt uchun menyularni qo'shing</p>
                    <a href="{{ route('admin.menu.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Birinchi menyuni yaratish
                    </a>
                </div>
            @endif
        </div>
    </div>

    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-info text-white">
            <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Ma'lumot</h6>
        </div>
        <div class="card-body">
            <p class="mb-2"><strong>FontAwesome ikonlar:</strong> <code>fas fa-home</code>, <code>fas fa-info-circle</code>, <code>fas fa-envelope</code></p>
            <p class="mb-0"><strong>URL misollari:</strong> <code>/about</code>, <code>/contact</code>, <code>https://example.com</code></p>
        </div>
    </div>
</div>
@endsection
