<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->title }} - Preview</title>
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
            padding: 20px 0;
        }
        .pb-column {
            padding: 15px;
        }
        .pb-element {
            margin-bottom: 20px;
        }
        .preview-banner {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #333;
            color: white;
            padding: 10px;
            text-align: center;
            z-index: 9999;
        }
        body {
            padding-top: 50px;
        }
    </style>
</head>
<body>
    <div class="preview-banner">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <span>Preview Mode - {{ $page->status === 'published' ? 'Published' : 'Draft' }}</span>
                <div>
                    <a href="{{ route('page-builder.editor', $page->id) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    @if($page->status === 'published')
                    <a href="{{ route('page.show', $page->slug) }}" class="btn btn-sm btn-success" target="_blank">
                        <i class="fas fa-external-link-alt"></i> View Live
                    </a>
                    @else
                    <form action="{{ route('page-builder.publish', $page->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-success">
                            <i class="fas fa-globe"></i> Publish
                        </button>
                    </form>
                    @endif
                    <a href="{{ route('page-builder.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

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