@extends('mock.layout')

@section('content')
  <div class="flex justify-between">
    <h1 class="text-3xl font-extrabold tracking-tight text-gray-600">Company List</h1>
    <div>
      @if (getSetting('is_group_company'))
        <a href="{{ route('mock.company.create', ['level' => 2]) }}"
          class="text-white bg-green-500 hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-green-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">+
          Add Wing</a>
      @endif

      @if (getSetting('has_branch'))
        <a href="{{ route('mock.company.create', ['level' => 3]) }}"
          class="text-white bg-green-500 hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-green-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">+
          Add Branch</a>
      @endif
    </div>

  </div>
  <div class="relative overflow-x-auto my-4">
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
      <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr>
          <th scope="col" class="px-6 py-3">
            Company Type
          </th>
          <th scope="col" class="px-6 py-3">
            Name
          </th>
          <th scope="col" class="px-6 py-3">
            Address
          </th>
          <th scope="col" class="px-6 py-3">
            Parent Company
          </th>
          <th scope="col" class="px-6 py-3 text-center">
            Action
          </th>
        </tr>
      </thead>
      <tbody>
        @if ($companies->count() > 0)
          @foreach ($companies as $i => $company)
            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
              <td class="px-6 py-4">
                {{ $company->getType($company->level) }}
              </td>
              <td class="px-6 py-4">
                {{ $company->name }}
              </td>
              <td class="px-6 py-4">
                {{ $company->address }}
              </td>
              <td class="px-6 py-4">
                {{ $company->parent?->name }}
              </td>
              <td class="px-6 py-4 text-center">
                <a href="{{ route('mock.company.edit', $company->id) }}"
                  class="text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg px-3 py-2 text-xs text-center mr-2 mb-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800">Edit</a>

                <form action="{{ route('mock.company.destroy', $company->id) }}" method="POST" class="inline">
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
  {{ $companies->links() }}
@endsection
