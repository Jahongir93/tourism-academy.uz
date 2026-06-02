@if($element->content['link'] ?? false)
<a href="{{ $element->content['link'] }}" {{ $element->content['lightbox'] ?? false ? 'data-lightbox="image"' : '' }}>
@endif
    <img src="{{ $element->content['src'] ?? '{{ asset('images/ext/placeholder.jpg') }}' }}" 
         alt="{{ $element->content['alt'] ?? '' }}"
         class="img-fluid"
         style="width: {{ $element->settings['width'] ?? 'auto' }}; 
                height: {{ $element->settings['height'] ?? 'auto' }}; 
                object-fit: {{ $element->settings['objectFit'] ?? 'cover' }};
                border-radius: {{ $element->settings['borderRadius'] ?? 0 }}px;">
@if($element->content['link'] ?? false)
</a>
@endif