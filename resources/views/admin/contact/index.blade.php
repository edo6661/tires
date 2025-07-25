<x-layouts.app>
    <div class="container space-y-6" x-data="contactIndex()">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-title-lg font-bold text-main-text">Contact Management</h1>
                <p class="text-main-text/70 mt-1 text-body-md">Manage contact messages from customers</p>
            </div>
            <div class="flex items-center gap-3">
                {{-- <a href="{{ route('admin.contact.create') }}" 
                   class="px-4 py-2 bg-main-button text-white rounded-lg hover:bg-btn-main-hover transition-all duration-300 transform hover:scale-105 hover:shadow-lg flex items-center gap-2 text-button-md font-semibold">
                    <i class="fas fa-plus"></i>
                    Add Contact
                </a> --}}
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow-sm p-6 border border-disabled/20 hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-body-md text-main-text/70">Total Contacts</p>
                        <p class="text-title-md font-bold text-main-text">{{ $stats['total'] }}</p>
                    </div>
                    <div class="bg-brand/10 p-3 rounded-full">
                        <i class="fas fa-envelope text-brand"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6 border border-disabled/20 hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-body-md text-main-text/70">Pending</p>
                        <p class="text-title-md font-bold text-yellow-600">{{ $stats['pending'] }}</p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6 border border-disabled/20 hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-body-md text-main-text/70">Replied</p>
                        <p class="text-title-md font-bold text-green-600">{{ $stats['replied'] }}</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-reply text-green-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6 border border-disabled/20 hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-body-md text-main-text/70">Today</p>
                        <p class="text-title-md font-bold text-purple-600">{{ $stats['today'] }}</p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-calendar-day text-purple-600"></i>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg animate-fade-in" x-data="{ show: true }" x-show="show" x-transition>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                    <button @click="show = false" class="text-green-700 hover:text-green-900 transition-colors duration-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg animate-fade-in" x-data="{ show: true }" x-show="show" x-transition>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                    <button @click="show = false" class="text-red-700 hover:text-red-900 transition-colors duration-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm p-6 border border-disabled/20">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-heading-lg font-semibold text-main-text">Filter & Search</h3>
                <button @click="showFilters = !showFilters" class="text-link hover:text-link-hover transition-colors duration-200 flex items-center gap-2">
                    <i class="fas fa-filter"></i>
                    <span x-text="showFilters ? 'Hide Filters' : 'Show Filters'" class="text-body-md"></span>
                </button>
            </div>
            <div x-show="showFilters"
                 x-cloak 
                 x-transition:enter="transition-all ease-out duration-300"
                 x-transition:enter-start="opacity-0 max-h-0"
                 x-transition:enter-end="opacity-100 max-h-96"
                 x-transition:leave="transition-all ease-in duration-200"
                 x-transition:leave-start="opacity-100 max-h-96"
                 x-transition:leave-end="opacity-0 max-h-0"
                 class="space-y-4 overflow-hidden">
                <form method="GET" action="{{ route('admin.contact.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-body-md font-medium text-main-text mb-1">Status</label>
                        <select name="status" class="w-full border border-disabled rounded-lg px-3 py-2 text-body-md focus:ring-2 focus:ring-brand focus:border-brand transition-all duration-200">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="replied" {{ request('status') == 'replied' ? 'selected' : '' }}>Replied</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-body-md font-medium text-main-text mb-1">Start Date</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" 
                               class="w-full border border-disabled rounded-lg px-3 py-2 text-body-md focus:ring-2 focus:ring-brand focus:border-brand transition-all duration-200">
                    </div>
                    <div>
                        <label class="block text-body-md font-medium text-main-text mb-1">End Date</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" 
                               class="w-full border border-disabled rounded-lg px-3 py-2 text-body-md focus:ring-2 focus:ring-brand focus:border-brand transition-all duration-200">
                    </div>
                    <div class="lg:col-span-1">
                        <label class="block text-body-md font-medium text-main-text mb-1">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email, subject..."
                               class="w-full border border-disabled rounded-lg px-3 py-2 text-body-md focus:ring-2 focus:ring-brand focus:border-brand transition-all duration-200">
                    </div>
                    <div class="lg:col-span-4 flex gap-2">
                        <button type="submit" 
                            class="px-4 py-2 bg-brand text-white rounded-lg hover:bg-brand/90 transition-all duration-200 transform hover:scale-105 flex items-center gap-2 text-button-md font-semibold">
                            <i class="fas fa-search"></i>
                            Filter
                        </button>
                        <a href="{{ route('admin.contact.index') }}" 
                           class="px-4 py-2 bg-secondary-button text-main-text rounded-lg hover:bg-secondary-button/80 transition-all duration-200 transform hover:scale-105 text-button-md">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="selectedItems.length > 0"
             x-cloak 
             x-transition:enter="transition-all ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition-all ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="bg-sub border border-brand/20 rounded-lg p-4 flex items-center justify-between">
            <span class="text-brand text-body-md font-medium">
                <span x-text="selectedItems.length"></span> items selected
            </span>
            <div class="flex gap-2">
                <button @click="deleteSelected()" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition-all duration-200 transform hover:scale-105 text-body-md">
                    <i class="fas fa-trash mr-1"></i>
                    Delete
                </button>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-disabled/20">
            <div class="p-6 border-b border-disabled/20">
                <div class="flex items-center justify-between">
                    <h3 class="text-heading-lg font-semibold text-main-text">Contact List</h3>
                </div>
            </div>
            @if($contacts->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-sub/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-body-md font-medium text-main-text/70 uppercase tracking-wider">
                                <input type="checkbox" 
                                    @change="toggleSelectAll($event.target.checked)" 
                                    :checked="allItemIds.length > 0 && selectedItems.length === allItemIds.length"
                                    class="rounded border-disabled text-brand focus:ring-brand">
                                </th>
                                <th class="px-6 py-3 text-left text-body-md font-medium text-main-text/70 uppercase tracking-wider">Sender</th>
                                <th class="px-6 py-3 text-left text-body-md font-medium text-main-text/70 uppercase tracking-wider">Subject</th>
                                <th class="px-6 py-3 text-left text-body-md font-medium text-main-text/70 uppercase tracking-wider">Message</th>
                                <th class="px-6 py-3 text-left text-body-md font-medium text-main-text/70 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-body-md font-medium text-main-text/70 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-body-md font-medium text-main-text/70 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-disabled/20">
                            @foreach($contacts as $contact)
                                <tr class="hover:bg-sub/30 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" value="{{ $contact->id }}" 
                                               @change="toggleSelect({{ $contact->id }}, $event.target.checked)"
                                               class="rounded border-disabled text-brand focus:ring-brand">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-brand/10 flex items-center justify-center">
                                                    <i class="fas fa-user text-brand"></i>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-body-md font-medium text-main-text">{{ $contact->full_name }}</div>
                                                <div class="text-body-md text-main-text/70">{{ $contact->email }}</div>
                                                @if($contact->phone_number)
                                                    <div class="text-body-md text-main-text/50">{{ $contact->phone_number }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-body-md text-main-text font-medium max-w-xs">
                                            <div class="truncate" title="{{ $contact->subject }}">
                                                {{ $contact->subject }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-body-md text-main-text/70 max-w-xs">
                                            <div class="truncate" title="{{ $contact->message }}">
                                                {{ Str::limit($contact->message, 50) }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-body-md text-main-text/70">
                                        <div class="space-y-1">
                                            <div>{{ $contact->created_at->format('d/m/Y') }}</div>
                                            <div class="text-body-md">{{ $contact->created_at->format('H:i') }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($contact->status === \App\Enums\ContactStatus::PENDING)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-body-md font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-clock text-yellow-500 mr-1 text-xs"></i>
                                                Pending
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-body-md font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check text-green-500 mr-1 text-xs"></i>
                                                Replied
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-body-md font-medium">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('admin.contact.show', $contact->id) }}" 
                                               class="text-brand hover:text-brand/80 transition-all duration-200 transform hover:scale-110" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            {{-- <a href="{{ route('admin.contact.edit', $contact->id) }}" 
                                               class="text-yellow-600 hover:text-yellow-700 transition-all duration-200 transform hover:scale-110" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a> --}}
                                            @if($contact->status === \App\Enums\ContactStatus::PENDING)
                                                <button @click="quickReply({{ $contact->id }})" 
                                                        class="text-green-600 hover:text-green-700 transition-all duration-200 transform hover:scale-110" title="Quick Reply">
                                                    <i class="fas fa-reply"></i>
                                                </button>
                                            @endif
                                            <button @click="deleteSingle({{ $contact->id }})" 
                                                    class="text-red-600 hover:text-red-700 transition-all duration-200 transform hover:scale-110" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-disabled/20">
                    {{ $contacts->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-inbox text-disabled text-6xl mb-4"></i>
                    <h3 class="text-heading-lg font-medium text-main-text mb-2">No contacts</h3>
                    <p class="text-main-text/70 mb-4 text-body-md">No contact messages have been received or match the applied filters.</p>
                    <a href="{{ route('admin.contact.create') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-brand text-white rounded-lg hover:bg-brand/90 transition-all duration-300 transform hover:scale-105 hover:shadow-lg text-button-md font-semibold">
                        <i class="fas fa-plus"></i>
                        Add Contact
                    </a>
                </div>
            @endif
        </div>

        <div x-show="showReplyModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             x-cloak
             class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                <div class="mt-3">
                    <h3 class="text-heading-lg font-medium text-main-text text-center">Quick Reply</h3>
                    <div class="mt-4">
                        <textarea x-model="replyMessage" rows="4" 
                                  class="w-full border border-disabled rounded-lg px-3 py-2 text-body-md focus:ring-2 focus:ring-brand focus:border-brand transition-all duration-200"
                                  placeholder="Write your reply..."></textarea>
                    </div>
                    <div class="items-center px-4 py-3 flex gap-2 justify-center mt-4">
                        <button @click="showReplyModal = false" 
                                class="px-4 py-2 bg-secondary-button text-main-text text-button-md font-medium rounded-md shadow-sm hover:bg-secondary-button/80 transition-all duration-200">
                            Cancel
                        </button>
                        <button @click="submitQuickReply()" 
                                class="px-4 py-2 bg-brand text-white text-button-md font-medium rounded-md shadow-sm hover:bg-brand/90 transition-all duration-200">
                            Send Reply
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="showDeleteModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             x-cloak
             class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                <div class="mt-3 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                    <h3 class="text-heading-lg font-medium text-main-text mt-2">Confirm Delete</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-body-md text-main-text/70" x-text="deleteMessage"></p>
                    </div>
                    <div class="items-center px-4 py-3 flex gap-2 justify-center">
                        <button @click="showDeleteModal = false" 
                                class="px-4 py-2 bg-secondary-button text-main-text text-button-md font-medium rounded-md shadow-sm hover:bg-secondary-button/80 transition-all duration-200">
                            Cancel
                        </button>
                        <button @click="confirmDelete()" 
                                class="px-4 py-2 bg-red-600 text-white text-button-md font-medium rounded-md shadow-sm hover:bg-red-700 transition-all duration-200">
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
                allItemIds: [],
                deleteTarget: null,
                deleteMessage: '',
                replyTarget: null,
                replyMessage: '',
                init() {
                    this.allItemIds = Array.from(document.querySelectorAll('tbody input[type="checkbox"][value]')).map(cb => parseInt(cb.value));
                },
                toggleSelectAll(checked) {
                    const checkboxes = document.querySelectorAll('tbody input[type="checkbox"][value]');
                    if (checked) {
                        this.selectedItems = [...this.allItemIds];
                    } else {
                        this.selectedItems = [];
                    }
                    checkboxes.forEach(checkbox => checkbox.checked = checked);
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