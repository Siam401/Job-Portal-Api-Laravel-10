@extends('mock.layout')

@section('content')
  <h1 class="text-3xl font-extrabold tracking-tight text-gray-600 text-center">
    @if (isset($company) && $company->id > 0)
      Edit {{ $type }}
    @else
      Add {{ $type }}
    @endif
  </h1>

  <form
    action="{{ isset($company) && $company->id > 0 ? route('mock.company.update', $company) : route('mock.company.store') }}"
    enctype="multipart/form-data" method="POST" class="mb-10">
    @csrf
    @if (isset($company) && $company->id > 0)
      @method('PUT')
    @endif
    <input type="hidden" name="level" value="{{ $level }}" hidden>
    <div class="grid gap-6 gap-y-0 mb-6 md:grid-cols-2 xl:grid-cols-3 mt-4">
      <x-text-input name="name" required="1" label="Company Name" value="{{ $company?->name }}" />
      @if ($parents && (getSetting('is_group_company')))
        <x-select name="parent_id" value="{{ $company?->parent_id }}" required="1" label="Parent Company"
          default="Please Select" options="{!! $parents !!}" />
      @endif
      <x-text-input name="address" required="1" label="Address" value="{{ $company?->address }}" />
      <div class="flex justify-between">
        <x-file-input name="logo" label="Logo" divClass="grow" value="{{ $company?->address }}" />

        @if ($company?->logo)
          <img style="max-width: 120px" class="h-auto max-w-xl rounded-lg ml-4" src="{{ getFile($company->logo) }}"
            alt="Company Logo">
        @endif
      </div>

      <x-text-input name="city" required="1" label="City" value="{{ $company?->city }}" />

      <div class="mb-4">
        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Divisions <span
            class="text-red-500">*</span></label>
        <select name="division_id" required data-route="{{ url('api/location/get-districts') }}" data-param="division_id"
          data-target="select[name='district_id']"
          class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 ajax-call">
          @if ($divisions)
            @php
              $divId = $company?->divisionId();
              echo empty($divId) ? '<option value="" selected>Please Select</option>' : '';
            @endphp

            @foreach ($divisions as $id => $text)
              <option value="{{ $id }}" {{ $id == $divId ? 'selected' : '' }}>{{ $text }}</option>
            @endforeach
          @endif
        </select>
      </div>

      <div class="mb-4">
        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Districts <span
            class="text-red-500">*</span></label>
        <select name="district_id" required data-route="{{ url('api/location/get-areas') }}" data-param="district_id"
          data-target="select[name='area_id']"
          class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 ajax-call">
          @if ($districts)
            @php
              $distId = $company?->district_id;
            @endphp
            @foreach ($districts as $id => $text)
              <option value="{{ $id }}" {{ $id == $distId ? 'selected' : '' }}>{{ $text }}</option>
            @endforeach
          @endif
        </select>
      </div>

      <x-select name="area_id" label="Areas" value="{{ $company?->area_id }}" options="{!! isset($areas) ? $areas : false !!}" />

      <x-text-input name="zipcode" label="Zip/Post Code" value="{{ $company?->zipcode }}" />
      <x-text-input name="country"
        class="disabled:bg-slate-50 disabled:text-slate-500 disabled:border-slate-200 disabled:shadow-none" disabled
        label="Country" value="Bangladesh" />
      <x-text-input name="phone" label="Telephone" value="{{ $company?->phone }}" />
      <x-text-input type="email" name="email" required="1" label="System Email" value="{{ $company?->email }}" />
      <x-text-input name="from_name" required="1" label="From Name(Email)" value="{{ $company?->from_name }}" />
      <x-text-input name="reg_number" label="Company Registration Number" value="{{ $company?->reg_number }}" />
      <div>
        <div class="flex">
          <x-radio name="tax_type" divClass="mr-5 !mb-2" label="VAT Number" value="vat"
            checked="{!! $company?->tax_type == 'vat' ? true : false !!}" />
          <x-radio name="tax_type" divClass="!mb-2" label="GST Number" value="gst" checked="{!! $company?->tax_type == 'gst' ? true : false !!}" />
        </div>
        <x-text-input name="tax_number" value="{{ $company?->tax_number }}" />
      </div>
      <x-select name="timezone" value="{{ $company?->timezone }}" label="Timezone" default="Please Select"
        options="{!! isset($timezones) ? $timezones : false !!}" />
      <div class="flex justify-between">
        <x-text-input divClass="w-full mr-2" type="time" name="office_start_time" label="Office Start Time"
          value="{{ $company?->office_start_time }}" />
        <x-text-input divClass="w-full ml-2" type="time" name="office_end_time" label="Office End Time"
          value="{{ $company?->office_end_time }}" />
      </div>

      <x-text-input type="url" name="website" label="Company Website" value="{{ $company?->website }}" />
      <x-select class="select2" name="weekends[]" label="Office Weekends" value="{!! isset($company->weekends) ? $company->weekends : false !!}"
        options="{!! isset($weekdays) ? $weekdays : false !!}" multiple />

    </div>

    <div class="flex justify-center">
      <button type="submit"
        class="px-6 py-3.5 text-base font-medium text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
        @if (isset($company) && $company->id > 0)
          Update {{ $type }}
        @else
          Create {{ $type }}
        @endif
      </button>

    </div>
  </form>
@endsection


@push('style')
  <link href="{{ url('build/tagify/dist/tagify.css') }}" rel="stylesheet" type="text/css" />
  <script src="{{ url('build/tagify/dist/tagify.polyfills.min.js') }}"></script>
@endpush

@push('script')
  <script src="{{ url('build/tinymce/tinymce.min.js') }}"></script>
  <script src="{{ url('build/tagify/dist/tagify.min.js') }}"></script>
  <script src="{{ url('build/tagify/dist/tagify.polyfills.min.js') }}"></script>
@endpush
