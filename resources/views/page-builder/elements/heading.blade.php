<{{ $element->settings['tag'] ?? 'h2' }} 
    class="heading-element" 
    style="text-align: {{ $element->settings['align'] ?? 'left' }}; {{ $element->settings['style'] ?? '' }}">
    {{ $element->content['text'] ?? 'Heading' }}
</{{ $element->settings['tag'] ?? 'h2' }}>