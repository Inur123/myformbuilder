<x-user-layout>
    @push('styles')
        <style>
            /* ... (kode style Anda) ... */
            @import url('https://fonts.googleapis.com/css2?family={{ urlencode($form->font_family ?? 'Inter') }}:wght@400;500;600;700&display=swap');
            :root { --theme-color: {{ $form->theme_color ?? '#4F46E5' }}; }
            body {
                background-color: {{ $form->background_color ?? '#F1F5F9' }} !important;
                font-family: '{{ $form->font_family ?? 'Inter' }}', sans-serif;
            }
            .link-themed { color: var(--theme-color); }
        </style>
    @endpush

    <div class="max-w-3xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-2xl p-8 sm:p-10 space-y-6">
            <div class="pb-4 border-b">
                <h2 class="text-2xl font-bold tracking-tight text-gray-900">{!! $form->title !!}</h2>
            </div>

            <div class="prose max-w-none">
                <p class="text-lg">Jawaban Anda telah direkam.</p>

                @if($form->thank_you_message)
                    <div class="mt-4 text-gray-700">
                        {!! $form->thank_you_message !!}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-user-layout>
