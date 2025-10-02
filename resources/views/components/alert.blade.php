@props([
    'type' => 'success', // 'success' atau 'danger'
    'message'
])

@php
    $isSuccess = $type === 'success';
    $isDanger = $type === 'danger';

    $wrapperClasses = \Illuminate\Support\Arr::toCssClasses([
        'fixed top-5 right-5 w-full max-w-sm rounded-lg shadow-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden',
        'bg-green-50' => $isSuccess,
        'bg-red-50' => $isDanger,
    ]);

    $iconWrapperClasses = \Illuminate\Support\Arr::toCssClasses([
        'p-4 rounded-l-lg',
        'bg-green-100' => $isSuccess,
        'bg-red-100' => $isDanger,
    ]);

    $iconClasses = \Illuminate\Support\Arr::toCssClasses([
        'h-6 w-6',
        'text-green-600' => $isSuccess,
        'text-red-600' => $isDanger,
    ]);

    $messageTitleClasses = \Illuminate\Support\Arr::toCssClasses([
        'text-sm font-semibold',
        'text-green-800' => $isSuccess,
        'text-red-800' => $isDanger,
    ]);

    $messageBodyClasses = \Illuminate\Support\Arr::toCssClasses([
        'mt-1 text-sm list-disc list-inside',
        'text-green-700' => $isSuccess,
        'text-red-700' => $isDanger,
    ]);
@endphp

{{--
    Menggunakan Alpine.js untuk state.
    - 'show: true' untuk menampilkan alert.
    - Alert akan hilang otomatis setelah 5 detik (5000ms).
    - 'x-transition' untuk animasi fade in/out yang halus.
--}}
<div
    x-data="{ show: true, timeout: null }"
    x-init="timeout = setTimeout(() => { show = false }, 5000)"
    x-show="show"
    x-transition:enter="transform ease-out duration-300 transition"
    x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
    x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
    x-transition:leave="transition ease-in duration-100"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="{{ $wrapperClasses }}"
    style="z-index: 50;"
>
    <div class="p-4 flex items-start">
        <div class="flex-shrink-0">
            @if($isSuccess)
                <svg class="{{ $iconClasses }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            @endif
            @if($isDanger)
                 <svg class="{{ $iconClasses }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
            @endif
        </div>
        <div class="ml-3 w-0 flex-1 pt-0.5">
            <p class="{{ $messageTitleClasses }}">
                {{ $isSuccess ? 'Sukses' : 'Terjadi Kesalahan' }}
            </p>
            <div class="{{ $messageBodyClasses }}">
                {{-- Komponen ini bisa menerima string atau array (untuk validation errors) --}}
                @if(is_array($message))
                    <ul class="list-disc list-inside">
                        @foreach($message as $msg)
                            <li>{{ $msg }}</li>
                        @endforeach
                    </ul>
                @else
                    {{ $message }}
                @endif
            </div>
        </div>
        <div class="ml-4 flex-shrink-0 flex">
            <button @click="show = false; clearTimeout(timeout)" type="button" class="inline-flex rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                <span class="sr-only">Close</span>
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                </svg>
            </button>
        </div>
    </div>
</div>
