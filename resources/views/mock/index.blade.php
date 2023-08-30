@extends('mock.layout')

@section('content')
  <dl class="grid grid-cols-1 gap-8 w-100 mx-auto text-gray-900 dark:text-white sm:py-8 md:grid-cols-3">
    <div class="flex flex-col items-center justify-center border rounded-xl mx-4 p-8">
      <dt class="mb-2 text-3xl font-extrabold">{{ $jobs }}</dt>
      <dd class="text-gray-500 dark:text-gray-400">Active Jobs</dd>
    </div>
    <div class="flex flex-col items-center justify-center border rounded-xl mx-4 p-8">
      <dt class="mb-2 text-3xl font-extrabold">{{ $applicants }}</dt>
      <dd class="text-gray-500 dark:text-gray-400">Total Applicants</dd>
    </div>
    <div class="flex flex-col items-center justify-center border rounded-xl mx-4 p-8">
      <dt class="mb-2 text-3xl font-extrabold">{{ $applications }}</dt>
      <dd class="text-gray-500 dark:text-gray-400">Total Job Applications</dd>
    </div>
  </dl>
@endsection
