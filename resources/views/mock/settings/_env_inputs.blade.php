@if (isset($inputs) && $inputs->count() > 0)

  @foreach ($inputs as $i => $item)
    @php
      $class = $i % 2 == 0 ? 'bg-gray-50 dark:bg-gray-800 dark:border-gray-700' : 'bg-white dark:bg-gray-900';
    @endphp
    <div class="grid grid-cols-1 gap-2 md:grid-cols-3">
      <div class="{{ $class }} p-3">
        <label for="{{ $item->name }}" class="block mt-2 text-sm font-medium text-gray-900 dark:text-white">
          {{ strtoupper($item->name) }}
        </label>
      </div>
      <div class="col-span-2 {{ $class }} pt-2 px-2">
        @if ($item->data_type === 'integer' || $item->data_type === 'number')
          <x-text-input type="number" name="env[{{ $item->name }}]" :value="$item->value" divClass="!mb-2" />
        @elseif ($item->data_type === 'boolean')
          <div class="flex">
            <x-radio name="env[{{ $item->name }}]" value="ok" checked="{{ isTrue($item->value) }}" divClass="!mb-2 mr-4"
              label="Yes" />
            <x-radio name="env[{{ $item->name }}]" value="no" checked="{{ !isTrue($item->value) }}"
              divClass="!mb-2" label="No" />
          </div>
        @else
          <x-text-input name="env[{{ $item->name }}]" :value="$item->value" divClass="!mb-2" />
        @endif
        <input type="hidden" name="data_type[{{ $item->name }}]" value="{{ $item->data_type }}" />
      </div>
    </div>
  @endforeach
@endif
