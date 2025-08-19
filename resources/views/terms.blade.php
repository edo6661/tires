<x-layouts.app>
    {{-- Slot untuk SEO Meta Title --}}
    <x-slot name="title">{{ __('terms.title') }}</x-slot>

    <div class="container">
        <main class="wrapper">
            <h1 class="text-title-lg font-bold font-jp text-brand mb-6 pb-4 border-b border-disabled/50">
                {{ __('terms.title') }}
            </h1>

            @if ($businessSettings && $businessSettings->terms_of_use)
                {{-- Styling untuk konten rich text dari Tailwind Typography --}}
                <div class="prose max-w-none">
                    {!! $businessSettings->terms_of_use !!}
                </div>
            @else
                <p class="text-main-text/70">
                    The Terms of Service are not available at the moment. Please check back later.
                </p>
            @endif
        </main>
    </div>
</x-layouts.app>
