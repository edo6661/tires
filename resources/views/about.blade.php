<x-layouts.app>
    <div class="flex flex-col md:flex-row gap-6 container">
        <x-layouts.sidebar :business-settings="$businessSettings" />

        <main class="flex-1 bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="wrapper space-y-8">
                <h1 class="text-title-lg font-bold text-brand pb-4 border-b border-disabled/50">
                    {{ __('about.title') }}
                </h1>

                <p class="text-body-md text-main-text leading-relaxed">
                    {{ __('about.mission_statement') }}
                </p>

                <dl class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                        <dt class="md:col-span-1 font-semibold text-main-text">{{ __('about.company_name_label') }}
                        </dt>
                        <dd class="md:col-span-3 text-main-text/90">{{ __('about.company_name_value') }}</dd>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                        <dt class="md:col-span-1 font-semibold text-main-text">{{ __('about.address_label') }}</dt>
                        <dd class="md:col-span-3 text-main-text/90">{{ __('about.address_value') }}</dd>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                        <dt class="md:col-span-1 font-semibold text-main-text">
                            {{ __('about.representative_label') }}</dt>
                        <dd class="md:col-span-3 text-main-text/90">{{ __('about.representative_value') }}</dd>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                        <dt class="md:col-span-1 font-semibold text-main-text">{{ __('about.phone_label') }}</dt>
                        <dd class="md:col-span-3 text-main-text/90">{{ __('about.phone_value') }}</dd>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                        <dt class="md:col-span-1 font-semibold text-main-text">{{ __('about.fax_label') }}</dt>
                        <dd class="md:col-span-3 text-main-text/90">{{ __('about.fax_value') }}</dd>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                        <dt class="md:col-span-1 font-semibold text-main-text">
                            {{ __('about.business_hours_label') }}</dt>
                        <dd class="md:col-span-3 text-main-text/90">{{ __('about.business_hours_value') }}</dd>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                        <dt class="md:col-span-1 font-semibold text-main-text">{{ __('about.holidays_label') }}
                        </dt>
                        <dd class="md:col-span-3 text-main-text/90">{{ __('about.holidays_value') }}</dd>
                    </div>
                </dl>
            </div>
        </main>
    </div>
</x-layouts.app>

