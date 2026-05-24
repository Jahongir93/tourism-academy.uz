@php
    use App\Helpers\TemplateHelper;
    $templateLayout = TemplateHelper::getLayout();
@endphp

@extends($templateLayout)

@section('content')
    @yield('page-content')
@endsection
