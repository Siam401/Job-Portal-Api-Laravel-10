@props(['routes' => false])

@php
  $routes = is_string($routes) ? json_decode($routes, true) : $routes;
@endphp

<ul
  class="font-medium flex flex-col p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-gray-50 md:flex-row md:space-x-8 md:mt-0 md:border-0 md:bg-white dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700">
  @if ($routes && count($routes) > 0)
    @foreach ($routes as $name => $link)
      <li>
        <a href="{{ $link }}"
          class="block py-2 pl-3 pr-4 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">{{ $name }}</a>
      </li>
    @endforeach
  @endif
</ul>
