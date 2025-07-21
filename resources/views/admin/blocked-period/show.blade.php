<x-layouts.app>
    <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        {{-- Header Halaman --}}
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Detail Periode Blokir</h1>
                <p class="text-sm text-gray-500 mt-1">Menampilkan rincian dari periode blokir yang dipilih.</p>
            </div>
            <a href="{{ route('admin.blocked-period.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>

        {{-- Card Detail --}}
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-5">
                {{-- Detail Grid --}}
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
                    {{-- Menu yang Diblokir --}}
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">
                            Menu yang Diblokir
                        </dt>
                        <dd class="mt-1 text-lg text-gray-900 font-semibold flex items-center">
                            @if($blockedPeriod->all_menus)
                                <span class="px-3 py-1 text-sm font-bold text-white bg-red-600 rounded-full">
                                    <i class="fas fa-globe-asia mr-1"></i> Semua Menu
                                </span>
                            @elseif($blockedPeriod->menu)
                                <span class="w-4 h-4 rounded-full mr-2" style="background-color: {{ $blockedPeriod->menu->color ?? '#6B7280' }};"></span>
                                {{ $blockedPeriod->menu->name }}
                            @else
                                <span class="text-gray-500 italic">Menu tidak tersedia</span>
                            @endif
                        </dd>
                    </div>

                    {{-- Waktu Mulai --}}
                    <div>
                        <dt class="text-sm font-medium text-gray-500">
                            <i class="far fa-clock text-gray-400 mr-1"></i> Waktu Mulai
                        </dt>
                        <dd class="mt-1 text-gray-900">
                            {{ $blockedPeriod->start_datetime->format('d F Y, H:i') }}
                        </dd>
                    </div>

                    {{-- Waktu Selesai --}}
                    <div>
                        <dt class="text-sm font-medium text-gray-500">
                             <i class="far fa-check-circle text-gray-400 mr-1"></i> Waktu Selesai
                        </dt>
                        <dd class="mt-1 text-gray-900">
                            {{ $blockedPeriod->end_datetime->format('d F Y, H:i') }}
                        </dd>
                    </div>

                    {{-- Durasi --}}
                    <div>
                        <dt class="text-sm font-medium text-gray-500">
                            <i class="fas fa-hourglass-half text-gray-400 mr-1"></i> Durasi
                        </dt>
                        <dd class="mt-1 text-gray-900">
                            {{ $blockedPeriod->getDurationText() }}
                        </dd>
                    </div>

                    {{-- Status --}}
                    <div>
                        <dt class="text-sm font-medium text-gray-500">
                            <i class="fas fa-circle-info text-gray-400 mr-1"></i> Status
                        </dt>
                        <dd class="mt-1">
                            @php
                                $now = now();
                                if ($blockedPeriod->isActive()) {
                                    $statusClass = 'bg-green-100 text-green-800';
                                    $statusText = 'Aktif';
                                } elseif ($blockedPeriod->start_datetime->isFuture()) {
                                    $statusClass = 'bg-blue-100 text-blue-800';
                                    $statusText = 'Akan Datang';
                                } else {
                                    $statusClass = 'bg-gray-100 text-gray-800';
                                    $statusText = 'Selesai';
                                }
                            @endphp
                            <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full {{ $statusClass }}">
                                {{ $statusText }}
                            </span>
                        </dd>
                    </div>

                    {{-- Alasan --}}
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">
                            <i class="fas fa-comment-alt text-gray-400 mr-1"></i> Alasan
                        </dt>
                        <dd class="mt-1 text-gray-900 prose max-w-none">
                            {!! nl2br(e($blockedPeriod->reason)) !!}
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Footer Card (Aksi) --}}
            <div class="bg-gray-50 px-6 py-4 flex justify-between items-center">
                <div class="text-sm text-gray-500">
                    Dibuat: {{ $blockedPeriod->created_at->diffForHumans() }} |
                    Diperbarui: {{ $blockedPeriod->updated_at->diffForHumans() }}
                </div>
                <div class="flex items-center space-x-3" x-data="{ showConfirm: false }">
                    <a href="{{ route('admin.blocked-period.edit', $blockedPeriod->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 focus:outline-none focus:border-yellow-700 focus:ring focus:ring-yellow-200 disabled:opacity-25 transition">
                        <i class="fas fa-edit mr-2"></i>
                        Edit
                    </a>
                    <button @click="showConfirm = true" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:border-red-900 focus:ring focus:ring-red-300 disabled:opacity-25 transition">
                        <i class="fas fa-trash-alt mr-2"></i>
                        Hapus
                    </button>

                    {{-- Modal Konfirmasi Hapus --}}
                    <div x-show="showConfirm" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
                        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md" @click.away="showConfirm = false">
                            <h3 class="text-lg font-bold">Konfirmasi Penghapusan</h3>
                            <p class="mt-2 text-sm text-gray-600">Anda yakin ingin menghapus periode blokir ini? Tindakan ini tidak dapat diurungkan.</p>
                            <div class="mt-6 flex justify-end space-x-3">
                                <button @click="showConfirm = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Batal</button>
                                <form action="{{ route('admin.blocked-period.destroy', $blockedPeriod->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Ya, Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>