<a href="{{ $element->content['link'] ?? '#' }}" 
   target="{{ $element->content['target'] ?? '_self' }}"
   class="btn btn-{{ $element->settings['style'] ?? 'primary' }} btn-{{ $element->settings['size'] ?? 'md' }} {{ $element->settings['fullWidth'] ?? false ? 'w-100' : '' }}">
    @if($element->settings['icon'] ?? false)
    <i class="{{ $element->settings['icon'] }}"></i>
    @endif
    {{ $element->content['text'] ?? 'Button' }}
</a>