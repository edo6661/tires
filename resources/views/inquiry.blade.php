<x-layouts.app>
    <div class="flex flex-col md:flex-row gap-8">
        <div class="w-full md:w-1/3 lg:w-1/4 bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="space-y-6">
                <h2 class="text-xl font-semibold text-gray-800">Takanawa Gateway City</h2>
                
                <div>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Lokasi</h3>
                    <p class="text-gray-600">Saitama-ken, Iruma-shi, Miyadera 2095-8</p>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Jam Operasional</h3>
                    <ul class="text-gray-600 space-y-1">
                        <li class="flex justify-between">
                            <span>Senin</span>
                            <span>10:00 ~ 18:00</span>
                        </li>
                        <li class="flex justify-between">
                            <span>Selasa</span>
                            <span>10:00 ~ 18:00</span>
                        </li>
                        <li class="flex justify-between text-gray-400">
                            <span>Rabu</span>
                            <span>Libur</span>
                        </li>
                        <li class="flex justify-between text-gray-400">
                            <span>Kamis</span>
                            <span>Libur</span>
                        </li>
                        <li class="flex justify-between">
                            <span>Jumat</span>
                            <span>10:00 ~ 18:00</span>
                        </li>
                        <li class="flex justify-between">
                            <span>Sabtu</span>
                            <span>10:00 ~ 18:00</span>
                        </li>
                        <li class="flex justify-between">
                            <span>Minggu</span>
                            <span>10:00 ~ 18:00</span>
                        </li>
                    </ul>
                </div>
                
                <div class="pt-4 border-t border-gray-200">
                    <a href="#" class="text-gray-600 hover:text-green-600 transition">Tentang Kami</a>
                    <a href="#" class="text-gray-600 hover:text-green-600 transition block mt-2">Syarat Penggunaan</a>
                </div>
            </div>
        </div>
        
        <div class="flex-1 bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Hubungi Kami</h2>
            <p class="text-gray-600 mb-6">Silakan masukkan konten pertanyaan Anda. Jika Anda memiliki akun RESERVA, silakan login dari sini.</p>
            
            <form class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama *</label>
                    <input type="text" id="name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="Tokyo Taro">
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Alamat Email *</label>
                    <input type="email" id="email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="alamat email">
                </div>
                
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                    <input type="tel" id="phone" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="00-0000-0000">
                </div>
                
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Judul *</label>
                    <input type="text" id="subject" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>
                
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Konten Pertanyaan *</label>
                    <textarea id="message" rows="5" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="Silakan masukkan konten pertanyaan Anda"></textarea>
                </div>
                
                <button type="submit" class="w-full bg-primary hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                    Kirim Pertanyaan
                </button>
            </form>
        </div>
    </div>
</x-layouts.app>