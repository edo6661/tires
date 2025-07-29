@auth
    @if(auth()->user()->isAdmin())
    @else
        <footer class="text-center text-xs text-footer-text py-6 px-6 md:px-12 flex flex-col md:flex-row justify-between items-center gap-4 md:gap-0 bg-footer-bg">
            <div class="space-x-2">
                <a href="#" class="hover:opacity-80 transition-opacity duration-300">{{ __('footer.terms_of_service') }}</a>
                <span>|</span>
                <a href="#" class="hover:opacity-80 transition-opacity duration-300">{{ __('footer.privacy_policy') }}</a>
            </div>
            <div class="flex items-center gap-3 bg-transparent border border-white/40 rounded-lg px-3 py-1 shadow-sm select-none">
                <span class="text-xs font-semibold">{{ __('footer.system_name') }}</span>
            </div>
        </footer>
    @endif
@endauth
@guest
    <footer class="text-center text-xs text-footer-text py-6 px-6 md:px-12 flex flex-col md:flex-row justify-between items-center gap-4 md:gap-0 bg-footer-bg">
        <div class="space-x-2">
            <a href="#" class="hover:opacity-80 transition-opacity duration-300">{{ __('footer.terms_of_service') }}</a>
            <span>|</span>
            <a href="#" class="hover:opacity-80 transition-opacity duration-300">{{ __('footer.privacy_policy') }}</a>
        </div>
        <div class="flex items-center gap-3 bg-transparent border border-white/40 rounded-lg px-3 py-1 shadow-sm select-none">
            <span class="text-xs font-semibold">{{ __('footer.system_name') }}</span>
        </div>
    </footer>
@endguest