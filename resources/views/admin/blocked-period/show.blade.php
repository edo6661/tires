<x-layouts.app>
    <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8" x-data="{ loaded: false }" x-init="setTimeout(() => loaded = true, 100)">
        <div class="flex justify-between items-center mb-6" 
             x-show="loaded"
             x-transition:enter="transition-all ease-out duration-500"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0">
            <div>
                <h1 class="text-title-lg font-bold text-main-text">Blocked Period Details</h1>
                <p class="text-body-md text-main-text/70 mt-1">Displaying the details of the selected blocked period.</p>
            </div>
            <a href="{{ route('admin.blocked-period.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-secondary-button border border-transparent rounded-md font-semibold text-button-md text-main-text uppercase tracking-widest hover:bg-secondary-button/80 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-brand transition-all duration-300 transform ">
                <i class="fas fa-arrow-left mr-2"></i>
                Back
            </a>
        </div>

        <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-disabled/20"
             x-show="loaded"
             x-transition:enter="transition-all ease-out duration-700 delay-200"
             x-transition:enter-start="opacity-0 translate-y-8"
             x-transition:enter-end="opacity-100 translate-y-0">
            <div class="px-6 py-5">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
                    <div class="md:col-span-2 transform transition-all duration-300 hover:scale-[1.01]">
                        <dt class="text-body-md font-medium text-main-text/70 flex items-center">
                            <i class="fas fa-utensils text-brand mr-2"></i>
                            Blocked Menu
                        </dt>
                        <dd class="mt-1 text-heading-lg text-main-text font-semibold flex items-center">
                            @if($blockedPeriod->all_menus)
                                <span class="px-3 py-1 text-body-md font-bold text-white bg-red-600 rounded-full flex items-center shadow-sm hover:shadow-md transition-shadow duration-300">
                                    <i class="fas fa-globe-asia mr-2"></i> All Menus
                                </span>
                            @elseif($blockedPeriod->menu)
                                <span class="w-4 h-4 rounded-full mr-3 shadow-sm" style="background-color: {{ $blockedPeriod->menu->color ?? '#6B7280' }};"></span>
                                {{ $blockedPeriod->menu->name }}
                            @else
                                <span class="text-main-text/50 italic">Menu not available</span>
                            @endif
                        </dd>
                    </div>

                    <div class="transform transition-all duration-300 hover:scale-[1.02] hover:bg-sub/30 rounded-lg p-3 -m-3">
                        <dt class="text-body-md font-medium text-main-text/70 flex items-center">
                            <i class="far fa-clock text-brand mr-2"></i> Start Time
                        </dt>
                        <dd class="mt-1 text-body-lg text-main-text font-medium">
                            {{ $blockedPeriod->start_datetime->format('F d, Y, H:i') }}
                        </dd>
                    </div>

                    <div class="transform transition-all duration-300 hover:scale-[1.02] hover:bg-sub/30 rounded-lg p-3 -m-3">
                        <dt class="text-body-md font-medium text-main-text/70 flex items-center">
                            <i class="far fa-check-circle text-brand mr-2"></i> End Time
                        </dt>
                        <dd class="mt-1 text-body-lg text-main-text font-medium">
                            {{ $blockedPeriod->end_datetime->format('F d, Y, H:i') }}
                        </dd>
                    </div>

                    <div class="transform transition-all duration-300 hover:scale-[1.02] hover:bg-sub/30 rounded-lg p-3 -m-3">
                        <dt class="text-body-md font-medium text-main-text/70 flex items-center">
                            <i class="fas fa-hourglass-half text-brand mr-2"></i> Duration
                        </dt>
                        <dd class="mt-1 text-body-lg text-main-text font-medium">
                            {{ $blockedPeriod->getDurationText() }}
                        </dd>
                    </div>

                    <div class="transform transition-all duration-300 hover:scale-[1.02] hover:bg-sub/30 rounded-lg p-3 -m-3">
                        <dt class="text-body-md font-medium text-main-text/70 flex items-center">
                            <i class="fas fa-circle-info text-brand mr-2"></i> Status
                        </dt>
                        <dd class="mt-1">
                            @php
                                $now = now();
                                if ($blockedPeriod->isActive()) {
                                    $statusClass = 'bg-green-100 text-green-800';
                                    $statusText = 'Active';
                                    $statusIcon = 'fas fa-circle text-green-500';
                                } elseif ($blockedPeriod->start_datetime->isFuture()) {
                                    $statusClass = 'bg-yellow-100 text-yellow-800';
                                    $statusText = 'Upcoming';
                                    $statusIcon = 'fas fa-clock text-yellow-500';
                                } else {
                                    $statusClass = 'bg-disabled/30 text-main-text/70';
                                    $statusText = 'Completed';
                                    $statusIcon = 'fas fa-history text-main-text/50';
                                }
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 text-body-md font-semibold rounded-full {{ $statusClass }} shadow-sm hover:shadow-md transition-all duration-300 transform ">
                                <i class="{{ $statusIcon }} mr-1 text-xs"></i>
                                {{ $statusText }}
                            </span>
                        </dd>
                    </div>

                    <div class="md:col-span-2 transform transition-all duration-300 hover:scale-[1.01]">
                        <dt class="text-body-md font-medium text-main-text/70 flex items-center">
                            <i class="fas fa-comment-alt text-brand mr-2"></i> Reason
                        </dt>
                        <dd class="mt-1 text-body-lg text-main-text prose max-w-none bg-sub/20 rounded-lg p-4 hover:bg-sub/30 transition-colors duration-300">
                            {!! nl2br(e($blockedPeriod->reason)) !!}
                        </dd>
                    </div>
                </dl>
            </div>

            <div class="bg-sub/50 px-6 py-4 flex justify-between items-center border-t border-disabled/20">
                <div class="text-body-md text-main-text/70">
                    <span class="flex items-center gap-1">
                        <i class="fas fa-plus-circle text-brand text-xs"></i>
                        Created: {{ $blockedPeriod->created_at->diffForHumans() }}
                    </span>
                    <span class="flex items-center gap-1 mt-1">
                        <i class="fas fa-edit text-brand text-xs"></i>
                        Updated: {{ $blockedPeriod->updated_at->diffForHumans() }}
                    </span>
                </div>
                
                <div class="flex items-center space-x-3" x-data="{ showConfirm: false }">
                    <a href="{{ route('admin.blocked-period.edit', $blockedPeriod->id) }}" 
                       class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-button-md text-white uppercase tracking-widest hover:bg-yellow-600 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-yellow-300 transition-all duration-300 transform ">
                        <i class="fas fa-edit mr-2"></i>
                        Edit
                    </a>
                    
                    <button @click="showConfirm = true" 
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-button-md text-white uppercase tracking-widest hover:bg-red-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-red-300 transition-all duration-300 transform ">
                        <i class="fas fa-trash-alt mr-2"></i>
                        Delete
                    </button>

                    <div x-show="showConfirm" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" 
                         style="display: none;">
                        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md border border-disabled/20" 
                             @click.away="showConfirm = false"
                             x-transition:enter="transition ease-out duration-300 transform"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-200 transform"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95">
                            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                            </div>
                            <h3 class="text-heading-lg font-bold text-main-text text-center">Confirm Deletion</h3>
                            <p class="mt-2 text-body-md text-main-text/70 text-center">Are you sure you want to delete this blocked period? This action cannot be undone.</p>
                            <div class="mt-6 flex justify-center space-x-3">
                                <button @click="showConfirm = false" 
                                        class="px-4 py-2 bg-secondary-button text-main-text text-button-md font-medium rounded-md hover:bg-secondary-button/80 transition-all duration-200 transform ">
                                    Cancel
                                </button>
                                <form action="{{ route('admin.blocked-period.destroy', $blockedPeriod->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="px-4 py-2 bg-red-600 text-white text-button-md font-medium rounded-md hover:bg-red-700 hover:shadow-lg transition-all duration-200 transform ">
                                        Yes, Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>