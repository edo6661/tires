{{-- resources/views/components/shared/features/reservation/calendar/status-badge.blade.php --}}
@props(['status', 'size' => 'sm'])

<span class="px-2 py-1 rounded-full font-medium
    {{ $size === 'xs' ? 'text-xs' : '' }}
    {{ $size === 'sm' ? 'text-xs' : '' }}
    {{ $size === 'md' ? 'text-sm' : '' }}
    {{ $status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
    {{ $status === 'confirmed' ? 'bg-green-100 text-green-800' : '' }}
    {{ $status === 'completed' ? 'bg-blue-100 text-blue-800' : '' }}
    {{ $status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
">
    {{ $status->label() }}
</span>