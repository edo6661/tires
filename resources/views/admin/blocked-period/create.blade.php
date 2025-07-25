<x-layouts.app>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
         x-data="blockedPeriodForm({
            menus: {{ Js::from($menus) }},
            old_input: {{ Js::from(session()->getOldInput()) }},
            check_conflict_url: '{{ route('admin.blocked-period.check-conflict') }}',
            csrf_token: '{{ csrf_token() }}'
         })">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div class="transform transition-all duration-500 hover:translate-x-2">
                <h1 class="text-title-lg font-bold text-main-text">Create New Blocked Period</h1>
                <p class="mt-1 text-body-md text-main-text/80">Set a time period during which a specific menu or all menus are unavailable for reservation.</p>
            </div>
            <a href="{{ route('admin.blocked-period.index') }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-disabled rounded-lg shadow-sm text-button-md font-medium text-main-text hover:bg-sub hover:text-brand hover:border-brand hover:shadow-md hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand transition-all duration-300 transform">
                <i class="fas fa-arrow-left mr-2 transition-transform duration-300 group-hover:-translate-x-1"></i>
                Back to List
            </a>
        </div>

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded-md shadow-sm transform transition-all duration-500 hover:shadow-lg animate-pulse">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-times-circle text-red-400 animate-bounce"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-body-md text-red-700">
                            {{ session('error') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('admin.blocked-period.store') }}" method="POST" class="bg-white p-6 md:p-8 rounded-lg shadow-md space-y-6 transform transition-all duration-500 hover:shadow-xl">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2 flex items-center group">
                    <input id="all_menus" name="all_menus" type="checkbox" value="1" x-model="all_menus"
                           @if(old('all_menus')) checked @endif
                           class="h-4 w-4 rounded border-disabled text-brand focus:ring-brand transition-all duration-300 ">
                    <label for="all_menus" class="ml-3 block text-body-md font-medium text-main-text group-hover:text-brand transition-colors duration-300 cursor-pointer">
                        Block All Menus?
                    </label>
                </div>
                <div class="md:col-span-2" x-cloak x-show="!all_menus" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform -translate-y-4"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 transform translate-y-0"
                     x-transition:leave-end="opacity-0 transform -translate-y-4">
                    <label for="menu_id" class="block text-body-md font-medium text-main-text mb-1">Select Specific Menu</label>
                    <select id="menu_id" name="menu_id" x-model="menu_id" :disabled="all_menus" @change="checkConflict"
                            class="block w-full rounded-md border-disabled shadow-sm focus:border-brand focus:ring-brand text-body-md disabled:bg-disabled disabled:cursor-not-allowed transition-all duration-300 hover:border-brand/50 hover:shadow-md">
                        <option value="">-- Select one menu --</option>
                        @foreach($menus as $menu)
                            <option value="{{ $menu->id }}" {{ old('menu_id') == $menu->id ? 'selected' : '' }}>
                                {{ $menu->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('menu_id')
                        <p class="mt-2 text-body-md text-red-600 animate-pulse">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <hr class="border-disabled/50">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="transform transition-all duration-300  hover:shadow-md rounded-lg p-2 -m-2">
                    <label for="start_datetime" class="block text-body-md font-medium text-main-text mb-1">Start Time</label>
                    <input type="datetime-local" id="start_datetime" name="start_datetime" x-model="start_datetime" @input.debounce.750ms="checkConflict"
                           value="{{ old('start_datetime') }}"
                           class="block w-full rounded-md border-disabled shadow-sm focus:border-brand focus:ring-brand text-body-md transition-all duration-300 hover:border-brand/50 hover:shadow-md">
                    @error('start_datetime')
                        <div class="mt-2 text-body-md text-red-600 whitespace-pre-line animate-pulse">{{ $message }}</div>
                    @enderror
                </div>
                <div class="transform transition-all duration-300  hover:shadow-md rounded-lg p-2 -m-2">
                    <label for="end_datetime" class="block text-body-md font-medium text-main-text mb-1">End Time</label>
                    <input type="datetime-local" id="end_datetime" name="end_datetime" x-model="end_datetime" @input.debounce.750ms="checkConflict"
                           value="{{ old('end_datetime') }}"
                           class="block w-full rounded-md border-disabled shadow-sm focus:border-brand focus:ring-brand text-body-md transition-all duration-300 hover:border-brand/50 hover:shadow-md">
                    @error('end_datetime')
                        <p class="mt-2 text-body-md text-red-600 animate-pulse">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div x-cloak x-show="conflict.has_conflict" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                 class="md:col-span-2 p-4 bg-red-50 border-l-4 border-red-400 rounded-md shadow-md animate-pulse">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-red-400 mt-0.5 animate-bounce"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-body-md text-red-800 font-semibold">Schedule Conflict Detected!</p>
                        <p class="text-body-md text-red-700 mt-1">The entered period overlaps with the following schedule(s):</p>
                        <ul class="mt-2 list-disc list-inside text-body-md text-red-700 space-y-1">
                            <template x-for="detail in conflict.details" :key="detail.id">
                                <li class="transform transition-all duration-300 hover:translate-x-2">
                                    <strong x-text="detail.menu_name"></strong>:
                                    <span x-text="`${detail.start_datetime} - ${detail.end_datetime}`"></span>
                                    <span x-text="`(${detail.reason})`" class="italic text-main-text/60"></span>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>
            </div>
            <hr class="border-disabled/50">

            <div class="transform transition-all duration-300  hover:shadow-md rounded-lg p-2 -m-2">
                <label for="reason" class="block text-body-md font-medium text-main-text mb-1">Reason</label>
                <textarea id="reason" name="reason" rows="4"
                          class="block w-full rounded-md border-disabled shadow-sm focus:border-brand focus:ring-brand text-body-md transition-all duration-300 hover:border-brand/50 hover:shadow-md resize-none"
                          placeholder="e.g., Regular maintenance, holiday, private event, etc.">{{ old('reason') }}</textarea>
                @error('reason')
                    <p class="mt-2 text-body-md text-red-600 animate-pulse">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit"
                        :disabled="isLoading || (conflict.has_conflict && !isSubmittingOnPurpose)"
                        class="inline-flex items-center justify-center px-6 py-2 border border-transparent text-button-md font-medium rounded-lg shadow-sm text-white bg-brand hover:bg-link-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand disabled:bg-disabled disabled:cursor-not-allowed transition-all duration-300 transform  hover:shadow-lg hover:-translate-y-1"
                        :class="{ 'animate-pulse': isLoading }">
                    <i class="fas fa-spinner fa-spin mr-2 transition-all duration-300" x-cloak x-show="isLoading" x-cloak></i>
                    <span x-text="isLoading ? 'Checking...' : 'Save Blocked Period'" class="transition-all duration-300"></span>
                </button>
            </div>
        </form>
    </div>

    <script>
        function blockedPeriodForm(config) {
            return {
                all_menus: config.old_input.all_menus === '1' || false,
                menu_id: config.old_input.menu_id || '',
                start_datetime: config.old_input.start_datetime || '',
                end_datetime: config.old_input.end_datetime || '',
                isLoading: false,
                conflict: {
                    has_conflict: false,
                    details: []
                },
                isSubmittingOnPurpose: false, 
                init() {
                    this.$watch('all_menus', () => {
                        if (this.all_menus) {
                            this.menu_id = '';
                        }
                        this.checkConflict();
                    });
                    if (this.start_datetime && this.end_datetime && (this.menu_id || this.all_menus)) {
                        this.checkConflict();
                    }
                },
                checkConflict() {
                    if (!this.start_datetime || !this.end_datetime || (!this.all_menus && !this.menu_id)) {
                        this.conflict = { has_conflict: false, details: [] };
                        return;
                    }
                    if (new Date(this.start_datetime) >= new Date(this.end_datetime)) {
                        this.conflict = { has_conflict: false, details: [] }; 
                        return;
                    }
                    this.isLoading = true;
                    axios.post(config.check_conflict_url, {
                        menu_id: this.menu_id,
                        start_datetime: this.start_datetime,
                        end_datetime: this.end_datetime,
                        all_menus: this.all_menus
                    }, {
                        headers: {
                            'X-CSRF-TOKEN': config.csrf_token,
                            'Accept': 'application/json'
                        }
                    }).then(response => {
                        this.conflict.has_conflict = response.data.has_conflict;
                        this.conflict.details = response.data.conflict_details || [];
                    }).catch(error => {
                        console.error('Error checking conflict:', error);
                    }).finally(() => {
                        this.isLoading = false;
                    });
                }
            }
        }
    </script>
</x-layouts.app>