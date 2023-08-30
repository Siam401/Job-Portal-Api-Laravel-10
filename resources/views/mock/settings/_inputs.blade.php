@if ($item->data_type == 'text')
  <x-textarea name="{{ $item->name }}" label="{{ reverseSlug($item->name, true) }}" :value="$item->value" />
@elseif ($item->data_type == 'number')
  <x-text-input type="number" name="{{ $item->name }}" label="{{ reverseSlug($item->name, true) }}"
    :value="$item->value" />
@elseif ($item->data_type == 'image')
  <div class="flex justify-between preview-image-container">
    <x-file-input name="{{ $item->name }}" label="{{ reverseSlug($item->name, true) }}" divClass="grow"
      class="preview-image" />

    <img style="max-width: 200px; min-width: 80px; max-height: 80px" class="h-auto max-w-xl rounded-lg ml-4"
      src="{{ $item->value ? getFile($item->value) : null }}" alt="Image">
  </div>
@elseif ($item->data_type == 'boolean')
  <div class="mb-4">
    <label for="{{ $item->name }}"
      class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ reverseSlug($item->name, true) }}</label>
    <div class="flex">
      <x-radio name="{{ $item->name }}" divClass="mr-5 !mb-2" label="No" value="0"
        checked="{!! !isTrue($item->value) !!}" />
      <x-radio name="{{ $item->name }}" divClass="!mb-2" label="Yes" value="1"
        checked="{!! isTrue($item->value) !!}" />
    </div>
  </div>
@else
  <x-text-input name="{{ $item->name }}" label="{{ reverseSlug($item->name, true) }}" :value="$item->value" />
@endif
