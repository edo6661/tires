<x-layouts.app>
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h1 class="text-2xl font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-plus-circle mr-3 text-blue-600"></i>
                    Buat Reservasi Baru
                </h1>
            </div>

            <div class="p-6">
                <form method="POST" action="{{ route('admin.reservation.store') }}" x-data="reservationForm()" @submit.prevent="submitForm">
                    @csrf

                    <!-- Alert untuk availability check -->
                    <div x-show="availabilityMessage" 
                         :class="availabilityStatus === 'available' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800'"
                         class="border rounded-md p-4 mb-6" 
                         style="display: none;">
                        <div class="flex items-center">
                            <i :class="availabilityStatus === 'available' ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle'" 
                               class="mr-2"></i>
                            <span x-text="availabilityMessage"></span>
                        </div>
                    </div>

                    <!-- Error Messages -->
                    @if($errors->any())
                        <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
                            <div class="flex">
                                <i class="fas fa-exclamation-triangle text-red-400 mr-2"></i>
                                <div>
                                    <h3 class="text-sm font-medium text-red-800">Terjadi kesalahan:</h3>
                                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Success Message -->
                    @if(session('success'))
                        <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-6">
                            <div class="flex">
                                <i class="fas fa-check-circle text-green-400 mr-2"></i>
                                <p class="text-sm text-green-800">{{ session('success') }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Customer Type Selection -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Tipe Pelanggan</label>
                            <div class="flex space-x-4">
                                <label class="flex items-center">
                                    <input type="radio" name="customer_type" value="existing" x-model="customerType" class="mr-2">
                                    <span>Pelanggan Terdaftar</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="customer_type" value="guest" x-model="customerType" class="mr-2">
                                    <span>Pelanggan Tamu</span>
                                </label>
                            </div>
                        </div>

                        <!-- Existing Customer Selection -->
                        <div x-show="customerType === 'existing'" class="md:col-span-2">
                            <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">Pilih Pelanggan</label>
                            <select name="user_id" id="user_id" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Pilih Pelanggan...</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->full_name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Guest Customer Information -->
                        <div x-show="customerType === 'guest'" class="md:col-span-2">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap *</label>
                                    <input type="text" name="full_name" id="full_name" value="{{ old('full_name') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label for="full_name_kana" class="block text-sm font-medium text-gray-700 mb-2">Nama Kana *</label>
                                    <input type="text" name="full_name_kana" id="full_name_kana" value="{{ old('full_name_kana') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon *</label>
                                    <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>

                        <!-- Menu Selection -->
                        <div>
                            <label for="menu_id" class="block text-sm font-medium text-gray-700 mb-2">Menu *</label>
                            <select name="menu_id" id="menu_id" x-model="selectedMenu" @change="checkAvailability()"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="">Pilih Menu...</option>
                                @foreach($menus as $menu)
                                    <option value="{{ $menu->id }}" data-price="{{ $menu->price }}" data-duration="{{ $menu->required_time }}"
                                            {{ old('menu_id') == $menu->id ? 'selected' : '' }}>
                                        {{ $menu->name }} ({{ number_format($menu->price, 0, ',', '.') }} yen - {{ $menu->required_time }} menit)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Reservation Date Time -->
                        <div>
                            <label for="reservation_datetime" class="block text-sm font-medium text-gray-700 mb-2">Tanggal & Waktu Reservasi *</label>
                            <input type="datetime-local" name="reservation_datetime" id="reservation_datetime" 
                                   x-model="reservationDateTime" @change="checkAvailability()"
                                   value="{{ old('reservation_datetime') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>

                        <!-- Number of People -->
                        <div>
                            <label for="number_of_people" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Orang *</label>
                            <input type="number" name="number_of_people" id="number_of_people" min="1" value="{{ old('number_of_people', 1) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>

                        <!-- Amount -->
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Total Biaya *</label>
                            <input type="number" name="amount" id="amount" step="0.01" value="{{ old('amount') }}"
                                   x-model="amount" readonly
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>

                        <!-- Status -->
                        <div class="md:col-span-2">
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" id="status" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ old('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>

                        <!-- Notes -->
                        <div class="md:col-span-2">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                            <textarea name="notes" id="notes" rows="3" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.reservation.index') }}" 
                           class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Batal
                        </a>
                        <button type="button" @click="checkAvailability()" 
                                class="px-4 py-2 text-sm font-medium text-white bg-yellow-600 rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                            <i class="fas fa-check mr-2"></i>
                            Cek Ketersediaan
                        </button>
                        <button type="submit" 
                                :disabled="!isFormValid || availabilityStatus !== 'available'"
                                :class="(!isFormValid || availabilityStatus !== 'available') ? 'opacity-50 cursor-not-allowed' : ''"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-save mr-2"></i>
                            Simpan Reservasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function reservationForm() {
            return {
                customerType: '{{ old('customer_type', 'existing') }}',
                selectedMenu: '{{ old('menu_id') }}',
                reservationDateTime: '{{ old('reservation_datetime') }}',
                amount: '{{ old('amount') }}',
                availabilityStatus: null,
                availabilityMessage: '',
                isFormValid: false,
                
                init() {
                    this.updateAmount();
                    this.validateForm();
                },
                
                updateAmount() {
                    if (this.selectedMenu) {
                        const menuSelect = document.getElementById('menu_id');
                        const selectedOption = menuSelect.options[menuSelect.selectedIndex];
                        if (selectedOption && selectedOption.dataset.price) {
                            this.amount = selectedOption.dataset.price;
                        }
                    }
                },
                
                validateForm() {
                    const menuSelected = this.selectedMenu && this.selectedMenu !== '';
                    const dateTimeSelected = this.reservationDateTime && this.reservationDateTime !== '';
                    
                    let customerInfoValid = false;
                    if (this.customerType === 'existing') {
                        customerInfoValid = document.getElementById('user_id').value !== '';
                    } else {
                        const fullName = document.getElementById('full_name').value;
                        const fullNameKana = document.getElementById('full_name_kana').value;
                        const email = document.getElementById('email').value;
                        const phoneNumber = document.getElementById('phone_number').value;
                        customerInfoValid = fullName && fullNameKana && email && phoneNumber;
                    }
                    
                    this.isFormValid = menuSelected && dateTimeSelected && customerInfoValid;
                },
                
                async checkAvailability() {
                    if (!this.selectedMenu || !this.reservationDateTime) {
                        this.availabilityMessage = 'Harap pilih menu dan tanggal/waktu terlebih dahulu';
                        this.availabilityStatus = 'error';
                        return;
                    }
                    
                    try {
                        const response = await fetch('{{ route('admin.reservation.check-availability') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                menu_id: this.selectedMenu,
                                reservation_datetime: this.reservationDateTime
                            })
                        });
                        
                        const data = await response.json();
                        console.log(data);
                        
                        if (data.available) {
                            this.availabilityStatus = 'available';
                            this.availabilityMessage = 'Waktu tersedia untuk reservasi';
                        } else {
                            this.availabilityStatus = 'unavailable';
                            this.availabilityMessage = 'Waktu tidak tersedia - bentrok dengan periode blokir atau reservasi lain';
                        }
                    } catch (error) {
                        this.availabilityStatus = 'error';
                        this.availabilityMessage = 'Terjadi kesalahan saat mengecek ketersediaan';
                    }
                },
                
                submitForm() {
                    this.validateForm();
                    if (this.isFormValid && this.availabilityStatus === 'available') {
                        document.querySelector('form').submit();
                    }
                }
            }
        }
        
        // Add CSRF token to head if not exists
        if (!document.querySelector('meta[name="csrf-token"]')) {
            const meta = document.createElement('meta');
            meta.name = 'csrf-token';
            meta.content = '{{ csrf_token() }}';
            document.head.appendChild(meta);
        }
        
        // Auto-update amount when menu changes
        document.getElementById('menu_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption && selectedOption.dataset.price) {
                document.getElementById('amount').value = selectedOption.dataset.price;
            }
        });
    </script>
</x-layouts.app>