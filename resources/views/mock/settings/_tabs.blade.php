<div
  class="text-sm font-medium text-center text-gray-500 border-b border-gray-200 dark:text-gray-400 dark:border-gray-700">
  <ul class="flex flex-wrap -mb-px">
    <li class="mr-2">
      <a href="{{ route('mock.settings.index') }}"
        @if (!isset($name) || $name === 'system') class="tab-active-link active" aria-current="page"
        @else
        class="tab-inactive-link" aria-current="page" @endif>System
        Configuration</a>
    </li>
    <li class="mr-2">
      <a href="{{ route('mock.settings.environment.index') }}"
        @if (isset($name) && $name == 'environment') class="tab-active-link active" aria-current="page"
      @else
      class="tab-inactive-link" aria-current="page" @endif>Environment</a>
    </li>
    <li class="mr-2">
      <a href="{{ route('mock.settings.social-links') }}"
        @if (isset($name) && $name == 'social-links') class="tab-active-link active" aria-current="page"
      @else
      class="tab-inactive-link" aria-current="page" @endif>Social
        Links</a>
    </li>
    <li class="mr-2">
      <a href="{{ route('mock.settings.social-auths') }}"
        @if (isset($name) && $name == 'social-auths') class="tab-active-link active" aria-current="page"
      @else
      class="tab-inactive-link" aria-current="page" @endif>Social Auths</a>
    </li>
  </ul>
</div>
