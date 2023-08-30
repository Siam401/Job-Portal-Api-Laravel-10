@extends('mock.layout')

@section('content')
  <div class="bg-white rounded-lg shadow-lg px-8 py-6">
    <form action="{{ route('mock.settings.social-auths') }}" method="POST" class="mb-10">
      @csrf

      <h4 class="text-2xl font-bold dark:text-white mb-5 pb-3 text-center">Social Authentications</h4>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mx-auto justify-center">
        <div class="border rounded px-6 py-4">
          <div class="text-xl text-center dark:text-white mb-4">
            Google Auth
            <span class="pt-2 ml-1"></span>
          </div>
          <x-text-input name="social_google_client_id" value="{{ getSetting('social_google_client_id') }}"
            label="Client ID" />
          <x-text-input name="social_google_client_secret" value="{{ getSetting('social_google_client_secret') }}"
            label="Client Secret" />
        </div>

        <div class="border rounded px-6 py-4">
          <div class="text-xl text-center dark:text-white mb-4">
            Facebook Auth
            <span class="pt-2 ml-1"></span>
          </div>
          <x-text-input name="social_facebook_client_id" value="{{ getSetting('social_facebook_client_id') }}"
            label="Client ID" />
          <x-text-input name="social_facebook_client_secret" value="{{ getSetting('social_facebook_client_secret') }}"
            label="Client Secret" />
        </div>

        <div class="border rounded px-6 py-4">
          <div class="text-xl text-center dark:text-white mb-4">
            LinkedIn Auth
            <span class="pt-2 ml-1"></span>
          </div>
          <x-text-input name="social_linkedin_client_id" value="{{ getSetting('social_linkedin_client_id') }}"
            label="Client ID" />
          <x-text-input name="social_linkedin_client_secret" value="{{ getSetting('social_linkedin_client_secret') }}"
            label="Client Secret" />
        </div>

      </div>

      <div class="flex justify-center mt-4">
        <button type="submit"
          class="px-8 py-4 text-xl text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 rounded-lg text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
          Save
        </button>
      </div>
    </form>
  @endsection
