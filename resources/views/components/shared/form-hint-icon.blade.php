@props([
    'label' => '',
    'icon' => '',
    'position' => 'bottom',
    'action' => '#',
    'textColor' => '',
    'hoverColor' => 'hover:text-[#4abaa7]',
])

<form method="POST" action="{{ $action }}" {{ $attributes->merge(['class' => 'inline-block']) }}>
    @csrf

    <button
        type="submit"
        aria-label="{{ $label }}"
        class="relative group {{ $textColor }} {{ $hoverColor }} transition text-xl opacity-30 hover:opacity-100 bg-transparent border-none p-0 cursor-pointer"
    >
        <i class="{{ $icon }}" aria-hidden="true"></i>
        <span class="absolute left-1/2 transform -translate-x-1/2 {{ $position === 'bottom' ? 'top-full mt-1' : 'bottom-full mb-1' }} px-2 py-1 text-xs text-white rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap">
            {{ $label }}
        </span>
    </button>
</form>