<x-layouts.app>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
         x-data="blockedPeriodForm({
            menus: {{ Js::from($menus) }},
            old_input: {{ Js::from(session()->getOldInput()) }},
            check_conflict_url: '{{ route('admin.blocked-period.check-conflict') }}',
            csrf_token: '{{ csrf_token() }}'
         })">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Create New Blocked Period</h1>
                <p class="mt-1 text-sm text-gray-600">Set a time period during which a specific menu or all menus are unavailable for reservation.</p>
            </div>
            <a href="{{ route('admin.blocked-period.index') }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to List
            </a>
        </div>
        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded-md shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-times-circle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">
                            {{ session('error') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif
        <form action="{{ route('admin.blocked-period.store') }}" method="POST" class="bg-white p-6 md:p-8 rounded-lg shadow-md space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2 flex items-center">
                    <input id="all_menus" name="all_menus" type="checkbox" value="1" x-model="all_menus"
                           @if(old('all_menus')) checked @endif
                           class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <label for="all_menus" class="ml-3 block text-sm font-medium text-gray-900">
                        Block All Menus?
                    </label>
                </div>
                <div class="md:col-span-2" x-show="!all_menus" x-transition>
                    <label for="menu_id" class="block text-sm font-medium text-gray-700 mb-1">Select Specific Menu</label>
                    <select id="menu_id" name="menu_id" x-model="menu_id" :disabled="all_menus" @change="checkConflict"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm disabled:bg-gray-100 disabled:cursor-not-allowed">
                        <option value="">-- Select one menu --</option>
                        @foreach($menus as $menu)
                            <option value="{{ $menu->id }}" {{ old('menu_id') == $menu->id ? 'selected' : '' }}>
                                {{ $menu->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('menu_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <hr>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="start_datetime" class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
                    <input type="datetime-local" id="start_datetime" name="start_datetime" x-model="start_datetime" @input.debounce.750ms="checkConflict"
                           value="{{ old('start_datetime') }}"
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('start_datetime')
                        <div class="mt-2 text-sm text-red-600 whitespace-pre-line">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="end_datetime" class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
                    <input type="datetime-local" id="end_datetime" name="end_datetime" x-model="end_datetime" @input.debounce.750ms="checkConflict"
                           value="{{ old('end_datetime') }}"
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('end_datetime')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div x-show="conflict.has_conflict" x-transition class="md:col-span-2 p-4 bg-red-50 border-l-4 border-red-400 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-red-400 mt-0.5"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-800 font-semibold">Schedule Conflict Detected!</p>
                        <p class="text-sm text-red-700 mt-1">The entered period overlaps with the following schedule(s):</p>
                        <ul class="mt-2 list-disc list-inside text-sm text-red-700 space-y-1">
                            <template x-for="detail in conflict.details" :key="detail.id">
                                <li>
                                    <strong x-text="detail.menu_name"></strong>:
                                    <span x-text="`${detail.start_datetime} - ${detail.end_datetime}`"></span>
                                    <span x-text="`(${detail.reason})`" class="italic text-gray-600"></span>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>
            </div>
            <hr>
            <div>
                <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                <textarea id="reason" name="reason" rows="4"
                          class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                          placeholder="e.g., Regular maintenance, holiday, private event, etc.">{{ old('reason') }}</textarea>
                @error('reason')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex justify-end pt-4">
                <button type="submit"
                        :disabled="isLoading || (conflict.has_conflict && !isSubmittingOnPurpose)"
                        class="inline-flex items-center justify-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:bg-gray-400 disabled:cursor-not-allowed transition">
                    <i class="fas fa-spinner fa-spin mr-2" x-show="isLoading" x-cloak></i>
                    <span x-text="isLoading ? 'Checking...' : 'Save Blocked Period'"></span>
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