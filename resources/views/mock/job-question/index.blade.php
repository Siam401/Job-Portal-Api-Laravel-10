@extends('mock.layout')

@section('content')
  <h1 class="text-3xl font-extrabold tracking-tight text-gray-600">Job Questions</h1>

  <form action="{{ route('mock.job-questions.save') }}" enctype="multipart/form-data" method="POST" class="mb-10">
    @csrf

    <div class="relative overflow-x-auto">
      <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400 action-table" id="action-job-questions">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
          <tr>
            <th scope="col" class="px-6 py-3">
              Question
            </th>
            <th scope="col" class="px-6 py-3">
              Is Required
            </th>
            <th scope="col" class="px-6 py-3">
              Is Active
            </th>
            <th scope="col" class="px-6 py-3 text-center">
              Remove
            </th>

          </tr>
        </thead>
        <tbody>
          @foreach ($jobQuestions as $item)
            <tr class="border-b border-gray-200 dark:border-gray-700" data-id="{{ $item->id }}">
              <td class="px-6 py-4">
                <x-text-input divClass="!mb-0" name="questions[{{ $item->id }}][question]"
                  value="{{ $item->question }}" />
              </td>
              <td class="px-6 py-4">
                <x-checkbox name="questions[{{ $item->id }}][is_required]" checked="{!! $item->is_required ? '1' : '0' !!}"
                  value="1" label="Yes" />
              </td>
              <td class="px-6 py-4">
                <x-checkbox name="questions[{{ $item->id }}][is_active]" checked="{!! $item->is_active ? '1' : '0' !!}"
                  value="1" label="Yes" />
              </td>

              <td class="px-6 py-4 text-center">
                <input type="hidden" name="questions[{{ $item->id }}][id]" value="{{ $item->id }}">
                <button type="button"
                  class="px-3 py-2 text-xs font-medium text-center text-white bg-red-700 rounded-lg hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800 delete-row">
                  <i class="fa-regular fa-circle-xmark"></i></button>
              </td>
            </tr>
          @endforeach

          <tr class="action-add-tr">
            <td class="px-6 py-4" rowspan="3">
              <input type="hidden" name="delete_items" value="">
              <button type="button"
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center mr-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 add-row">
                <i class="fa-solid fa-plus"></i>
              </button>
            </td>
          </tr>

          <tr class="hidden form-tr border-b border-gray-200 dark:border-gray-700">
            <td class="px-6 py-4">
              <x-text-input divClass="!mb-0" disabled placeholder="Enter question" name="questions[___][question]" />
            </td>
            <td class="px-6 py-4">
              <x-checkbox name="questions[___][is_required]" disabled value="1" label="Yes" />
            </td>
            <td class="px-6 py-4">
              <x-checkbox name="questions[___][is_active]" disabled value="1" label="Yes" />
            </td>
            <td class="px-6 py-4 text-center">
              <button type="button"
                class="px-3 py-2 text-xs font-medium text-center text-white bg-red-700 rounded-lg hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800 delete-row">
                <i class="fa-regular fa-circle-xmark"></i></button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="flex justify-center mt-4">
      <button type="submit"
        class="px-6 py-3.5 text-base font-medium text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 rounded-lg text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
        Save
      </button>

    </div>
  </form>
@endsection
