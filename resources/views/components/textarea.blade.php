@props(['name', 'placeholder' => '', 'required' => false, 'label' => '', 'class' => '', 'value' => null, 'id' => null, 'disabled' => false, 'divClass' => ''])

<div class="mb-4 {{ $divClass }}">
  @if ($label)
    <label for="{{ $name }}"
      class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ $label }} @if ($required)
        <span class="text-red-500">*</span>
      @endif
    </label>
  @endif
  <textarea id="{{ $id ?? slug($name, true) }}" {{ $disabled ? 'disabled' : '' }} rows="4" name="{{ $name }}"
    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 {{ $class }}"
    placeholder={{ $placeholder ?? '' }} {{ $required ? 'required' : '' }}>{{ $value ?? old($name) }}</textarea>

  @error($name)
    <div class="text-sm text-red-600">{{ $message }}</div>
  @enderror
</div>
