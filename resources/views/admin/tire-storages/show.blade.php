<x-layouts.app>
    <div class="max-w-7xl mx-auto space-y-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <a href="{{ route('admin.tire-storage.index') }}"
                       class="text-gray-500 hover:text-gray-700 transition-colors duration-200">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">Tire Storage Details</h1>
                </div>
                <p class="text-gray-600">Complete details of the customer's tire storage.</p>
            </div>
            <div class="flex items-center gap-3">
                @if($tireStorage->status->value === 'active')
                    <button onclick="endStorage({{ $tireStorage->id }})"
                            class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors duration-200 flex items-center gap-2">
                        <i class="fas fa-stop"></i>
                        End Storage
                    </button>
                @endif
                <a href="{{ route('admin.tire-storage.edit', $tireStorage->id) }}"
                   class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors duration-200 flex items-center gap-2">
                    <i class="fas fa-edit"></i>
                    Edit
                </a>
                @if($tireStorage->status->value === 'ended')
                    <button onclick="deleteStorage({{ $tireStorage->id }})"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 flex items-center gap-2">
                        <i class="fas fa-trash"></i>
                        Delete
                    </button>
                @endif
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

        <div class="flex justify-center">
            @if($tireStorage->status->value === 'active')
                <span class="inline-flex items-center px-6 py-3 rounded-full text-lg font-medium bg-green-100 text-green-800">
                    <i class="fas fa-play-circle text-green-500 mr-2"></i>
                    {{ $tireStorage->status->label() }}
                </span>
            @else
                <span class="inline-flex items-center px-6 py-3 rounded-full text-lg font-medium bg-gray-100 text-gray-800">
                    <i class="fas fa-stop-circle text-gray-500 mr-2"></i>
                    {{ $tireStorage->status->label() }}
                </span>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-user text-blue-600"></i>
                        Customer Information
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center">
                                <i class="fas fa-user text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <div class="text-lg font-medium text-gray-900">{{ $tireStorage->user->full_name }}</div>
                                <div class="text-sm text-gray-500">{{ $tireStorage->user->email }}</div>
                            </div>
                        </div>
                        <div class="border-t pt-4 space-y-3">
                            @if($tireStorage->user->phone)
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-phone text-gray-400 w-5"></i>
                                    <span class="text-sm text-gray-700">{{ $tireStorage->user->phone }}</span>
                                </div>
                            @endif
                            @if($tireStorage->user->address)
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-map-marker-alt text-gray-400 w-5 mt-0.5"></i>
                                    <span class="text-sm text-gray-700">{{ $tireStorage->user->address }}</span>
                                </div>
                            @endif
                            <div class="flex items-center gap-3">
                                <i class="fas fa-calendar text-gray-400 w-5"></i>
                                <span class="text-sm text-gray-700">
                                    Joined on {{ $tireStorage->user->created_at->format('d M Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center gap-2">
                        <i class="fas fa-archive text-blue-600"></i>
                        Storage Details
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <h4 class="font-medium text-gray-900 border-b pb-2">Tire Information</h4>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tire Brand</label>
                                    <div class="mt-1 p-3 bg-gray-50 rounded-lg">
                                        <span class="text-sm font-medium text-gray-900">{{ $tireStorage->tire_brand }}</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tire Size</label>
                                    <div class="mt-1 p-3 bg-gray-50 rounded-lg">
                                        <span class="text-sm font-medium text-gray-900">{{ $tireStorage->tire_size }}</span>
                                    </div>
                                </div>
                                @if($tireStorage->tire_type)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Tire Type</label>
                                        <div class="mt-1 p-3 bg-gray-50 rounded-lg">
                                            <span class="text-sm font-medium text-gray-900">{{ $tireStorage->tire_type }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="space-y-4">
                            <h4 class="font-medium text-gray-900 border-b pb-2">Storage Information</h4>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Start Date</label>
                                    <div class="mt-1 p-3 bg-gray-50 rounded-lg">
                                        <span class="text-sm font-medium text-gray-900">
                                            {{ $tireStorage->storage_start_date->format('d F Y') }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Planned End Date</label>
                                    <div class="mt-1 p-3 bg-gray-50 rounded-lg">
                                        <span class="text-sm font-medium text-gray-900">
                                            {{ $tireStorage->planned_end_date->format('d F Y') }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Storage Fee</label>
                                    <div class="mt-1 p-3 bg-purple-50 rounded-lg">
                                        <span class="text-lg font-bold text-purple-600">
                                            Rp {{ number_format($tireStorage->storage_fee, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($tireStorage->description)
                        <div class="mt-6 pt-6 border-t">
                            <h4 class="font-medium text-gray-900 mb-3">Notes</h4>
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $tireStorage->description }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="mt-6 pt-6 border-t">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="text-center p-4 bg-blue-50 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600">
                                    {{ $tireStorage->storage_start_date->diffInDays($tireStorage->planned_end_date) }}
                                </div>
                                <div class="text-sm text-blue-700">Duration (Days)</div>
                            </div>
                            <div class="text-center p-4 bg-green-50 rounded-lg">
                                <div class="text-2xl font-bold text-green-600">
                                    {{ $tireStorage->storage_start_date->diffInDays(now()) }}
                                </div>
                                <div class="text-sm text-green-700">Days Passed</div>
                            </div>
                            <div class="text-center p-4 bg-orange-50 rounded-lg">
                                <div class="text-2xl font-bold text-orange-600">
                                    @if($tireStorage->status->value === 'active')
                                        {{ now()->diffInDays($tireStorage->planned_end_date) }}
                                    @else
                                        0
                                    @endif
                                </div>
                                <div class="text-sm text-orange-700">Days Remaining</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center gap-2">
                <i class="fas fa-history text-blue-600"></i>
                Storage Timeline
            </h3>
            <div class="relative">
                <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                
                <div class="relative flex items-start mb-6">
                    <div class="absolute left-2 w-4 h-4 bg-blue-600 rounded-full border-2 border-white"></div>
                    <div class="ml-10">
                        <div class="flex items-center gap-2">
                            <span class="font-medium text-gray-900">Storage Created</span>
                            <span class="text-sm text-gray-500">
                                {{ $tireStorage->created_at->format('d M Y, H:i') }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">
                            Tire storage for {{ $tireStorage->user->full_name }} has been created.
                        </p>
                    </div>
                </div>

                <div class="relative flex items-start mb-6">
                    <div class="absolute left-2 w-4 h-4 bg-green-600 rounded-full border-2 border-white"></div>
                    <div class="ml-10">
                        <div class="flex items-center gap-2">
                            <span class="font-medium text-gray-900">Storage Started</span>
                            <span class="text-sm text-gray-500">
                                {{ $tireStorage->storage_start_date->format('d M Y') }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">
                            Storage for tire {{ $tireStorage->tire_brand }} size {{ $tireStorage->tire_size }} has begun.
                        </p>
                    </div>
                </div>

                @if($tireStorage->status->value === 'ended')
                    <div class="relative flex items-start">
                        <div class="absolute left-2 w-4 h-4 bg-gray-600 rounded-full border-2 border-white"></div>
                        <div class="ml-10">
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-gray-900">Storage Ended</span>
                                <span class="text-sm text-gray-500">
                                    {{ $tireStorage->planned_end_date->format('d M Y') }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">
                                The tire storage period has ended.
                            </p>
                        </div>
                    </div>
                @else
                    <div class="relative flex items-start">
                        <div class="absolute left-2 w-4 h-4 bg-orange-400 rounded-full border-2 border-white"></div>
                        <div class="ml-10">
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-gray-900">Planned End Date</span>
                                <span class="text-sm text-gray-500">
                                    {{ $tireStorage->planned_end_date->format('d M Y') }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">
                                The planned end date for the storage period.
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        async function endStorage(id) {
            if (confirm('Are you sure you want to end this tire storage?')) {
                try {
                    const response = await fetch(`/admin/tire-storage/${id}/end`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        }
                    });
                    
                    const result = await response.json();
                    if (result.success) {
                        alert(result.message);
                        window.location.reload();
                    } else {
                        alert(result.message || 'An error occurred while ending the storage.');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('An error occurred while ending the storage.');
                }
            }
        }

        async function deleteStorage(id) {
            if (confirm('Are you sure you want to delete this tire storage? This action cannot be undone.')) {
                try {
                    const response = await fetch(`/admin/tire-storage/bulk-delete`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ ids: [id] })
                    });
                    
                    const result = await response.json();
                    if (result.success) {
                        alert(result.message);
                        window.location.href = '/admin/tire-storage';
                    } else {
                        alert(result.message || 'An error occurred while deleting the storage.');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the storage.');
                }
            }
        }
    </script>
</x-layouts.app>