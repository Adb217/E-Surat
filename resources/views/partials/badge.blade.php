@php
    $cls = match ($status) {
        'Final'      => 'bg-orange-50 text-orange-700 ring-orange-200',
        'Dibatalkan' => 'bg-red-50 text-red-600 ring-red-200',
        default      => 'bg-gray-100 text-gray-600 ring-gray-200',
    };
@endphp
<span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 {{ $cls }}">{{ $status }}</span>