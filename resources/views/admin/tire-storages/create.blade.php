<x-layouts.app>
    <div class="container space-y-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Add Tire Storage</h1>
                <p class="text-gray-600 mt-1">Create a new tire storage record for a customer</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.tire-storage.index') }}"
                   class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200 flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    Back
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
                    <i class="fas fa-plus-circle text-blue-600"></i>
                    New Tire Storage Form
                </h3>
            </div>
            <form action="{{ route('admin.tire-storage.store') }}" method="POST" class="p-6 space-y-6" x-data="tireStorageCreate()">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user mr-1"></i>
                            Customer <span class="text-red-500">*</span>
                        </label>
                        <select name="user_id" id="user_id"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('user_id') border-red-500 @enderror">
                            <option value="">Select Customer</option>
                            @foreach($users ?? [] as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
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
                        Tire Information
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="tire_brand" class="block text-sm font-medium text-gray-700 mb-2">
                                Tire Brand <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="tire_brand" id="tire_brand"
                                   value="{{ old('tire_brand') }}"
                                   placeholder="e.g., Bridgestone, Michelin, Goodyear"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tire_brand') border-red-500 @enderror">
                            @error('tire_brand')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="tire_size" class="block text-sm font-medium text-gray-700 mb-2">
                                Tire Size <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="tire_size" id="tire_size"
                                   value="{{ old('tire_size') }}"
                                   placeholder="e.g., 225/60R16, 185/65R15"
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
                        Storage Schedule
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="storage_start_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Storage Start Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="storage_start_date" id="storage_start_date"
                                   value="{{ old('storage_start_date', date('Y-m-d')) }}"
                                   @change="calculateFee()"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('storage_start_date') border-red-500 @enderror">
                            @error('storage_start_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="planned_end_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Planned End Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="planned_end_date" id="planned_end_date"
                                   value="{{ old('planned_end_date') }}"
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
                        Fee & Status
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="storage_fee" class="block text-sm font-medium text-gray-700 mb-2">
                                Storage Fee (IDR)
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500">IDR</span>
                                <input type="number" name="storage_fee" id="storage_fee"
                                       value="{{ old('storage_fee') }}"
                                       step="0.01" min="0"
                                       placeholder="0"
                                       class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('storage_fee') border-red-500 @enderror">
                            </div>
                            <p class="text-sm text-gray-500 mt-1">
                                <i class="fas fa-info-circle"></i>
                                Leave blank for auto-calculation (IDR 50,000/month)
                            </p>
                            <div x-show="calculatedFee > 0" class="text-sm text-blue-600 mt-1">
                                <i class="fas fa-calculator"></i>
                                Calculated fee: IDR <span x-text="formatNumber(calculatedFee)"></span>
                            </div>
                            @error('storage_fee')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Status
                            </label>
                            <select name="status" id="status"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror">
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>
                                    <i class="fas fa-play-circle"></i> Active
                                </option>
                                <option value="ended" {{ old('status') == 'ended' ? 'selected' : '' }}>
                                    <i class="fas fa-stop-circle"></i> Ended
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
                            Notes
                        </label>
                        <textarea name="notes" id="notes" rows="4"
                                  placeholder="Additional notes about this tire storage..."
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="border-t pt-6 flex flex-col sm:flex-row gap-3 justify-end">
                    <a href="{{ route('admin.tire-storage.index') }}"
                       class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-times"></i>
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i>
                        Save Storage
                    </button>
                </div>
            </form>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <div class="bg-blue-100 p-2 rounded-full">
                    <i class="fas fa-info-circle text-blue-600"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-blue-900 mb-2">Important Information</h4>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>• The planned end date must be after the storage start date.</li>
                        <li>• The storage fee will be auto-calculated if left blank (IDR 50,000 per month).</li>
                        <li>• "Active" status means the storage is ongoing.</li>
                        <li>• "Ended" status means the storage has concluded.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <script>
        function tireStorageCreate() {
            return {
                calculatedFee: 0,
                calculateFee() {
                    const startDate = document.getElementById('storage_start_date').value;
                    const endDate = document.getElementById('planned_end_date').value;
                    if (startDate && endDate) {
                        const start = new Date(startDate);
                        const end = new Date(endDate);
                        if (end > start) {
                            const months = this.getMonthsDifference(start, end);
                            const monthlyRate = 50000; 
                            this.calculatedFee = months * monthlyRate;
                            const feeInput = document.getElementById('storage_fee');
                            if (!feeInput.value) {
                                feeInput.value = this.calculatedFee;
                            }
                        } else {
                            this.calculatedFee = 0;
                        }
                    }
                },
                getMonthsDifference(start, end) {
                    const months = (end.getFullYear() - start.getFullYear()) * 12 +
                                   (end.getMonth() - start.getMonth());
                    return months < 1 ? 1 : months; 
                },
                formatNumber(num) {
                    return new Intl.NumberFormat('id-ID').format(num);
                }
            }
        }
        document.getElementById('storage_start_date').addEventListener('change', function() {
            const startDate = this.value;
            const endDateInput = document.getElementById('planned_end_date');
            if (startDate) {
                const minEndDate = new Date(startDate);
                minEndDate.setDate(minEndDate.getDate() + 1);
                endDateInput.min = minEndDate.toISOString().split('T')[0];
            }
        });
        document.addEventListener('DOMContentLoaded', function() {
            const startDate = document.getElementById('storage_start_date').value;
            const endDate = document.getElementById('planned_end_date').value;
            if (startDate && endDate) {
                const component = document.querySelector('[x-data]').__x.$data;
                if (component.calculateFee) {
                    component.calculateFee();
                }
            }
        });
    </script>
</x-layouts.app>