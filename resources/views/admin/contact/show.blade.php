<!-- resources/views/admin/contact/show.blade.php -->
<x-layouts.app>
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Contact Details</h1>
                <p class="text-gray-600 mt-1">View and update contact message</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.contact.index') }}" 
                   class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200 flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    Back to List
                </a>
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
        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                <div class="flex items-start gap-2">
                    <i class="fas fa-exclamation-circle mt-1"></i>
                    <div>
                        <strong>Terjadi kesalahan:</strong>
                        <ul class="mt-1 ml-4 list-disc">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-lg shadow-sm border">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">Contact Information</h3>
                            <div class="flex items-center gap-2">
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
                            </div>
                        </div>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 h-12 w-12">
                                <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                                    <i class="fas fa-user text-blue-600 text-lg"></i>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-lg font-medium text-gray-900">{{ $contact->full_name }}</h4>
                                <div class="space-y-1 mt-1">
                                    <div class="flex items-center gap-2 text-gray-600">
                                        <i class="fas fa-envelope text-sm"></i>
                                        <span>{{ $contact->email }}</span>
                                    </div>
                                    @if($contact->phone_number)
                                        <div class="flex items-center gap-2 text-gray-600">
                                            <i class="fas fa-phone text-sm"></i>
                                            <span>{{ $contact->phone_number }}</span>
                                        </div>
                                    @endif
                                    <div class="flex items-center gap-2 text-gray-500 text-sm">
                                        <i class="fas fa-calendar text-sm"></i>
                                        <span>{{ $contact->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="border-t pt-4">
                            <h5 class="text-sm font-medium text-gray-700 mb-2">Subject</h5>
                            <p class="text-gray-900">{{ $contact->subject }}</p>
                        </div>
                        <div class="border-t pt-4">
                            <h5 class="text-sm font-medium text-gray-700 mb-2">Message</h5>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-gray-800 whitespace-pre-wrap">{{ $contact->message }}</p>
                            </div>
                        </div>
                        @if($contact->admin_reply)
                            <div class="border-t pt-4">
                                <h5 class="text-sm font-medium text-gray-700 mb-2">Admin Reply</h5>
                                <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                                    <p class="text-gray-800 whitespace-pre-wrap">{{ $contact->admin_reply }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="lg:col-span-1 flex flex-col gap-4">
                <div class="bg-white rounded-lg shadow-sm border sticky top-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Update Contact</h3>
                    </div>
                    <form method="POST" action="{{ route('admin.contact.update', $contact->id) }}" class="p-6 space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Status
                            </label>
                            <select name="status" id="status" 
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="pending" {{ $contact->status === \App\Enums\ContactStatus::PENDING ? 'selected' : '' }}>
                                    Pending
                                </option>
                                <option value="replied" {{ $contact->status === \App\Enums\ContactStatus::REPLIED ? 'selected' : '' }}>
                                    Replied
                                </option>
                            </select>
                            @error('status')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="admin_reply" class="block text-sm font-medium text-gray-700 mb-2">
                                Admin Reply
                            </label>
                            <textarea name="admin_reply" id="admin_reply" rows="6" 
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                      placeholder="Write your reply to the customer...">{{ old('admin_reply', $contact->admin_reply) }}</textarea>
                            @error('admin_reply')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-gray-500 text-xs mt-1">Maximum 2000 characters</p>
                        </div>
                        <div class="pt-4 border-t">
                            <button type="submit" 
                                    class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center justify-center gap-2">
                                <i class="fas fa-save"></i>
                                Update Contact
                            </button>
                        </div>
                    </form>
                </div>
                <div class="bg-white rounded-lg shadow-sm border mt-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                    </div>
                    <div class="p-6 flex flex-col gap-4">
                        @if($contact->status === \App\Enums\ContactStatus::PENDING)
                            <form method="POST" action="{{ route('admin.contact.update', $contact->id) }}" class="inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="replied">
                                <input type="hidden" name="admin_reply" value="Thank you for contacting us. We have received your message and will get back to you soon.">
                                <button type="submit" 
                                        class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 flex items-center justify-center gap-2">
                                    <i class="fas fa-reply"></i>
                                    Mark as Replied 
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.contact.update', $contact->id) }}" class="inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="pending">
                                <button type="submit" 
                                        class="w-full px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors duration-200 flex items-center justify-center gap-2">
                                    <i class="fas fa-clock"></i>
                                    Mark as Pending
                                </button>
                            </form>
                        @endif
                        <button onclick="deleteContact()" 
                                class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 flex items-center justify-center gap-2">
                            <i class="fas fa-trash"></i>
                            Delete Contact
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-2">Confirm Delete</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">Are you sure you want to delete this contact? This action cannot be undone.</p>
                </div>
                <div class="items-center px-4 py-3 flex gap-2 justify-center">
                    <button onclick="closeDeleteModal()" 
                            class="px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-300">
                        Cancel
                    </button>
                    <button onclick="confirmDelete()" 
                            class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script>
        function deleteContact() {
            document.getElementById('deleteModal').classList.remove('hidden');
        }
        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }
        async function confirmDelete() {
            try {
                const response = await fetch(`/admin/contact/{{ $contact->id }}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                const result = await response.json();
                if (result.success) {
                    window.location.href = '{{ route("admin.contact.index") }}';
                } else {
                    alert(result.message || 'An error occurred while deleting');
                    closeDeleteModal();
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while deleting');
                closeDeleteModal();
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            const textarea = document.getElementById('admin_reply');
            if (textarea) {
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = (this.scrollHeight) + 'px';
                });
                textarea.style.height = 'auto';
                textarea.style.height = (textarea.scrollHeight) + 'px';
            }
        });
    </script>
</x-layouts.app>