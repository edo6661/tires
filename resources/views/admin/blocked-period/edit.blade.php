<x-layouts.app>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
         x-data="blockedPeriodForm({
            menus: {{ Js::from($menus) }},
            blockedPeriod: {{ Js::from($blockedPeriod) }},
            old_input: {{ Js::from(session()->getOldInput()) }},
            check_conflict_url: '{{ route('admin.blocked-period.check-conflict') }}',
            csrf_token: '{{ csrf_token() }}'
         })">

        {{-- Header Halaman --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Periode Blokir</h1>
                <p class="mt-1 text-sm text-gray-600">Perbarui periode waktu di mana menu tidak tersedia untuk reservasi.</p>
            </div>
            <a href="{{ route('admin.blocked-period.index') }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Daftar
            </a>
        </div>

        {{-- Notifikasi Error dari Session --}}
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

        {{-- Form Edit --}}
        <form action="{{ route('admin.blocked-period.update', $blockedPeriod->id) }}" method="POST" class="bg-white p-6 md:p-8 rounded-lg shadow-md space-y-6">
            @csrf
            @method('PUT') {{-- Metode HTTP untuk update --}}

            {{-- Pilihan Menu --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2 flex items-center">
                    <input id="all_menus" name="all_menus" type="checkbox" value="1" x-model="all_menus"
                           class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <label for="all_menus" class="ml-3 block text-sm font-medium text-gray-900">
                        Blokir untuk Semua Menu?
                    </label>
                </div>
                <div class="md:col-span-2" x-show="!all_menus" x-transition>
                    <label for="menu_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Menu Spesifik</label>
                    <select id="menu_id" name="menu_id" x-model="menu_id" :disabled="all_menus" @change="checkConflict"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm disabled:bg-gray-100 disabled:cursor-not-allowed">
                        <option value="">-- Pilih satu menu --</option>
                        @foreach($menus as $menu)
                            <option value="{{ $menu->id }}">
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

            {{-- Pilihan Waktu --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="start_datetime" class="block text-sm font-medium text-gray-700 mb-1">Waktu Mulai</label>
                    <input type="datetime-local" id="start_datetime" name="start_datetime" x-model="start_datetime" @input.debounce.750ms="checkConflict"
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('start_datetime')
                        <div class="mt-2 text-sm text-red-600 whitespace-pre-line">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="end_datetime" class="block text-sm font-medium text-gray-700 mb-1">Waktu Selesai</label>
                    <input type="datetime-local" id="end_datetime" name="end_datetime" x-model="end_datetime" @input.debounce.750ms="checkConflict"
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('end_datetime')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Notifikasi Konflik --}}
            <div x-show="conflict.has_conflict" x-transition class="md:col-span-2 p-4 bg-red-50 border-l-4 border-red-400 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-red-400 mt-0.5"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-800 font-semibold">Terdeteksi Konflik Jadwal!</p>
                        <p class="text-sm text-red-700 mt-1">Periode yang Anda masukkan tumpang tindih dengan jadwal berikut:</p>
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

            {{-- Alasan Blokir --}}
            <div>
                <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Alasan</label>
                <textarea id="reason" name="reason" rows="4" x-model="reason"
                          class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                          placeholder="Contoh: Perawatan rutin, hari libur, acara pribadi, dll."></textarea>
                @error('reason')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex justify-end pt-4">
                <button type="submit"
                        :disabled="isLoading || conflict.has_conflict"
                        class="inline-flex items-center justify-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:bg-gray-400 disabled:cursor-not-allowed transition">
                    <i class="fas fa-spinner fa-spin mr-2" x-show="isLoading" x-cloak></i>
                    <span x-text="isLoading ? 'Memeriksa...' : 'Simpan Perubahan'"></span>
                </button>
            </div>
        </form>
    </div>

    {{-- Script Alpine.js untuk logika form --}}
    <script>
        function blockedPeriodForm(config) {
            const formatDateTimeLocal = (datetime) => {
                if (!datetime) return '';
                try {
                    const date = new Date(datetime);
                    // Sesuaikan dengan timezone lokal
                    date.setMinutes(date.getMinutes() - date.getTimezoneOffset());
                    // Format ke YYYY-MM-DDTHH:mm
                    return date.toISOString().slice(0, 16);
                } catch (e) {
                    return '';
                }
            };

            return {
                // Inisialisasi data: prioritaskan old input, lalu data dari model
                all_menus: config.old_input.hasOwnProperty('all_menus')
                    ? (config.old_input.all_menus === '1' || config.old_input.all_menus === true)
                    : config.blockedPeriod.all_menus,
                menu_id: config.old_input.menu_id || config.blockedPeriod.menu_id || '',
                start_datetime: config.old_input.start_datetime || formatDateTimeLocal(config.blockedPeriod.start_datetime),
                end_datetime: config.old_input.end_datetime || formatDateTimeLocal(config.blockedPeriod.end_datetime),
                reason: config.old_input.reason !== undefined ? config.old_input.reason : (config.blockedPeriod.reason || ''),
                excludeId: config.blockedPeriod.id, // ID untuk dikecualikan dari pemeriksaan konflik

                isLoading: false,
                conflict: {
                    has_conflict: false,
                    details: []
                },

                init() {
                    // Cek konflik saat pertama kali halaman dimuat jika data sudah lengkap
                    if (this.start_datetime && this.end_datetime && (this.menu_id || this.all_menus)) {
                        this.checkConflict();
                    }

                    // Tambahkan listener untuk `all_menus`
                    this.$watch('all_menus', (value) => {
                        if (value) {
                            this.menu_id = '';
                        }
                        this.checkConflict();
                    });
                },

                checkConflict() {
                    // Batalkan jika data belum lengkap
                    if (!this.start_datetime || !this.end_datetime || (!this.all_menus && !this.menu_id)) {
                        this.conflict = { has_conflict: false, details: [] };
                        return;
                    }
                    // Batalkan jika tanggal mulai lebih besar atau sama dengan tanggal selesai
                    if (new Date(this.start_datetime) >= new Date(this.end_datetime)) {
                        this.conflict = { has_conflict: false, details: [] };
                        return;
                    }

                    this.isLoading = true;
                    axios.post(config.check_conflict_url, {
                        menu_id: this.menu_id,
                        start_datetime: this.start_datetime,
                        end_datetime: this.end_datetime,
                        all_menus: this.all_menus,
                        exclude_id: this.excludeId // Kirim ID yang sedang diedit
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
                        // Reset status konflik jika terjadi error
                        this.conflict = { has_conflict: false, details: [] };
                    }).finally(() => {
                        this.isLoading = false;
                    });
                }
            }
        }
    </script>
</x-layouts.app>