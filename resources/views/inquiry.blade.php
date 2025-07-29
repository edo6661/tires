<x-layouts.app>
    <div class="flex flex-col md:flex-row gap-6 container">
        
        <div class="w-full md:w-1/3 lg:w-1/4 bg-sub p-6 rounded-lg shadow-sm border border-brand/10">
            <div class="space-y-6">
                <h2 class="text-title-md font-bold font-jp text-brand">
                    {{ $businessSettings->business_name ?? 'Takanawa Gateway City' }}
                </h2>
                
                <div>
                    <h3 class="text-heading-lg font-semibold text-main-text mb-2 font-jp">{{ __('inquiry.location') }}</h3>
                    <p class="text-body-md text-main-text/90">{{ $businessSettings->address ?? '2095-8 Miyadera, Iruma-shi, Saitama-ken' }}</p>
                </div>

                <div>
                    <h3 class="text-heading-lg font-semibold text-main-text mb-2 font-jp">{{ __('inquiry.opening_hours') }}</h3>
                    <ul class="text-body-md text-main-text/90 space-y-1">
                        @if($businessSettings && $businessSettings->business_hours)
                            {{-- Mengambil array hari langsung dari file lokalisasi --}}
                            @foreach(__('inquiry.days') as $dayKey => $dayName)
                                @php
                                    $hours = $businessSettings->business_hours[$dayKey] ?? null;
                                    $isClosed = ($hours && isset($hours['closed']) && $hours['closed']) || !$hours;
                                @endphp
                                <li class="flex justify-between {{ $isClosed ? 'text-main-text/40' : '' }}">
                                    <span>{{ $dayName }}</span>
                                    @if($isClosed)
                                        <span>{{ __('inquiry.closed') }}</span>
                                    @else
                                        <span>{{ $hours['open'] }} ~ {{ $hours['close'] }}</span>
                                    @endif
                                </li>
                            @endforeach
                        @else
                            {{-- Fallback jika data tidak ada --}}
                            <li class="flex justify-between"><span>{{ __('inquiry.days.monday') }}</span><span>10:00 ~ 18:00</span></li>
                            <li class="flex justify-between"><span>{{ __('inquiry.days.tuesday') }}</span><span>10:00 ~ 18:00</span></li>
                            <li class="flex justify-between text-main-text/40"><span>{{ __('inquiry.days.wednesday') }}</span><span>{{ __('inquiry.closed') }}</span></li>
                            <li class="flex justify-between text-main-text/40"><span>{{ __('inquiry.days.thursday') }}</span><span>{{ __('inquiry.closed') }}</span></li>
                            <li class="flex justify-between"><span>{{ __('inquiry.days.friday') }}</span><span>10:00 ~ 18:00</span></li>
                            <li class="flex justify-between"><span>{{ __('inquiry.days.saturday') }}</span><span>10:00 ~ 18:00</span></li>
                            <li class="flex justify-between"><span>{{ __('inquiry.days.sunday') }}</span><span>10:00 ~ 18:00</span></li>
                        @endif
                    </ul>
                </div>

                <div class="pt-4 border-t border-brand/20">
                    <a href="#" class="text-link hover:text-link-hover hover:underline transition-colors duration-300 text-body-md">{{ __('inquiry.about_us') }}</a>
                    <a href="#" class="text-link hover:text-link-hover hover:underline transition-colors duration-300 block mt-2 text-body-md">{{ __('inquiry.terms_of_use') }}</a>
                </div>
            </div>
        </div>

        <div class="flex-1 bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h2 class="text-title-lg font-bold font-jp text-brand mb-6">{{ __('inquiry.title') }}</h2>

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-400 text-green-800 rounded-r-lg">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 text-red-800 rounded-r-lg">
                    {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 text-red-800 rounded-r-lg">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('inquiry.submit') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label for="name" class="block text-body-md font-medium text-main-text mb-1">{{ __('inquiry.name') }}</label>
                    <input type="text" id="name" name="name" 
                        value="{{ old('name', auth()->user()->full_name ?? '') }}" 
                        class="w-full px-4 py-2 text-body-lg border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand/50 focus:border-brand transition-all duration-300 @error('name') border-red-500 @enderror"
                        placeholder="{{ __('inquiry.placeholders.name') }}" required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-body-md font-medium text-main-text mb-1">{{ __('inquiry.email') }}</label>
                    <input type="email" id="email" name="email" 
                        value="{{ old('email', auth()->user()->email ?? '') }}"
                        class="w-full px-4 py-2 text-body-lg border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand/50 focus:border-brand transition-all duration-300 @error('email') border-red-500 @enderror"
                        placeholder="{{ __('inquiry.placeholders.email') }}" required>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-body-md font-medium text-main-text mb-1">{{ __('inquiry.phone') }}</label>
                    <input type="tel" id="phone" name="phone" 
                        value="{{ old('phone', auth()->user()->phone_number ?? '') }}"
                        class="w-full px-4 py-2 text-body-lg border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand/50 focus:border-brand transition-all duration-300 @error('phone') border-red-500 @enderror"
                        placeholder="{{ __('inquiry.placeholders.phone') }}">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="subject" class="block text-body-md font-medium text-main-text mb-1">{{ __('inquiry.subject') }}</label>
                    <input type="text" id="subject" name="subject" value="{{ old('subject') }}"
                        class="w-full px-4 py-2 text-body-lg border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand/50 focus:border-brand transition-all duration-300 @error('subject') border-red-500 @enderror"
                        required>
                    @error('subject')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="message" class="block text-body-md font-medium text-main-text mb-1">{{ __('inquiry.inquiry_content') }}</label>
                    <textarea id="message" name="message" rows="5"
                        class="w-full px-4 py-2 text-body-lg border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand/50 focus:border-brand transition-all duration-300 @error('message') border-red-500 @enderror"
                        placeholder="{{ __('inquiry.placeholders.message') }}" required>{{ old('message') }}</textarea>
                    @error('message')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" 
                        class="w-full bg-main-button hover:bg-btn-main-hover text-white text-button-lg font-bold py-3 px-4 rounded-md transition-all duration-300 transform hover:scale-[1.02] focus:outline-none focus:ring-4 focus:ring-main-button/50">
                    {{ __('inquiry.submit_button') }}
                </button>
            </form>
        </div>
    </div>
</x-layouts.app>