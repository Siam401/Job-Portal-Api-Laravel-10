@extends('mock.layout')

@section('content')
  <div class="flex justify-between">
    <h1 class="text-3xl font-extrabold tracking-tight text-gray-600">All Jobs</h1>

    <div>

        <a href="{{ route('mock.job-questions.index') }}"
        class="text-white bg-yellow-500 hover:bg-yellow-700 focus:outline-none focus:ring-4 focus:ring-yellow-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-2 dark:bg-yellow-600 dark:hover:bg-yellow-700 dark:focus:ring-yellow-800">Job Questions</a>

        <a href="{{ route('mock.job.create') }}"
        class="text-white bg-green-500 hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-green-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">+
        Create</a>

    </div>
  </div>
  <div class="relative overflow-x-auto my-4">
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
      <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr>
          <th scope="col" class="px-6 py-3">
            No.
          </th>
          <th scope="col" class="px-6 py-3">
            Job Post
          </th>
          <th scope="col" class="px-6 py-3">
            Job Category
          </th>
          <th scope="col" class="px-6 py-3">
            Vacancy
          </th>
          <th scope="col" class="px-6 py-3">
            Deadline
          </th>
          <th scope="col" class="px-6 py-3 text-center">
            Action
          </th>
        </tr>
      </thead>
      <tbody>
        @if ($jobs->count() > 0)
          @foreach ($jobs as $i => $job)
            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
              <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                {{ $jobs->firstItem() + $i }}
              </th>
              <td class="px-6 py-4">
                {{ $job->title }}
              </td>
              <td class="px-6 py-4">
                {{ $job->jobFunction?->name }}
              </td>
              <td class="px-6 py-4">
                {{ $job->vacancy ?? 'N/A' }}
              </td>
              <td class="px-6 py-4">
                {{ date('j F, Y', strtotime($job->end_date)) }}
              </td>
              <td class="px-6 py-4 text-center">
                <a href="{{ route('mock.job.edit', $job->id) }}"
                  class="text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg px-3 py-2 text-xs text-center mr-2 mb-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800">Edit</a>

                <form action="{{ route('mock.job.destroy', $job->id) }}" method="POST" class="inline">
                  @csrf @method('DELETE')
                  <button type="submit"
                    class="text-red-700 hover:text-white border border-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg px-3 py-2 text-xs text-center mr-2 mb-2 dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900">Delete</button>
                </form>
              </td>
            </tr>
          @endforeach
        @endif
      </tbody>
    </table>

  </div>
  {{ $jobs->links() }}
@endsection
