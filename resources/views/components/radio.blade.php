@props(['name', 'label' => 'Label', 'class' => '', 'divClass' => '', 'value' => 1, 'checked' => false, 'id' => null])

<div class="flex items-center mb-4 {{ $divClass }}">
  <input id="{{ $id ?? slug($name, true) }}" type="radio" value="{{ $value }}" name="{{ $name }}" {{ $checked ? 'checked' : '' }}
    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600 {{ $class }}">
  <label for="{{ $id ?? slug($name, true) }}"
    class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{ $label }}</label>
</div>
