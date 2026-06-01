@extends('layouts.app')

@section('content')
<div id="page-builder-app">
    <page-builder 
        :page-id="{{ $page ? $page->id : 'null' }}"
        :initial-data='@json($page)'
        :element-types='@json($elementTypes)'
        :templates='@json($templates)'
    />
</div>
@endsection

@push('styles')
<style>
    body {
        overflow: hidden;
    }
    #app > .container-fluid {
        padding: 0;
        max-width: 100%;
    }
    .navbar {
        display: none;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('vendor/sortablejs/Sortable.min.js') }}"></script>
<script>
window.PageBuilderConfig = {
    apiUrl: '{{ url("/api/page-builder") }}',
    uploadUrl: '{{ route("page-builder.upload") }}',
    csrfToken: '{{ csrf_token() }}'
};
</script>
@endpush