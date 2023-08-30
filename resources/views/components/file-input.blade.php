@props(['name', 'placeholder' => '', 'required' => false, 'label' => '', 'class' => '', 'divClass' => '', 'id' => null])

<div class="mb-4 {{ $divClass }}">
  @if ($label)
    <label for="{{ $name }}"
      class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ $label }} @if ($required)
        <span class="text-red-500">*</span>
      @endif
    </label>
  @endif
  <input
    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 {{ $class }}"
    id="{{ $id ?? slug($name, true) }}" type="file" name="{{ $name }}" {{ $required ? 'required' : '' }} />

  @error($name)
    <div class="text-sm text-red-600">{{ $message }}</div>
  @enderror
</div>
