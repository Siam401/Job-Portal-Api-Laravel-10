@extends('mock.layout')

@section('content')
  @include('mock.settings._tabs')

  <div class="bg-white rounded-lg shadow-lg px-8 py-6">
    <form action="{{ route('mock.settings.update', ['name' => 'configs']) }}" method="POST" class="mb-10">
      @csrf

      <h4 class="text-2xl font-bold dark:text-white mb-5 border-b pb-3">System Configuration</h4>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach ($configs as $item)
          @include('mock.settings._inputs', ['item' => $item])
        @endforeach
      </div>

      <div class="flex justify-center mt-4">
        <button type="submit"
          class="px-8 py-4 text-xl text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 rounded-lg text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
          Save
        </button>
      </div>
    </form>

    <form action="{{ route('mock.settings.update', ['name' => 'theme']) }}" method="POST" class="mb-10" enctype="multipart/form-data">
      @csrf

      <h4 class="text-2xl font-bold dark:text-white mb-5 border-b pb-3">Theme Settings</h4>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach ($theme as $item)
          @include('mock.settings._inputs', ['item' => $item])
        @endforeach
      </div>

      <div class="flex justify-center mt-4">
        <button type="submit"
          class="px-8 py-4 text-xl text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 rounded-lg text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
          Save
        </button>
      </div>
    </form>

    <form action="{{ route('mock.settings.update', ['name' => 'seo']) }}" method="POST" class="mb-10" enctype="multipart/form-data">
      @csrf

      <h4 class="text-2xl font-bold dark:text-white mb-5 border-b pb-3">Seo Settings</h4>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach ($seo as $item)
          @include('mock.settings._inputs', ['item' => $item])
        @endforeach
      </div>

      <div class="flex justify-center mt-4">
        <button type="submit"
          class="px-8 py-4 text-xl text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 rounded-lg text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
          Save
        </button>
      </div>
    </form>

    <form action="{{ route('mock.settings.update', ['name' => 'other']) }}" method="POST" class="mb-10">
      @csrf

      <h4 class="text-2xl font-bold dark:text-white mb-5 border-b pb-3">Other Settings</h4>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach ($other as $item)
          @include('mock.settings._inputs', ['item' => $item])
        @endforeach
      </div>

      <div class="flex justify-center mt-4">
        <button type="submit"
          class="px-8 py-4 text-xl text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 rounded-lg text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
          Save
        </button>
      </div>
    </form>
  </ @endsection
