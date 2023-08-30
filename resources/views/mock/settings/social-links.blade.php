@extends('mock.layout')

@section('content')
  <div class="bg-white rounded-lg shadow-lg px-8 py-6">
    <form action="{{ route('mock.settings.social-links') }}" method="POST" class="mb-10">
      @csrf

      <h4 class="text-2xl font-bold dark:text-white mb-5 pb-3 text-center">Edit Social Links</h4>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mx-auto justify-center">
        @foreach ($socialLinks as $social)
          <div class="border rounded px-6 py-4 has-preview-html">
            <div class="text-xl text-center dark:text-white mb-4">
              {{ reverseSlug($social->title) }}
              <span class="preview-html-target pt-2 ml-1">{!! $social->icon_image !!}</span>
            </div>
            <x-text-input name="social_links[{{ $social->title }}][url]" value="{{ $social->url }}" label="URL" />
            <x-text-input name="social_links[{{ $social->title }}][icon_image]" value="{{ $social->icon_image }}"
              label="Icon" class="preview-html" />
            <div class="flex justify-between">
              <x-text-input type="number" divClass="mr-3" name="social_links[{{ $social->title }}][serial]" value="{{ $social->serial }}"
                label="Serial" />
              <div class="grow">
                <label  class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Is Active ?</label>
                <select name="social_links[{{ $social->title }}][is_active]"
                  class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600">
                  <option value="1" {{ isTrue($social->is_active) ? 'selected' : '' }}>Yes</option>
                  <option value="0" {{ !isTrue($social->is_active) ? 'selected' : '' }}>No</option>
                </select>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      <div class="flex justify-center mt-4">
        <button type="submit"
          class="px-8 py-4 text-xl text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 rounded-lg text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
          Save
        </button>
      </div>
    </form>
  @endsection
