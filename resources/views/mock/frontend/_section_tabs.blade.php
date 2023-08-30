<ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="myTab" data-tabs-toggle="#myTabContent"
  role="tablist">
  @foreach ($sections as $item)
    <li class="mr-2" role="presentation">
      <button
        class="inline-block p-4 border-b-2 rounded-t-lg {{ $item->slug !== $active ? 'hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' : '' }}"
        id="{{ $item->slug }}-tab" data-tabs-target="#{{ $item->slug }}" type="button" role="tab"
        aria-controls="{{ $item->slug }}"
        aria-selected="{{ $item->slug !== $active ? 'false' : 'true' }}">{{ Str::title(str_replace('-', ' ', $item->slug)) }}</button>
    </li>
  @endforeach

</ul>
