<x-layouts.app>
    <div class="container px-4 sm:px-6 lg:px-8 py-8"
         x-data="blockedPeriodForm({
             menus: {{ Js::from($menus) }},
             old_input: {{ Js::from(session()->getOldInput()) }},
             check_conflict_url: '{{ route('admin.blocked-period.check-conflict') }}',
             calendar_url: '{{ route('admin.blocked-period.calendar') }}',
             csrf_token: '{{ csrf_token() }}',
             conflict_alert_title: '{{ __('admin/blocked-period/create.conflict_alert.title') }}',
             conflict_alert_message: '{{ __('admin/blocked-period/create.conflict_alert.message') }}',
             button_text: {
                save: '{{ __('admin/blocked-period/create.save_button') }}',
                saving: '{{ __('admin/blocked-period/create.saving_button') }}'
             },
             duration_presets: {
                full_day: '{{ __('admin/blocked-period/create.duration_presets.full_day') }}',
                full_2_days: '{{ __('admin/blocked-period/create.duration_presets.full_2_days') }}',
                full_week: '{{ __('admin/blocked-period/create.duration_presets.full_week') }}',
                custom: '{{ __('admin/blocked-period/create.duration_presets.custom') }}'
             }
         })">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div class="transform transition-all duration-500 hover:translate-x-2">
                <h1 class="text-title-lg font-bold text-main-text">{{ __('admin/blocked-period/create.title') }}</h1>
                <p class="mt-1 text-body-md text-main-text/80">{{ __('admin/blocked-period/create.description') }}</p>
            </div>
            <a href="{{ route('admin.blocked-period.index') }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-disabled rounded-lg shadow-sm text-button-md font-medium text-main-text hover:bg-sub hover:text-brand hover:border-brand hover:shadow-md hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand transition-all duration-300 transform">
                <i class="fas fa-arrow-left mr-2 transition-transform duration-300 group-hover:-translate-x-1"></i>
                {{ __('admin/blocked-period/create.back_to_list_button') }}
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Calendar Section -->
            <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold text-main-text mb-4">{{ __('admin/blocked-period/create.calendar.title') }}</h3>
                <div id="calendar" class="min-h-[500px]"></div>
            </div>

            <!-- Form Section -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <form action="{{ route('admin.blocked-period.store') }}" method="POST" class="space-y-6">
                    @csrf
                    @if ($errors->any())
                        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4 rounded-md shadow-sm">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-times-circle text-red-400"></i>
                                </div>
                                <div class="ml-3">
                                    <div class="mt-2 text-sm text-red-700">
                                        <ul role="list" class="list-disc pl-5 space-y-1">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    
                    <!-- Menu Selection -->
                    <div class="space-y-4">
                        <div class="flex items-center group">
                            <input id="all_menus" name="all_menus" type="checkbox" value="1" x-model="all_menus"
                                   @if(old('all_menus')) checked @endif
                                   class="h-4 w-4 rounded border-disabled text-brand focus:ring-brand transition-all duration-300">
                            <label for="all_menus" class="ml-3 block text-body-md font-medium text-main-text group-hover:text-brand transition-colors duration-300 cursor-pointer">
                                {{ __('admin/blocked-period/create.form.all_menus_label') }}
                            </label>
                        </div>
                        
                        <div x-cloak x-show="!all_menus"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 transform -translate-y-4"
                             x-transition:enter-end="opacity-100 transform translate-y-0">
                            <label for="menu_id" class="block text-body-md font-medium text-main-text mb-1">{{ __('admin/blocked-period/create.form.select_menu_label') }}</label>
                            <select id="menu_id" name="menu_id" x-model="menu_id" :disabled="all_menus" @change="loadCalendar"
                                    class="block w-full rounded-md border-disabled shadow-sm focus:border-brand focus:ring-brand text-body-md disabled:bg-disabled disabled:cursor-not-allowed transition-all duration-300">
                                <option value="">{{ __('admin/blocked-period/create.form.select_menu_placeholder') }}</option>
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

                    <!-- Duration Presets -->
                    <div>
                        <label class="block text-body-md font-medium text-main-text mb-3">{{ __('admin/blocked-period/create.form.duration_preset_label') }}</label>
                        <div class="space-y-2">
                            <template x-for="(label, value) in duration_presets" :key="value">
                                <label class="flex items-center group cursor-pointer">
                                    <input type="radio" name="duration_preset" :value="value" x-model="duration_preset" @change="applyDurationPreset"
                                           class="h-4 w-4 text-brand focus:ring-brand border-disabled transition-all duration-300">
                                    <span class="ml-3 text-body-md text-main-text group-hover:text-brand transition-colors duration-300" x-text="label"></span>
                                </label>
                            </template>
                        </div>
                    </div>

                    <hr class="border-disabled/50">

                    <!-- Date & Time Selection -->
                    <div class="space-y-4">
                        <div>
                            <label for="start_date" class="block text-body-md font-medium text-main-text mb-1">{{ __('admin/blocked-period/create.form.start_date_label') }}</label>
                            <input type="date" id="start_date" name="start_date" x-model="start_date" @input="updateDatetimes"
                                   value="{{ old('start_date') }}"
                                   class="block w-full rounded-md border-disabled shadow-sm focus:border-brand focus:ring-brand text-body-md transition-all duration-300">
                            @error('start_date')
                                <p class="mt-2 text-body-md text-red-600 animate-pulse">{{ $message }}</p>
                            @enderror
                        </div>

                        <div x-show="duration_preset === 'custom'" x-transition>
                            <label for="end_date" class="block text-body-md font-medium text-main-text mb-1">{{ __('admin/blocked-period/create.form.end_date_label') }}</label>
                            <input type="date" id="end_date" name="end_date" x-model="end_date" @input="updateDatetimes"
                                   value="{{ old('end_date') }}"
                                   class="block w-full rounded-md border-disabled shadow-sm focus:border-brand focus:ring-brand text-body-md transition-all duration-300">
                            @error('end_date')
                                <p class="mt-2 text-body-md text-red-600 animate-pulse">{{ $message }}</p>
                            @enderror
                            @error('end_datetime')
                                <p class="mt-2 text-body-md text-red-600 animate-pulse">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="start_time" class="block text-body-md font-medium text-main-text mb-1">{{ __('admin/blocked-period/create.form.start_time_label') }}</label>
                                <input type="time" id="start_time" name="start_time" x-model="start_time" @input="updateDatetimes"
                                       value="{{ old('start_time', '00:00') }}"
                                       class="block w-full rounded-md border-disabled shadow-sm focus:border-brand focus:ring-brand text-body-md transition-all duration-300">
                                        @error('start_time')
                                            <p class="mt-2 text-body-md text-red-600 animate-pulse">{{ $message }}</p>
                                        @enderror
                            </div>
                            <div>
                                <label for="end_time" class="block text-body-md font-medium text-main-text mb-1">{{ __('admin/blocked-period/create.form.end_time_label') }}</label>
                                <input type="time" id="end_time" name="end_time" x-model="end_time" @input="updateDatetimes"
                                       value="{{ old('end_time', '23:59') }}"
                                       class="block w-full rounded-md border-disabled shadow-sm focus:border-brand focus:ring-brand text-body-md transition-all duration-300">
                                @error('end_time')
                                    <p class="mt-2 text-body-md text-red-600 animate-pulse">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Hidden datetime fields for form submission -->
                        <input type="hidden" name="start_datetime" x-model="start_datetime">
                        <input type="hidden" name="end_datetime" x-model="end_datetime">
                    </div>

                    <!-- Conflict Alert -->
                    <div x-cloak x-show="conflict.has_conflict"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         class="p-4 bg-red-50 border-l-4 border-red-400 rounded-md shadow-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-red-400 mt-0.5 animate-bounce"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-body-md text-red-800 font-semibold" x-text="conflict_alert_title"></p>
                                <p class="text-body-md text-red-700 mt-1" x-text="conflict_alert_message"></p>
                                <ul class="mt-2 list-disc list-inside text-body-md text-red-700 space-y-1">
                                    <template x-for="detail in conflict.details" :key="detail.id">
                                        <li>
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

                    <!-- Reason -->
                    <div>
                        <label for="reason" class="block text-body-md font-medium text-main-text mb-1">{{ __('admin/blocked-period/create.form.reason_label') }}</label>
                        <textarea id="reason" name="reason" rows="4"
                                  class="block w-full rounded-md border-disabled shadow-sm focus:border-brand focus:ring-brand text-body-md transition-all duration-300 resize-none"
                                  placeholder="{{ __('admin/blocked-period/create.form.reason_placeholder') }}">{{ old('reason') }}</textarea>
                        @error('reason')
                            <p class="mt-2 text-body-md text-red-600 animate-pulse">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4">
                        <button type="submit"
                                :disabled="isLoading || conflict.has_conflict"
                                class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent text-button-md font-medium rounded-lg shadow-sm text-white bg-brand hover:bg-link-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand disabled:bg-disabled disabled:cursor-not-allowed transition-all duration-300 transform hover:shadow-lg hover:-translate-y-1"
                                :class="{ 'animate-pulse': isLoading }">
                            <i class="fas fa-spinner fa-spin mr-2 transition-all duration-300" x-cloak x-show="isLoading"></i>
                            <span x-text="isLoading ? button_text.saving : button_text.save" class="transition-all duration-300"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Include FullCalendar CSS & JS -->
    <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.10/index.global.min.css' rel='stylesheet' />
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.10/index.global.min.js'></script>

    <script>
        function blockedPeriodForm(config) {
            return {
                // Form data
                all_menus: config.old_input.all_menus === '1' || false,
                menu_id: config.old_input.menu_id || '',
                duration_preset: 'full_day',
                start_date: config.old_input.start_date || new Date().toISOString().split('T')[0],
                end_date: config.old_input.end_date || '',
                start_time: config.old_input.start_time || '00:00',
                end_time: config.old_input.end_time || '23:59',
                start_datetime: '',
                end_datetime: '',
                
                // UI state
                isLoading: false,
                calendar: null,
                blockedEvents: [],
                
                // Conflict data
                conflict: {
                    has_conflict: false,
                    details: []
                },
                
                // Localized text
                conflict_alert_title: config.conflict_alert_title,
                conflict_alert_message: config.conflict_alert_message,
                button_text: config.button_text,
                duration_presets: config.duration_presets,

                init() {
                    this.$nextTick(() => {
                        this.initCalendar();
                        this.loadCalendar();
                    });
                    
                    this.$watch('all_menus', () => {
                        if (this.all_menus) {
                            this.menu_id = '';
                        }
                        this.loadCalendar();
                    });
                    
                    this.$watch('menu_id', () => {
                        this.loadCalendar();
                    });
                    
                    // Initialize datetime values
                    this.updateDatetimes();
                },

                initCalendar() {
                    const calendarEl = document.getElementById('calendar');
                    this.calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: 'dayGridMonth',
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'dayGridMonth,timeGridWeek'
                        },
                        selectable: true,
                        selectMirror: true,
                        dayMaxEvents: true,
                        weekends: true,
                        select: (selectInfo) => {
                            this.handleDateSelect(selectInfo);
                        },
                        eventClick: (clickInfo) => {
                            // Show event details
                            alert(`${clickInfo.event.title}\n${clickInfo.event.extendedProps.reason || ''}`);
                        },
                        events: (fetchInfo, successCallback, failureCallback) => {
                            this.loadCalendarEvents(fetchInfo, successCallback, failureCallback);
                        },
                        eventDidMount: (info) => {
                            // Add visual indicators for blocked periods
                            if (info.event.extendedProps.all_menus) {
                                info.el.style.borderLeft = '4px solid #ef4444';
                            }
                        }
                    });
                    this.calendar.render();
                },

                loadCalendarEvents(fetchInfo, successCallback, failureCallback) {
                    const params = new URLSearchParams({
                        start: fetchInfo.startStr,
                        end: fetchInfo.endStr
                    });
                    
                    fetch(`${config.calendar_url}?${params}`)
                        .then(response => response.json())
                        .then(data => {
                            this.blockedEvents = data;
                            successCallback(data);
                        })
                        .catch(error => {
                            console.error('Error loading calendar events:', error);
                            failureCallback(error);
                        });
                },

                loadCalendar() {
                    if (this.calendar) {
                        this.calendar.refetchEvents();
                    }
                },

                handleDateSelect(selectInfo) {
                    this.start_date = selectInfo.startStr;
                    
                    if (this.duration_preset === 'custom') {
                        this.end_date = selectInfo.endStr;
                    }
                    
                    this.applyDurationPreset();
                    this.updateDatetimes();
                    
                    // Clear selection
                    this.calendar.unselect();
                },

                applyDurationPreset() {
                    if (!this.start_date) return;
                    
                    const startDate = new Date(this.start_date);
                    
                    switch (this.duration_preset) {
                        case 'full_day':
                            this.end_date = this.start_date;
                            this.start_time = '00:00';
                            this.end_time = '23:59';
                            break;
                            
                        case 'full_2_days':
                            const twoDaysLater = new Date(startDate);
                            twoDaysLater.setDate(twoDaysLater.getDate() + 1);
                            this.end_date = twoDaysLater.toISOString().split('T')[0];
                            this.start_time = '00:00';
                            this.end_time = '23:59';
                            break;
                            
                        case 'full_week':
                            const oneWeekLater = new Date(startDate);
                            oneWeekLater.setDate(oneWeekLater.getDate() + 6);
                            this.end_date = oneWeekLater.toISOString().split('T')[0];
                            this.start_time = '00:00';
                            this.end_time = '23:59';
                            break;
                            
                        case 'custom':
                            if (!this.end_date) {
                                this.end_date = this.start_date;
                            }
                            break;
                    }
                    
                    this.updateDatetimes();
                },

                updateDatetimes() {
                    if (this.start_date && this.start_time) {
                        this.start_datetime = `${this.start_date} ${this.start_time}:00`;
                    }
                    
                    const endDate = this.end_date || this.start_date;
                    if (endDate && this.end_time) {
                        this.end_datetime = `${endDate} ${this.end_time}:00`;
                    }
                    
                    // Check for conflicts after updating datetimes
                    this.checkConflict();
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
                    
                    fetch(config.check_conflict_url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': config.csrf_token,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            menu_id: this.menu_id,
                            start_datetime: this.start_datetime,
                            end_datetime: this.end_datetime,
                            all_menus: this.all_menus
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        this.conflict.has_conflict = data.has_conflict;
                        this.conflict.details = data.conflict_details || [];
                    })
                    .catch(error => {
                        console.error('Error checking conflict:', error);
                    })
                    .finally(() => {
                        this.isLoading = false;
                    });
                }
            }
        }
    </script>
</x-layouts.app>