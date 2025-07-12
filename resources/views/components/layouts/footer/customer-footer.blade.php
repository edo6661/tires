@auth
    @if(auth()->user()->isAdmin())
        @else
            <footer class="text-center text-xs text-gray-500 py-6 border-t border-gray-200 px-6 md:px-12 flex flex-col md:flex-row justify-between items-center gap-4 md:gap-0 bg-white">
                    <div class="space-x-2">
                        <a href="#" class="hover:text-green-600 transition">RESERVATION Terms of Service</a>
                    <span>|</span>
                    <a href="#" class="hover:text-green-600 transition">RESERVATION Privacy Policy</a>
                </div>
                <div class="flex items-center gap-3 bg-white border border-gray-300 rounded-lg px-3 py-1 shadow-sm select-none">
                    <span class="text-gray-700 text-xs font-semibold">Free Reservation System</span>
                </div>
            </footer>
        @endif
    @endauth
@guest
    <footer class="text-center text-xs text-gray-500 py-6 border-t border-gray-200 px-6 md:px-12 flex flex-col md:flex-row justify-between items-center gap-4 md:gap-0 bg-white">
        <div class="space-x-2">
            <a href="#" class="hover:text-green-600 transition">RESERVATION Terms of Service</a>
            <span>|</span>
            <a href="#" class="hover:text-green-600 transition">RESERVATION Privacy Policy</a>
        </div>
        <div class="flex items-center gap-3 bg-white border border-gray-300 rounded-lg px-3 py-1 shadow-sm select-none">
            <span class="text-gray-700 text-xs font-semibold">Free Reservation System</span>
        </div>
    </footer>
@endguest
