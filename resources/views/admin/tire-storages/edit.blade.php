{{-- resources/views/admin/tire-storages/edit.blade.php --}}
<x-layouts.app>
    <div class="container space-y-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ __('admin/tire-storage/edit.title') }}</h1>
                <p class="text-gray-600 mt-1">{{ __('admin/tire-storage/edit.description') }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.tire-storage.index') }}"
                   class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200 flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('admin/tire-storage/edit.back_button') }}
                </a>
            </div>
        </div>
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg" x-data="{ show: true }" x-show="show">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                    <button @click="show = false" class="text-green-700 hover:text-green-900">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg" x-data="{ show: true }" x-show="show">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                    <button @click="show = false" class="text-red-700 hover:text-red-900">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-edit text-blue-600"></i>
                    {{ __('admin/tire-storage/edit.form_title') }}
                </h3>
            </div>
            <form action="{{ route('admin.tire-storage.update', $tireStorage->id) }}" method="POST" class="p-6 space-y-6" x-data="tireStorageForm()">
                @csrf
                @method('PUT') {{-- Use PUT/PATCH for updates --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user mr-1"></i>
                            {{ __('admin/tire-storage/edit.customer_section.label') }} <span class="text-red-500">*</span>
                        </label>
                        <select name="user_id" id="user_id"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('user_id') border-red-500 @enderror">
                            <option value="">{{ __('admin/tire-storage/edit.customer_section.select_placeholder') }}</option>
                            @foreach($users ?? [] as $user)
                                <option value="{{ $user->id }}" {{ old('user_id', $tireStorage->user_id) == $user->id ? 'selected' : '' }}>
                                    {{ $user->full_name }} - {{ $user->email }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="border-t pt-6">
                    <h4 class="text-md font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-tire text-blue-600"></i>
                        {{ __('admin/tire-storage/edit.tire_info_section.title') }}
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="tire_brand" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('admin/tire-storage/edit.tire_info_section.brand_label') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="tire_brand" id="tire_brand"
                                   value="{{ old('tire_brand', $tireStorage->tire_brand) }}"
                                   placeholder="{{ __('admin/tire-storage/edit.tire_info_section.brand_placeholder') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tire_brand') border-red-500 @enderror">
                            @error('tire_brand')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="tire_size" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('admin/tire-storage/edit.tire_info_section.size_label') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="tire_size" id="tire_size"
                                   value="{{ old('tire_size', $tireStorage->tire_size) }}"
                                   placeholder="{{ __('admin/tire-storage/edit.tire_info_section.size_placeholder') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tire_size') border-red-500 @enderror">
                            @error('tire_size')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="border-t pt-6">
                    <h4 class="text-md font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-calendar-alt text-blue-600"></i>
                        {{ __('admin/tire-storage/edit.schedule_section.title') }}
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="storage_start_date" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('admin/tire-storage/edit.schedule_section.start_date_label') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="storage_start_date" id="storage_start_date"
                                   value="{{ old('storage_start_date', $tireStorage->storage_start_date->format('Y-m-d')) }}"
                                   @change="calculateFee()"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('storage_start_date') border-red-500 @enderror">
                            @error('storage_start_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="planned_end_date" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('admin/tire-storage/edit.schedule_section.end_date_label') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="planned_end_date" id="planned_end_date"
                                   value="{{ old('planned_end_date', $tireStorage->planned_end_date->format('Y-m-d')) }}"
                                   @change="calculateFee()"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('planned_end_date') border-red-500 @enderror">
                            @error('planned_end_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="border-t pt-6">
                    <h4 class="text-md font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-money-bill-wave text-blue-600"></i>
                        {{ __('admin/tire-storage/edit.fee_status_section.title') }}
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="storage_fee" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('admin/tire-storage/edit.fee_status_section.fee_label', ['currency' => 'IDR']) }}
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500">IDR</span>
                                <input type="number" name="storage_fee" id="storage_fee"
                                       value="{{ old('storage_fee', $tireStorage->storage_fee) }}"
                                       step="0.01" min="0"
                                       placeholder="0"
                                       class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('storage_fee') border-red-500 @enderror">
                            </div>
                            <p class="text-sm text-gray-500 mt-1">
                                <i class="fas fa-info-circle"></i>
                                {{ __('admin/tire-storage/edit.fee_status_section.fee_auto_calc_note', ['currency' => 'IDR', 'rate' => '50,000']) }}
                            </p>
                            <div x-show="calculatedFee > 0 && !document.getElementById('storage_fee').value" class="text-sm text-blue-600 mt-1">
                                <i class="fas fa-calculator"></i>
                                {{ __('admin/tire-storage/edit.fee_status_section.calculated_fee_note', ['currency' => 'IDR']) }} <span x-text="formatNumber(calculatedFee)"></span>
                            </div>
                            @error('storage_fee')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('admin/tire-storage/edit.fee_status_section.status_label') }}
                            </label>
                            <select name="status" id="status"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror">
                                <option value="active" {{ old('status', $tireStorage->status) == 'active' ? 'selected' : '' }}>
                                    {{ __('admin/tire-storage/edit.fee_status_section.status_active') }}
                                </option>
                                <option value="ended" {{ old('status', $tireStorage->status) == 'ended' ? 'selected' : '' }}>
                                    {{ __('admin/tire-storage/edit.fee_status_section.status_ended') }}
                                </option>
                            </select>
                            @error('status')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="border-t pt-6">
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-sticky-note mr-1"></i>
                            {{ __('admin/tire-storage/edit.notes_section.label') }}
                        </label>
                        <textarea name="notes" id="notes" rows="4"
                                  placeholder="{{ __('admin/tire-storage/edit.notes_section.placeholder') }}"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('notes') border-red-500 @enderror">{{ old('notes', $tireStorage->notes) }}</textarea>
                        @error('notes')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="border-t pt-6 flex flex-col sm:flex-row gap-3 justify-end">
                    <a href="{{ route('admin.tire-storage.index') }}"
                       class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-times"></i>
                        {{ __('admin/tire-storage/edit.buttons.cancel') }}
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i>
                        {{ __('admin/tire-storage/edit.buttons.update') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function tireStorageForm() {
            return {
                calculatedFee: 0,
                init() {
                    this.calculateFee();
                    this.updateMinEndDate();
                },
                calculateFee() {
                    const startDateInput = document.getElementById('storage_start_date');
                    const endDateInput = document.getElementById('planned_end_date');
                    const feeInput = document.getElementById('storage_fee');
                    if (startDateInput.value && endDateInput.value) {
                        const start = new Date(startDateInput.value);
                        const end = new Date(endDateInput.value);
                        if (end > start) {
                            const months = this.getMonthsDifference(start, end);
                            const monthlyRate = 50000;
                            this.calculatedFee = months * monthlyRate;
                        } else {
                            this.calculatedFee = 0;
                        }
                    } else {
                        this.calculatedFee = 0;
                    }
                },
                getMonthsDifference(start, end) {
                    let months = (end.getFullYear() - start.getFullYear()) * 12;
                    months -= start.getMonth();
                    months += end.getMonth();
                    if (end.getDate() >= start.getDate() && months === 0) {
                        return 1;
                    } else if (end.getDate() < start.getDate()) {
                        months--;
                    }
                    return months <= 0 ? 1 : months + 1;
                },
                formatNumber(num) {
                    return new Intl.NumberFormat('id-ID').format(num);
                },
                updateMinEndDate() {
                    const startDate = document.getElementById('storage_start_date').value;
                    const endDateInput = document.getElementById('planned_end_date');
                    if (startDate) {
                        const minEndDate = new Date(startDate);
                        minEndDate.setDate(minEndDate.getDate() + 1);
                        endDateInput.min = minEndDate.toISOString().split('T')[0];
                    }
                }
            }
        }
        document.getElementById('storage_start_date').addEventListener('change', function() {
            const component = document.querySelector('[x-data]').__x.$data;
            if (component.updateMinEndDate) {
                component.updateMinEndDate();
            }
        });
    </script>
</x-layouts.app>