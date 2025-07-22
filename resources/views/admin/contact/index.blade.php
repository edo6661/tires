<!-- resources/views/admin/contact/index.blade.php -->
<x-layouts.app>
    <div class="max-w-7xl mx-auto space-y-6" x-data="contactIndex()">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Contact Management</h1>
                <p class="text-gray-600 mt-1">Manage contact messages from customers</p>
            </div>
            <div class="flex items-center gap-3">
                {{-- <a href="{{ route('admin.contact.create') }}" 
                   class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    Add Contact
                </a> --}}
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Contacts</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-envelope text-blue-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Pending</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Replied</p>
                        <p class="text-2xl font-bold text-green-600">{{ $stats['replied'] }}</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-reply text-green-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Today</p>
                        <p class="text-2xl font-bold text-purple-600">{{ $stats['today'] }}</p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-calendar-day text-purple-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rest of your template remains the same... -->
        <!-- Alert Messages -->
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
        <!-- Filters Section -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Filter & Search</h3>
                <button @click="showFilters = !showFilters" class="text-blue-600 hover:text-blue-800">
                    <i class="fas fa-filter"></i>
                    <span x-text="showFilters ? 'Hide Filters' : 'Show Filters'"></span>
                </button>
            </div>
            
            <div x-show="showFilters" x-transition class="space-y-4">
                <form method="GET" action="{{ route('admin.contact.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="replied" {{ request('status') == 'replied' ? 'selected' : '' }}>Replied</option>
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Search -->
                    <div class="lg:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email, subject..."
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Buttons -->
                    <div class="lg:col-span-4 flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center gap-2">
                            <i class="fas fa-search"></i>
                            Filter
                        </button>
                        <a href="{{ route('admin.contact.index') }}" 
                           class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div x-show="selectedItems.length > 0" 
             class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-center justify-between">
            <span class="text-blue-700">
                <span x-text="selectedItems.length"></span> items selected
            </span>
            <div class="flex gap-2">
                <button @click="markAsRepliedSelected()" class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 transition-colors duration-200">
                    <i class="fas fa-reply mr-1"></i>
                    Mark as Replied
                </button>
                <button @click="deleteSelected()" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition-colors duration-200">
                    <i class="fas fa-trash mr-1"></i>
                    Delete
                </button>
            </div>
        </div>

        <!-- Table Section -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Contact List</h3>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" @change="toggleSelectAll($event.target.checked)" 
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label class="text-sm text-gray-700">Select All</label>
                    </div>
                </div>
            </div>

            @if($contacts->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" @change="toggleSelectAll($event.target.checked)" 
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sender</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($contacts as $contact)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" value="{{ $contact->id }}" 
                                               @change="toggleSelect({{ $contact->id }}, $event.target.checked)"
                                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                    <i class="fas fa-user text-blue-600"></i>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $contact->full_name }}</div>
                                                <div class="text-sm text-gray-500">{{ $contact->email }}</div>
                                                @if($contact->phone_number)
                                                    <div class="text-xs text-gray-400">{{ $contact->phone_number }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 font-medium max-w-xs">
                                            <div class="truncate" title="{{ $contact->subject }}">
                                                {{ $contact->subject }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-500 max-w-xs">
                                            <div class="truncate" title="{{ $contact->message }}">
                                                {{ Str::limit($contact->message, 50) }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="space-y-1">
                                            <div>{{ $contact->created_at->format('d/m/Y') }}</div>
                                            <div class="text-xs">{{ $contact->created_at->format('H:i') }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($contact->status === \App\Enums\ContactStatus::PENDING)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-clock text-yellow-500 mr-1 text-xs"></i>
                                                Pending
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check text-green-500 mr-1 text-xs"></i>
                                                Replied
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('admin.contact.show', $contact->id) }}" 
                                               class="text-blue-600 hover:text-blue-900" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            {{-- <a href="{{ route('admin.contact.edit', $contact->id) }}" 
                                               class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a> --}}
                                            @if($contact->status === \App\Enums\ContactStatus::PENDING)
                                                <button @click="quickReply({{ $contact->id }})" 
                                                        class="text-green-600 hover:text-green-900" title="Quick Reply">
                                                    <i class="fas fa-reply"></i>
                                                </button>
                                            @endif
                                            <button @click="deleteSingle({{ $contact->id }})" 
                                                    class="text-red-600 hover:text-red-900" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $contacts->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-inbox text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No contacts</h3>
                    <p class="text-gray-500 mb-4">No contact messages have been received or match the applied filters.</p>
                    <a href="{{ route('admin.contact.create') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        <i class="fas fa-plus"></i>
                        Add Contact
                    </a>
                </div>
            @endif
        </div>

        <!-- Quick Reply Modal -->
        <div x-show="showReplyModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 text-center">Quick Reply</h3>
                    <div class="mt-4">
                        <textarea x-model="replyMessage" rows="4" 
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Write your reply..."></textarea>
                    </div>
                    <div class="items-center px-4 py-3 flex gap-2 justify-center mt-4">
                        <button @click="showReplyModal = false" 
                                class="px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-300">
                            Cancel
                        </button>
                        <button @click="submitQuickReply()" 
                                class="px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-blue-700">
                            Send Reply
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div x-show="showDeleteModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mt-2">Confirm Delete</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500" x-text="deleteMessage"></p>
                    </div>
                    <div class="items-center px-4 py-3 flex gap-2 justify-center">
                        <button @click="showDeleteModal = false" 
                                class="px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-300">
                            Cancel
                        </button>
                        <button @click="confirmDelete()" 
                                class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        function contactIndex() {
            return {
                showFilters: false,
                showDeleteModal: false,
                showReplyModal: false,
                selectedItems: [],
                deleteTarget: null,
                deleteMessage: '',
                replyTarget: null,
                replyMessage: '',

                init() {
                    // Stats are now passed from the backend, no need to calculate here
                },

                toggleSelectAll(checked) {
                    const checkboxes = document.querySelectorAll('input[type="checkbox"][value]');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = checked;
                        const id = parseInt(checkbox.value);
                        if (checked && !this.selectedItems.includes(id)) {
                            this.selectedItems.push(id);
                        } else if (!checked) {
                            const index = this.selectedItems.indexOf(id);
                            if (index > -1) {
                                this.selectedItems.splice(index, 1);
                            }
                        }
                    });
                },

                toggleSelect(id, checked) {
                    if (checked && !this.selectedItems.includes(id)) {
                        this.selectedItems.push(id);
                    } else if (!checked) {
                        const index = this.selectedItems.indexOf(id);
                        if (index > -1) {
                            this.selectedItems.splice(index, 1);
                        }
                    }
                },

                deleteSingle(id) {
                    this.deleteTarget = [id];
                    this.deleteMessage = 'Are you sure you want to delete this contact?';
                    this.showDeleteModal = true;
                },

                deleteSelected() {
                    this.deleteTarget = [...this.selectedItems];
                    this.deleteMessage = `Are you sure you want to delete ${this.selectedItems.length} contacts?`;
                    this.showDeleteModal = true;
                },

                quickReply(id) {
                    this.replyTarget = id;
                    this.replyMessage = '';
                    this.showReplyModal = true;
                },

                async submitQuickReply() {
                    if (!this.replyMessage.trim()) {
                        alert('Reply message cannot be empty');
                        return;
                    }

                    try {
                        const response = await fetch(`/admin/contact/${this.replyTarget}/reply`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                admin_reply: this.replyMessage
                            })
                        });

                        const result = await response.json();

                        if (result.success) {
                            window.location.reload();
                        } else {
                            alert(result.message || 'An error occurred while sending the reply');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('An error occurred while sending the reply');
                    }

                    this.showReplyModal = false;
                },

                async confirmDelete() {
                    try {
                        if (this.deleteTarget.length === 1) {
                            // Single delete
                            const response = await fetch(`/admin/contact/${this.deleteTarget[0]}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            });

                            const result = await response.json();

                            if (result.success || response.ok) {
                                window.location.reload();
                            } else {
                                alert(result.message || 'An error occurred while deleting');
                            }
                        } else {
                            // Bulk delete
                            const response = await fetch('/admin/contact/bulk-delete', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({
                                    ids: this.deleteTarget
                                })
                            });

                            const result = await response.json();

                            if (result.success) {
                                window.location.reload();
                            } else {
                                alert(result.message || 'An error occurred while deleting');
                            }
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('An error occurred while deleting');
                    }

                    this.showDeleteModal = false;
                }
            }
        }
    </script>
</x-layouts.app>