<x-layouts.app>
<div class="container">
    <div class="flex flex-col items-center justify-center min-h-[60vh] text-center space-y-6">
        <div class="text-brand mb-4">
            <i class="fas fa-exclamation-triangle text-8xl opacity-50" aria-hidden="true"></i>
        </div>
        
        <h1 class="text-6xl font-bold text-brand mb-4">404</h1>
        
        <h2 class="text-title-lg font-semibold text-main-text mb-4">
            {{ __('404.title') }}
        </h2>
        
        <p class="text-body-lg text-main-text/80 max-w-md mb-8">
            {{ __('404.description') }}
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 items-center">
            <a href="{{ route('home') }}" 
                class="inline-block rounded-full bg-main-button hover:bg-btn-main-hover text-footer-text text-button-lg font-semibold px-8 py-3 transition-colors duration-300">
                {{ __('404.back_to_home') }}
            </a>
            
            <button onclick="history.back()" 
                    class="inline-block rounded-full bg-secondary-button hover:bg-disabled text-main-text text-button-lg font-semibold px-8 py-3 transition-colors duration-300">
                {{ __('404.go_back') }}
            </button>
        </div>
        
        <div class="mt-12 p-6 bg-sub rounded-lg max-w-lg">
            <h3 class="text-heading-md font-semibold text-brand mb-3">
                {{ __('404.help_title') }}
            </h3>
            <ul class="text-body-md text-main-text/80 space-y-2 text-left">
                <li>• {{ __('404.help_check_url') }}</li>
                <li>• {{ __('404.help_use_navigation') }}</li>
                <li>• {{ __('404.help_contact_support') }}</li>
            </ul>
            
            @if(isset($businessSettings) && $businessSettings->phone_number)
                <div class="mt-4 pt-4 border-t border-disabled/30">
                    <p class="text-body-md text-main-text">
                        {{ __('404.contact_us') }}: 
                        <a href="tel:{{ $businessSettings->phone_number }}" 
                            class="text-link hover:text-link-hover font-medium">
                            {{ $businessSettings->phone_number }}
                        </a>
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
</x-layouts.app>