@props(['name', 'multiple' => false, 'debug' => false, 'default' => '', 'required' => false, 'options' => [], 'label' => '', 'class' => '', 'defaultValue' => '', 'labelClass' => '', 'divClass' => '', 'value' => null, 'id' => null , 'disabled' => false])

@php
  $value = $value ?? old($name);
  $defaultClass = 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500';
@endphp

<div class="mb-4 {{ $divClass }}">
  @if ($label)
    <label for="{{ $name }}"
      class="block mb-2 text-sm font-medium text-gray-900 dark:text-white {{ $labelClass }}">{{ $label }}
      @if ($required)
        <span class="text-red-500">*</span>
      @endif
    </label>
  @endif
  <select name="{{ $name }}" {{ $multiple ? 'multiple' : '' }} {{ $required ? 'required' : '' }}
    id="{{ $id ?? slug($name, true) }}" class="{!! $defaultClass !!} {!! $class !!}" {!! $disabled ? 'disabled' : '' !!}>

    @if ($multiple)
      @if ($default)
        <option value="{{ $defaultValue }}"
          {{ (is_array($value) && in_array($defaultValue, $value)) || is_null($value) ? 'selected' : '' }}>
          {{ $default }}</option>
      @endif

      @if ($options)
        @php
          $options = is_string($options) ? json_decode($options) : $options;
        @endphp
        @foreach ($options as $id => $text)
          <option value="{{ $id }}" {{ is_array($value) && in_array($id, $value) ? 'selected' : '' }}>
            {{ $text }}</option>
        @endforeach
      @endif
    @else
      @if ($default)
        <option value="{{ $defaultValue }}" {{ is_null($value) || $defaultValue == $value ? 'selected' : '' }}>
          {{ $default }}</option>
      @endif

      @if ($options)
        @php
          $options = is_string($options) ? json_decode($options) : $options;
        @endphp
        @foreach ($options as $id => $text)
          <option value="{{ $id }}" {{ $id == $value ? 'selected' : '' }}>{{ $text }}</option>
        @endforeach
      @endif
    @endif

  </select>

  @error($name)
    <div class="text-sm text-red-600">{{ $message }}</div>
  @enderror
</div>
