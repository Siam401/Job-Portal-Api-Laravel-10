@extends('mock.layout')

@section('content')
  <h1 class="text-3xl font-extrabold tracking-tight text-gray-600 text-center">Edit Job</h1>

  <form action="{{ route('mock.job.update', $job) }}" enctype="multipart/form-data" method="POST" class="mb-10">
    @csrf
    @method('PUT')
    <div class="grid gap-6 mb-6 md:grid-cols-2 mt-4">
      <div>
        <x-text-input name="title" required="1" label="Job Title" value="{{ $job->title }}" />
        <x-select name="wing_id" required="1" value="{{ $job->wing_id }}" label="Wings"
          options="{!! isset($wings) ? $wings : false !!}" />
        <x-select name="branch_id" label="Branch" value="{{ $job->branch_id }}" options="{!! isset($branches) ? $branches : false !!}" />
        <x-select name="job_function_id" label="Job Category" value="{{ $job->job_function_id }}"
          options="{!! isset($categories) ? $categories : false !!}" />
        <div class="mb-4">
          <div class="flex justify-between">
            <label for="vacancy" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Vacancy <span
                class="text-red-500">*</span></label>
            <div class="flex items-center mb-2">
              <input type="checkbox" value="1" name="vacancy_na" {!! is_null($job->vacancy) ? 'checked' : '' !!}
                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
              <label for="vacancy_na" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">N/A</label>
            </div>
          </div>
          <x-text-input type="number" placeholder="number of vacancy" name="vacancy" value="{{ $job->vacancy }}" />
        </div>
        <x-select name="workplace" label="Workplace" value="{{ $jobDetail->workplace }}"
          options="{!! isset($workplaces) ? $workplaces : false !!}" />
        <x-select name="job_type" label="Job Type" value="{{ $jobDetail->job_type }}" options="{!! isset($jobTypes) ? $jobTypes : false !!}" />
        <x-text-input name="salary" label="Salary" value="{{ $jobDetail->salary }}" />
        <x-text-input name="skills" label="Skills" class="tagify" value="{!! $jobDetail->skills !!}" />
      </div>
      <div>

        <x-select name="gender" label="Gender" value="{{ $jobDetail->gender }}" default="Any" defaultValue="any"
          options="{!! isset($genderList) ? $genderList : false !!}" />
        <x-select name="status" label="Status" value={{ $job->status }} options="{!! isset($statusList) ? $statusList : false !!}" />

        <fieldset class="border border-solid border-gray-300 p-4 rounded-lg mb-4">
          <legend class="text-sm px-2">Need to Show Options</legend>
          <div>
            <x-checkbox name="form_visibility[]" checked="{!! in_array('photo', $jobDetail->form_visibility) ? '1' : '0' !!}" value="photo" label="Profile Image" />
            <x-checkbox name="form_visibility[]" value="cover_letter" label="Cover Letter"
              checked="{!! in_array('cover_letter', $jobDetail->form_visibility) ? '1' : '0' !!}" />
          </div>
        </fieldset>
        <fieldset class="border border-solid border-gray-300 p-4 rounded-lg">
          <legend class="text-sm px-2">Questions to Ask</legend>
          <div>
            @if (isset($questions) && $questions->count() > 0)
              @foreach ($questions as $ques)
                @if (isTrue($ques->is_active))
                  <x-checkbox name="questions[]" value="{{ $ques->id }}" label="{{ $ques->question }}"
                    required="{{ intval($ques->is_required) }}" checked="{!! in_array($ques->id, $jobDetail->questions) ? '1' : '0' !!}" />
                @endif
              @endforeach
            @endif
          </div>
        </fieldset>

        <div class="grid grid-cols-2 gap-4 mt-7">
          <x-select name="age_min" value="{{ $jobDetail->age_min }}" label="Min Age" divClass="flex"
            labelClass="min-w-fit pr-2 pt-3" default="Not Applicable" options="{!! isset($ages) ? collect($ages) : false !!}" />
          <x-select name="age_max" value="{{ $jobDetail->age_max }}" label="Max Age" divClass="flex"
            labelClass="min-w-fit pr-2 pt-3" default="Not Applicable" options="{!! isset($ages) ? collect(array_reverse($ages, true)) : false !!}" />
        </div>

        <x-text-input name="start_date" label="Start Date" value="{{ $job->start_date }}" required type="date" />
        <x-text-input name="end_date" label="End Date" value="{{ $job->end_date }}" type="date" required />
      </div>
    </div>
    <div class="grid gap-6 mb-6 md:grid-cols-2">
      <div class="mb-4">
        <label for="job_description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Job
          Description <span class="text-red-500">*</span></label>
        <textarea name="description" class="rich-text">{!! $jobDetail->description !!}</textarea>
      </div>
      <div class="mb-4">
        <label for="responsibility" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Job
          Responsibility <span class="text-red-500">*</span></label>
        <textarea name="responsibility" class="rich-text">{!! $jobDetail->responsibility !!}</textarea>
      </div>
      <div class="mb-4">
        <label for="education" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
          Education</label>
        <textarea name="education" class="rich-text">{!! $jobDetail->education !!}</textarea>
      </div>
      <div class="mb-4">
        <label for="benefit" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
          Compensation & Benefits <span class="text-red-500">*</span></label>
        <textarea name="benefit" class="rich-text">{!! $jobDetail->benefit !!}</textarea>
      </div>
      <div class="mb-4">
        <label for="additional" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
          Additional</label>
        <textarea name="additional" class="rich-text">{!! $jobDetail->additional !!}</textarea>
      </div>
      <div class="mb-4">
        <label for="experience" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
          Experience Requirement</label>
        <ul
          class="items-center w-full text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg sm:flex dark:bg-gray-700 dark:border-gray-600 dark:text-white mb-4">
          <li class="w-full border-b border-gray-200 sm:border-b-0 sm:border-r dark:border-gray-600">
            <div class="flex items-center pl-3">
              <input id="horizontal-list-radio-license" type="radio"
                {{ $jobDetail->is_exp_required > 0 ? 'checked' : '' }} value="1" name="is_exp_required"
                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
              <label for="horizontal-list-radio-license"
                class="w-full py-3 ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Experience Required</label>
            </div>
          </li>
          <li class="w-full border-b border-gray-200 sm:border-b-0 sm:border-r dark:border-gray-600">
            <div class="flex items-center pl-3">
              <input id="horizontal-list-radio-id" type="radio" value="0" name="is_exp_required"
                {{ $jobDetail->is_exp_required == 0 ? 'checked' : '' }}
                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
              <label for="horizontal-list-radio-id"
                class="w-full py-3 ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">No Experience
                Required</label>
            </div>
          </li>
        </ul>
        <div id="experienceBlock" class="{{ $jobDetail->is_exp_required > 0 ? '' : 'hidden' }}">

          <div class="grid grid-cols-2 gap-4">
            <x-select name="min_exp" value="{{ $jobDetail->min_exp }}" label="Min Experience" default="Any"
              options="{!! collect($exps) !!}" divClass="flex" labelClass="min-w-fit pr-2 pt-3" />
            @php
              array_shift($exps);
            @endphp
            <x-select name="max_exp" value="{{ $jobDetail->max_exp }}" label="Max Experience" default="Any"
              options="{!! collect($exps) !!}" divClass="flex" labelClass="min-w-fit pr-2 pt-3" />
          </div>
          <textarea name="experience" class="rich-text">{{ $jobDetail->experience }}</textarea>
        </div>
      </div>
    </div>
    <div class="flex justify-center">
      <button type="submit"
        class="px-6 py-3.5 text-base font-medium text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 mr-2 mb-2">
        Update Job
      </button>

      <a href="{{ route('mock.job.index') }}"
        class="focus:outline-none text-white bg-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:ring-yellow-300 font-medium rounded-lg px-6 py-3.5 mr-2 mb-2 dark:focus:ring-yellow-900">Back
        to List</a>

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
