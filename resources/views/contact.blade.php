<x-layouts.app>
    <div class="container">
        <div class="grid md:grid-cols-[250px_1fr] gap-10">
            {{-- Sidebar (disalin dari halaman lain untuk konsistensi) --}}
            <aside class="sm:sticky top-8 self-start text-main-text md:border-r md:border-disabled/50 md:pr-6 space-y-8">
                <div>
                    <p class="text-heading-md font-semibold mb-2 text-brand">{{ __('home.location') }}</p>
                    <p class="text-body-md">{{ $businessSettings->address ?? 'Default Address' }}</p>
                </div>
                <div>
                    <p class="text-heading-md font-semibold mb-2 text-brand">{{ __('home.business_hours') }}</p>
                    <ul class="space-y-1 leading-tight text-body-md">
                        @if ($businessSettings && $businessSettings->business_hours)
                            @foreach (__('home.days') as $dayKey => $dayTranslation)
                                @php
                                    $hours = $businessSettings->business_hours[$dayKey] ?? null;
                                @endphp
                                <li>
                                    <span class="font-medium w-8 inline-block">{{ $dayTranslation }}</span>
                                    @if ($hours && isset($hours['closed']) && $hours['closed'])
                                        {{ __('home.closed') }}
                                    @elseif($hours && isset($hours['open']) && isset($hours['close']))
                                        {{ $hours['open'] }} - {{ $hours['close'] }}
                                    @else
                                        {{ __('home.closed') }}
                                    @endif
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
                @if ($businessSettings && $businessSettings->phone_number)
                    <div>
                        <p class="text-heading-md font-semibold mb-2 text-brand">{{ __('home.phone') }}</p>
                        <p class="text-body-md">{{ $businessSettings->phone_number }}</p>
                    </div>
                @endif
                <nav class="pt-6 border-t border-disabled/50 space-y-2">
                    <a href="{{ route('about') }}"
                        class="block text-link hover:text-link-hover text-body-md transition-colors duration-200">{{ __('home.about_us') }}</a>
                    <a href="{{ route('contact') }}"
                        class="block text-link hover:text-link-hover text-body-md transition-colors duration-200">{{ __('home.contact_us') }}</a>
                    <a href="#"
                        class="block text-link hover:text-link-hover text-body-md transition-colors duration-200">{{ __('home.terms_of_service') }}</a>
                </nav>
            </aside>

            {{-- Main Content --}}
            <main class="wrapper">
                <h1 class="text-title-lg font-bold font-jp text-brand mb-2">{{ __('contact.title') }}</h1>
                <p class="text-body-md text-main-text/80 mb-6">{{ __('contact.description') }}</p>

                @if (session('success'))
                    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-400 text-green-800 rounded-r-lg">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 text-red-800 rounded-r-lg">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('contact.submit') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label for="name"
                            class="block text-body-md font-medium text-main-text mb-1">{{ __('contact.name') }}</label>
                        <input type="text" id="name" name="name"
                            value="{{ old('name', auth()->user()->full_name ?? '') }}"
                            class="w-full px-4 py-2 text-body-lg border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand/50 focus:border-brand transition-all duration-300 @error('name') border-red-500 @enderror"
                            placeholder="{{ __('contact.placeholders.name') }}" required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email"
                            class="block text-body-md font-medium text-main-text mb-1">{{ __('contact.email') }}</label>
                        <input type="email" id="email" name="email"
                            value="{{ old('email', auth()->user()->email ?? '') }}"
                            class="w-full px-4 py-2 text-body-lg border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand/50 focus:border-brand transition-all duration-300 @error('email') border-red-500 @enderror"
                            placeholder="{{ __('contact.placeholders.email') }}" required>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="subject"
                            class="block text-body-md font-medium text-main-text mb-1">{{ __('contact.subject') }}</label>
                        <input type="text" id="subject" name="subject" value="{{ old('subject') }}"
                            class="w-full px-4 py-2 text-body-lg border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand/50 focus:border-brand transition-all duration-300 @error('subject') border-red-500 @enderror"
                            placeholder="{{ __('contact.placeholders.subject') }}" required>
                        @error('subject')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="message"
                            class="block text-body-md font-medium text-main-text mb-1">{{ __('contact.message') }}</label>
                        <textarea id="message" name="message" rows="5"
                            class="w-full px-4 py-2 text-body-lg border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand/50 focus:border-brand transition-all duration-300 @error('message') border-red-500 @enderror"
                            placeholder="{{ __('contact.placeholders.message') }}" required>{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                        class="w-full bg-main-button hover:bg-btn-main-hover text-footer-text text-button-lg font-bold py-3 px-4 rounded-md transition-all duration-300 transform hover:scale-[1.02] focus:outline-none focus:ring-4 focus:ring-main-button/50">
                        {{ __('contact.submit_button') }}
                    </button>
                </form>
            </main>
        </div>
    </div>
</x-layouts.app>
