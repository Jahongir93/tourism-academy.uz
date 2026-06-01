<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->title }}</title>
    <meta name="description" content="{{ $page->meta_description }}">
    <meta name="keywords" content="{{ $page->meta_keywords }}">
    
    <!-- Bootstrap CSS -->
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    
    <!-- Custom CSS -->
    @if($page->assets)
    <style>
        {{ $page->assets->custom_css }}
    </style>
    @endif
    
    <style>
        .pb-section {
            position: relative;
        }
        .pb-column {
            padding: 15px;
        }
        .pb-element {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="page-content">
        @foreach($page->sections as $section)
        <section class="pb-section" style="{{ $section->settings['style'] ?? '' }}">
            <div class="container{{ $section->settings['fullWidth'] ?? false ? '-fluid' : '' }}">
                <div class="row">
                    @foreach($section->columns as $column)
                    <div class="col-md-{{ $column->width }} pb-column">
                        @foreach($column->elements as $element)
                        <div class="pb-element pb-element-{{ $element->type }}">
                            @include('page-builder.elements.' . $element->type, ['element' => $element])
                        </div>
                        @endforeach
                    </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endforeach
    </div>

    <!-- Bootstrap JS -->
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    
    <!-- Custom JS -->
    @if($page->assets)
    <script>
        {{ $page->assets->custom_js }}
    </script>
    @endif
</body>
</html>