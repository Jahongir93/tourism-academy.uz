{{-- Dynamic Form Field Component --}}
@php
    $fieldKey = $field->field_key;
    $fieldType = $field->field_type;
    $label = $field->label_uz;
    $placeholder = $field->placeholder ?? '';
    $isRequired = $field->is_required;
    $options = $field->options ?? [];
@endphp

@switch($fieldType)
    {{-- Heading --}}
    @case('heading')
        {{-- Headings are now shown in section headers, but we can still display sub-headings --}}
        @break

    {{-- Text, Email, Phone, Date inputs --}}
    @case('text')
    @case('email')
    @case('phone')
    @case('date')
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                {{ $label }}
                @if($isRequired)<span class="text-red-500 ml-1">*</span>@endif
            </label>
            <div class="relative">
                @if($fieldType === 'email')
                    <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                @elseif($fieldType === 'phone')
                    <i class="fas fa-phone absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                @elseif($fieldType === 'date')
                    <i class="fas fa-calendar absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                @elseif($fieldKey === 'jshshir')
                    <i class="fas fa-id-card absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                @endif
                <input type="{{ $field->getInputType() }}"
                       name="{{ $fieldKey }}"
                       class="form-input @if(in_array($fieldType, ['email', 'phone', 'date']) || $fieldKey === 'jshshir') pl-11 @endif @error($fieldKey) border-red-500 @enderror"
                       value="{{ old($fieldKey) }}"
                       placeholder="{{ $placeholder ?: $label }}"
                       @if($isRequired) required @endif
                       @if($fieldType === 'phone') pattern="[\+]?[0-9]{9,15}" @endif
                       @if($fieldKey === 'jshshir') maxlength="14" pattern="[0-9]{14}" @endif
                       @if($fieldKey === 'passport_series') maxlength="2" pattern="[A-Z]{2}" style="text-transform: uppercase;" @endif
                       @if($fieldKey === 'passport_number') maxlength="7" pattern="[0-9]{7}" @endif>
            </div>
            @error($fieldKey)
                <p class="mt-1 text-sm text-red-500 flex items-center">
                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                </p>
            @enderror
        </div>
        @break

    {{-- Textarea --}}
    @case('textarea')
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                {{ $label }}
                @if($isRequired)<span class="text-red-500 ml-1">*</span>@endif
            </label>
            <textarea name="{{ $fieldKey }}"
                      rows="3"
                      class="form-input @error($fieldKey) border-red-500 @enderror"
                      placeholder="{{ $placeholder ?: $label }}"
                      @if($isRequired) required @endif>{{ old($fieldKey) }}</textarea>
            @error($fieldKey)
                <p class="mt-1 text-sm text-red-500 flex items-center">
                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                </p>
            @enderror
        </div>
        @break

    {{-- Select Dropdown --}}
    @case('select')
        @if($fieldKey === 'faculty_id')
            {{-- Special handling for faculty --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    {{ $label }}
                    @if($isRequired)<span class="text-red-500 ml-1">*</span>@endif
                </label>
                <div class="relative">
                    <i class="fas fa-building absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <select name="{{ $fieldKey }}"
                            id="facultySelect"
                            class="form-input form-select pl-11 @error($fieldKey) border-red-500 @enderror"
                            @if($isRequired) required @endif>
                        <option value="">Fakultet tanlang...</option>
                        @foreach($faculties ?? [] as $faculty)
                            <option value="{{ $faculty->id }}" {{ old($fieldKey) == $faculty->id ? 'selected' : '' }}>
                                {{ $faculty->name_uz ?? $faculty->name_ru ?? $faculty->name_en }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error($fieldKey)
                    <p class="mt-1 text-sm text-red-500 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                    </p>
                @enderror
            </div>
        @elseif($fieldKey === 'specialty_id')
            {{-- Special handling for specialty --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    {{ $label }}
                    @if($isRequired)<span class="text-red-500 ml-1">*</span>@endif
                </label>
                <div class="relative">
                    <i class="fas fa-graduation-cap absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <select name="{{ $fieldKey }}"
                            id="specialtySelect"
                            class="form-input form-select pl-11 @error($fieldKey) border-red-500 @enderror"
                            @if($isRequired) required @endif>
                        <option value="">Avval fakultet tanlang...</option>
                    </select>
                </div>
                @error($fieldKey)
                    <p class="mt-1 text-sm text-red-500 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                    </p>
                @enderror
            </div>
        @else
            {{-- Regular select --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    {{ $label }}
                    @if($isRequired)<span class="text-red-500 ml-1">*</span>@endif
                </label>
                <select name="{{ $fieldKey }}"
                        class="form-input form-select @error($fieldKey) border-red-500 @enderror"
                        @if($isRequired) required @endif>
                    <option value="">Tanlang...</option>
                    @foreach($options as $value => $text)
                        <option value="{{ $value }}" {{ old($fieldKey) == $value ? 'selected' : '' }}>
                            {{ is_string($text) ? $text : $value }}
                        </option>
                    @endforeach
                </select>
                @error($fieldKey)
                    <p class="mt-1 text-sm text-red-500 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                    </p>
                @enderror
            </div>
        @endif
        @break

    {{-- Radio Buttons --}}
    @case('radio')
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-3">
                {{ $label }}
                @if($isRequired)<span class="text-red-500 ml-1">*</span>@endif
            </label>
            <div class="grid grid-cols-2 gap-3">
                @foreach($options as $value => $text)
                    <label class="custom-radio {{ old($fieldKey) == $value ? 'selected' : '' }}">
                        <input type="radio"
                               name="{{ $fieldKey }}"
                               value="{{ $value }}"
                               class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                               {{ old($fieldKey) == $value ? 'checked' : '' }}
                               @if($isRequired && $loop->first) required @endif
                               onchange="document.querySelectorAll('input[name={{ $fieldKey }}]').forEach(r => r.closest('.custom-radio').classList.remove('selected')); this.closest('.custom-radio').classList.add('selected')">
                        <span class="ml-3 text-gray-700">{{ is_string($text) ? $text : $value }}</span>
                    </label>
                @endforeach
            </div>
            @error($fieldKey)
                <p class="mt-2 text-sm text-red-500 flex items-center">
                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                </p>
            @enderror
        </div>
        @break

    {{-- Checkboxes --}}
    @case('checkbox')
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-3">
                {{ $label }}
                @if($isRequired)<span class="text-red-500 ml-1">*</span>@endif
            </label>
            <div class="space-y-2">
                @foreach($options as $value => $text)
                    <label class="custom-checkbox {{ is_array(old($fieldKey)) && in_array($value, old($fieldKey)) ? 'selected' : '' }}">
                        <input type="checkbox"
                               name="{{ $fieldKey }}[]"
                               value="{{ $value }}"
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                               {{ is_array(old($fieldKey)) && in_array($value, old($fieldKey)) ? 'checked' : '' }}
                               onchange="this.closest('.custom-checkbox').classList.toggle('selected', this.checked)">
                        <span class="ml-3 text-gray-700">{{ is_string($text) ? $text : $value }}</span>
                    </label>
                @endforeach
            </div>
            @error($fieldKey)
                <p class="mt-2 text-sm text-red-500 flex items-center">
                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                </p>
            @enderror
        </div>
        @break

    {{-- File Upload --}}
    @case('file')
        @php
            $fileConfig = $field->file_config ?? [];
            $maxSize = $fileConfig['max_size'] ?? 5120;
            $extensions = $fileConfig['allowed_extensions'] ?? ['pdf', 'jpg', 'jpeg', 'png'];
            $acceptString = $field->getAcceptString();
            $maxSizeMB = round($maxSize / 1024, 1);
        @endphp
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                {{ $label }}
                @if($isRequired)<span class="text-red-500 ml-1">*</span>@endif
            </label>
            <div class="file-upload-area" id="{{ $fieldKey }}Upload">
                <input type="file"
                       name="{{ $fieldKey }}"
                       id="{{ $fieldKey }}Input"
                       accept="{{ $acceptString }}"
                       @if($isRequired) required @endif>
                <div class="flex flex-col items-center">
                    <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center mb-3">
                        <i class="fas fa-cloud-upload-alt text-2xl text-blue-500 file-icon"></i>
                    </div>
                    <p class="text-gray-700 font-medium file-label">Fayl tanlang yoki bu yerga tashlang</p>
                    <p class="text-xs text-gray-500 mt-2">
                        <span class="font-medium">{{ strtoupper(implode(', ', $extensions)) }}</span>
                        <span class="mx-1">|</span>
                        Maksimum {{ $maxSizeMB }}MB
                    </p>
                </div>
            </div>
            @error($fieldKey)
                <p class="mt-2 text-sm text-red-500 flex items-center">
                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                </p>
            @enderror
        </div>
        @break

    {{-- Default fallback --}}
    @default
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                {{ $label }}
                @if($isRequired)<span class="text-red-500 ml-1">*</span>@endif
            </label>
            <input type="text"
                   name="{{ $fieldKey }}"
                   class="form-input @error($fieldKey) border-red-500 @enderror"
                   value="{{ old($fieldKey) }}"
                   placeholder="{{ $placeholder ?: $label }}"
                   @if($isRequired) required @endif>
            @error($fieldKey)
                <p class="mt-1 text-sm text-red-500 flex items-center">
                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                </p>
            @enderror
        </div>
@endswitch
