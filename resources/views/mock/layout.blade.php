<!-- meta tags and other links -->
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  {{-- <title>{{ $general->siteName($pageTitle ?? '') }}</title> --}}
  <title>{{ 'Mock Admin' }} | Job Portal</title>

  <link rel="shortcut icon" type="image/png" href="{{ getFile(getSetting('site_favicon')) }}">
  @vite(['resources/mock/styles.scss'])

  @stack('style')
</head>

<body>
  <header>
    <nav class="bg-white border-gray-200 dark:bg-gray-900 container mx-auto">
      <div class="flex flex-wrap items-center justify-between mx-auto p-4">
        <a href="{{ route('mock') }}" class="flex items-center">
          <img src="{{ getFile(getSetting('site_logo')) ?? 'https://nextitltd.com/img/interface/logo.png' }}" class="h-8 mr-3" alt="Logo" />
          <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white">Admin</span>
        </a>
        <button data-collapse-toggle="navbar-default" type="button"
          class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600"
          aria-controls="navbar-default" aria-expanded="false">
          <span class="sr-only">Open main menu</span>
          <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M1 1h15M1 7h15M1 13h15" />
          </svg>
        </button>
        <div class="hidden w-full md:block md:w-auto" id="navbar-default">
          @php
            $routes = [
                'Company' => route('mock.company.index'),
                'Frontend' => route('mock.frontend'),
                'Jobs' => route('mock.job.index'),
                'Settings' => route('mock.settings.index'),
            ];
          @endphp

          <x-navbar :routes="$routes" />
        </div>
      </div>
    </nav>
  </header>

  <div class="container mx-auto p-4">
    <x-alerts />

    @yield('content')
  </div>

  @vite(['resources/mock/script.js'])
  @stack('script')

</body>

</html>
