<fieldset class="border border-solid border-gray-300 p-6 mt-6">
  <legend class="text-medium font-semibold px-2">Text Items</legend>

  <div class="relative overflow-x-auto">
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400 action-table" id="action-{{ $section }}">
      <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr>
          <th scope="col" class="px-6 py-3">
            Serial
          </th>
          <th scope="col" class="px-6 py-3">
            Title
          </th>
          <th scope="col" class="px-6 py-3">
            Subtitle
          </th>
          <th scope="col" class="px-6 py-3">
            Image
          </th>

          <th scope="col" class="px-6 py-3 text-center">
            Remove
          </th>

        </tr>
      </thead>
      <tbody>
        @foreach ($items as $item)
          <tr class="border-b border-gray-200 dark:border-gray-700" data-id="{{ $item->id }}">
            <td class="px-6 py-4">
                <x-text-input divClass="!mb-0" type="number" name="section_items[{!! $item->id !!}][serial]"
                  value="{{ $item['serial'] ?? 1 }}" />
              </td>
            <td class="px-6 py-4">
              <x-text-input divClass="!mb-0" name="section_items[{{ $item->id }}][title]" value="{!! $item->items['title'] ?? '' !!}" />
            </td>
            <td class="px-6 py-4">
              <x-textarea divClass="!mb-0" name="section_items[{{ $item->id }}][sub_title]" value="{{ $item->items['sub_title'] ?? '' }}" />
            </td>
            <td class="px-6 py-4">
              <div class="flex justify-between preview-image-container">
                <x-file-input name="section_items[{{ $item->id }}][image]" divClass="grow" class="preview-image" />

                @if ($item->items['image_url'])
                  <img style="max-width: 200px" class="h-auto max-w-xl rounded-lg ml-4"
                    src="{{ getFile($item->items['image_url']) }}" alt="Item Image">
                @endif
              </div>
            </td>

            <td class="px-6 py-4 text-center">
              <input type="hidden" name="section_items[{{ $item->id }}][id]" value="{{ $item->id }}">
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
            <x-text-input divClass="!mb-0" type="number" disabled name="section_items[___][serial]" value="1" />
          </td>
          <td class="px-6 py-4">
            <x-text-input divClass="!mb-0" disabled name="section_items[___][title]" />
          </td>
          <td class="px-6 py-4">
            <x-textarea divClass="!mb-0" name="section_items[___][sub_title]" disabled />
          </td>
          <td class="px-6 py-4">
            <div class="flex justify-between preview-image-container">
              <x-file-input name="section_items[___][image]" divClass="grow" class="preview-image" />

              <img style="max-width: 200px" class="h-auto max-w-xl rounded-lg ml-4" src="" alt="Item Image">
            </div>
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
</fieldset>
