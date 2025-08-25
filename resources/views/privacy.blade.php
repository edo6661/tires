<x-layouts.app>
    {{-- Slot untuk SEO Meta Title --}}
    <x-slot name="title">{{ __('privacy.title') }}</x-slot>

    <div class="flex flex-col md:flex-row gap-6 container">
        <x-layouts.sidebar :business-settings="$businessSettings" />

        <main class="flex-1 bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h1 class="text-title-lg font-bold font-jp text-brand mb-6 pb-4 border-b border-disabled/50">
                {{ __('privacy.title') }}
            </h1>

            @if ($businessSettings && $businessSettings->privacy_policy)
                {{-- Styling untuk konten rich text dari Tailwind Typography --}}
                <div class="prose max-w-none">
                    {!! $businessSettings->privacy_policy !!}
                </div>
            @else
                <p class="text-main-text/70">
                    The Privacy Policy is not available at the moment. Please check back later.
                </p>
            @endif
        </main>
    </div>
</x-layouts.app>

