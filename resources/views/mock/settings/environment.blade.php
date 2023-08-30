@extends('mock.layout')

@section('content')
  <div class="bg-white rounded-lg shadow-lg px-8 py-6">
    <form action="{{ route('mock.settings.environment.save') }}" method="POST" class="mb-10">
      @csrf

      <h4 class="text-2xl font-bold dark:text-white mb-5 pb-3 text-center">
        Environment Configurations
      </h4>

      <input type="hidden" name="active" value="{{ $active }}" />

      <div id="accordion-flush" data-accordion="collapse"
        data-active-classes="bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
        data-inactive-classes="text-gray-500 dark:text-gray-400">
        @foreach ($configs as $name => $inputs)
          <h2 id="accordion-flush-heading-{{ $name }}">
            <button type="button"
              class="flex items-center justify-between w-full py-5 font-medium text-left text-gray-500 border-b border-gray-200 dark:border-gray-700 dark:text-gray-400 accordion-env" data-name="{{ $name }}"
              data-accordion-target="#accordion-flush-body-{{ $name }}" aria-expanded="{!! $active === $name ? 'true' : 'false' !!}"
              aria-controls="accordion-flush-body-{{ $name }}">
              <span>{{ ucwords($name) }} Settings</span>
              <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0" aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 5 5 1 1 5" />
              </svg>
            </button>
          </h2>
          <div id="accordion-flush-body-{{ $name }}" class="hidden"
            aria-labelledby="accordion-flush-heading-{{ $name }}">
            <div class="py-5 border-b border-gray-200 dark:border-gray-700 px-4 md:px-8">
              @include('mock.settings._env_inputs')
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
