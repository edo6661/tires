<x-layouts.app>
    <div class="flex flex-col md:flex-row gap-4">
        <div class="w-full md:w-1/3 lg:w-1/4 bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="space-y-6">
                <h2 class="text-xl font-semibold text-gray-800">{{ $businessSettings->business_name ?? 'Takanawa Gateway City' }}</h2>
                <div>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Location</h3>
                    <p class="text-gray-600">{{ $businessSettings->address ?? '2095-8 Miyadera, Iruma-shi, Saitama-ken' }}</p>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Opening Hours</h3>
                    <ul class="text-gray-600 space-y-1">
                        @if($businessSettings && $businessSettings->business_hours)
                            @php
                                // Gunakan nama hari yang lengkap
                                $days = [
                                    'monday'    => 'Monday',
                                    'tuesday'   => 'Tuesday', 
                                    'wednesday' => 'Wednesday',
                                    'thursday'  => 'Thursday',
                                    'friday'    => 'Friday',
                                    'saturday'  => 'Saturday',
                                    'sunday'    => 'Sunday'
                                ];
                            @endphp
                            @foreach($days as $dayKey => $dayName)
                                @php
                                    $hours = $businessSettings->business_hours[$dayKey] ?? null;
                                    $isClosed = ($hours && isset($hours['closed']) && $hours['closed']) || !$hours;
                                @endphp
                                <li class="flex justify-between {{ $isClosed ? 'text-gray-400' : '' }}">
                                    <span>{{ $dayName }}</span>
                                    @if($isClosed)
                                        <span>Closed</span>
                                    @else
                                        <span>{{ $hours['open'] }} ~ {{ $hours['close'] }}</span>
                                    @endif
                                </li>
                            @endforeach
                        @else
                            <li class="flex justify-between"><span>Monday</span><span>10:00 ~ 18:00</span></li>
                            <li class="flex justify-between"><span>Tuesday</span><span>10:00 ~ 18:00</span></li>
                            <li class="flex justify-between text-gray-400"><span>Wednesday</span><span>Closed</span></li>
                            <li class="flex justify-between text-gray-400"><span>Thursday</span><span>Closed</span></li>
                            <li class="flex justify-between"><span>Friday</span><span>10:00 ~ 18:00</span></li>
                            <li class="flex justify-between"><span>Saturday</span><span>10:00 ~ 18:00</span></li>
                            <li class="flex justify-between"><span>Sunday</span><span>10:00 ~ 18:00</span></li>
                        @endif
                    </ul>
                </div>
                <div class="pt-4 border-t border-gray-200">
                    <a href="#" class="text-gray-600 hover:text-green-600 transition">About Us</a>
                    <a href="#" class="text-gray-600 hover:text-green-600 transition block mt-2">Terms of Use</a>
                </div>
            </div>
        </div>
        <div class="flex-1 bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Inquiry</h2>
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
                    {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('inquiry.submit') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('name') border-red-500 @enderror" 
                           placeholder="Tokyo Taro" required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('email') border-red-500 @enderror" 
                           placeholder="email address" required>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('phone') border-red-500 @enderror" 
                           placeholder="00-0000-0000">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject *</label>
                    <input type="text" id="subject" name="subject" value="{{ old('subject') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('subject') border-red-500 @enderror" 
                           required>
                    @error('subject')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Inquiry Content *</label>
                    <textarea id="message" name="message" rows="5" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('message') border-red-500 @enderror" 
                              placeholder="Please enter the content of your inquiry" required>{{ old('message') }}</textarea>
                    @error('message')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="w-full bg-primary hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                    Submit Inquiry
                </button>
            </form>
        </div>
    </div>
</x-layouts.app>