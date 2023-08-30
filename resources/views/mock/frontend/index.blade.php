@extends('mock.layout')

@section('content')
  <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
    @include('mock.frontend._section_tabs')
  </div>
  <div id="myTabContent">
    @foreach ($sections as $item)
      <div class="hidden p-6 rounded-lg border" id="{{ $item->slug }}" role="tabpanel"
        aria-labelledby="{{ $item->slug }}-tab">

        <form action="{{ route('mock.frontend.update') }}" enctype="multipart/form-data" method="POST" class="mb-10">
          @csrf
          <input type="hidden" name="name" value="{{ $item->slug }}" hidden>
          <div class="grid gap-6 gap-y-0 mb-6 md:grid-cols-2 mt-4">
            <x-text-input name="title" required="1" label="Section Title" value="{{ $item->title }}" />
            <x-text-input name="subtitle" required="1" label="Subtitle" value="{{ $item->subtitle }}" />
            <x-textarea name="description" label="Description" value="{{ $item->description }}" />
            <div class="flex justify-between preview-image-container">
              <x-file-input name="image" label="Section Image" divClass="grow" class="preview-image" />

              @if ($item->image)
                <img style="max-width: 200px" class="h-auto max-w-xl rounded-lg ml-4" src="{{ getFile($item->image) }}"
                  alt="Section Image">
              @endif
            </div>
          </div>

          <div>
            <label class="relative inline-flex items-center mr-5 cursor-pointer">
              <input type="checkbox" value="1" name='is_active' class="sr-only peer"
                {{ $item->is_active ? 'checked' : '' }}>
              <div
                class="w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-focus:ring-4 peer-focus:ring-green-300 dark:peer-focus:ring-green-800 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600">
              </div>
              <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">Is Active?</span>
            </label>
          </div>


          @includeIf('mock.frontend.partials.' . $item->slug, [
              'items' => $item->sectionItems,
              'section' => $item->slug,
          ])

          <div class="flex justify-center mt-4">
            <button type="submit"
              class="px-6 py-3.5 text-base font-medium text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 rounded-lg text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
              Save
            </button>

          </div>

        </form>
      </div>
    @endforeach

  </div>
@endsection
