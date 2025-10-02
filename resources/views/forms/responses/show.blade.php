<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Jawaban: ') }} <span class="italic">{!! strip_tags($form->title) !!}</span>
        </h2>
    </x-slot>

    <div class="py-6 bg-gray-100 min-h-screen">
        <div class="max-w-3xl mx-auto space-y-4">

            {{-- Navigasi Tab (Meniru Google Form) --}}
            <div class="flex bg-white rounded-lg shadow-sm border-b border-gray-200">
                <a href="{{ route('forms.responses.index', $form) }}"
                   class="px-6 py-3 text-sm font-medium text-gray-600 hover:text-indigo-600">
                    Ringkasan & Pertanyaan
                </a>
                <span class="px-6 py-3 text-sm font-medium text-indigo-600 border-b-2 border-indigo-600">
                    Individual
                </span>
            </div>

            {{-- Kartu Navigasi Individual --}}
            <div class="bg-white rounded-lg shadow-sm p-4 border border-indigo-500">
                <div class="flex justify-between items-center">
                    {{-- Navigasi Sebelumnya/Berikutnya --}}
                    <div class="flex items-center space-x-2 text-gray-700">
                        {{-- Logika untuk navigasi response sebelumnya/berikutnya --}}
                        @php
                            $prevResponse = $form->responses()->where('created_at', '<', $response->created_at)->orderByDesc('created_at')->first();
                            $nextResponse = $form->responses()->where('created_at', '>', $response->created_at)->orderBy('created_at')->first();
                        @endphp

                        {{-- Tombol Previous --}}
                        @if ($prevResponse)
                            <a href="{{ route('forms.responses.show', [$form, $prevResponse]) }}" class="text-gray-500 hover:text-indigo-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                            </a>
                        @else
                            <span class="text-gray-300 cursor-not-allowed">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                            </span>
                        @endif

                        <span class="font-semibold">{{ $currentResponseIndex }}</span> dari <span class="text-gray-500">{{ $totalResponses }}</span>

                        {{-- Tombol Next --}}
                        @if ($nextResponse)
                            <a href="{{ route('forms.responses.show', [$form, $nextResponse]) }}" class="text-gray-500 hover:text-indigo-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        @else
                            <span class="text-gray-300 cursor-not-allowed">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </span>
                        @endif
                    </div>

                    {{-- Aksi --}}
                    <div class="flex items-center space-x-4">
                        <button class="text-gray-500 hover:text-red-600" title="Hapus Jawaban" onclick="document.getElementById('delete-response-form').submit()">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>

                        {{-- Form Hapus Jawaban (sembunyikan) --}}
                        <form id="delete-response-form" method="POST" action="{{ route('forms.responses.destroy', [$form, $response]) }}" style="display:none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>
            </div>

            {{-- Kartu Detail Jawaban --}}
            <div class="bg-white shadow-lg rounded-2xl p-6 space-y-6">

                {{-- Detail Jawaban per Pertanyaan --}}
                @foreach ($fields as $fieldName => $field)
                    <div class="border-b pb-4">
                        <p class="text-lg font-semibold text-gray-800 mb-2">{!! strip_tags($field['label']) !!}</p>

                        <div class="mt-1 p-3 bg-gray-50 rounded text-gray-700">
                            @php
                                $answer = $answers[$fieldName] ?? '-';
                            @endphp

                            @if (is_array($answer))
                                {{-- Untuk Checkbox --}}
                                <ul class="list-disc list-inside">
                                    @forelse($answer as $item)
                                        <li>{{ $item }}</li>
                                    @empty
                                        <span class="text-gray-400">Tidak ada pilihan.</span>
                                    @endforelse
                                </ul>
                            @elseif ($field['type'] === 'file' && $answer !== '-')
                                {{-- Untuk Upload File --}}
                                <a href="{{ Storage::url($answer) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 font-semibold flex items-center">
                                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0L8 12m4-4v12"></path></svg>
                                    {{ basename($answer) }} (Lihat/Download)
                                </a>
                            @else
                                {{-- Untuk Text, Textarea, Radio, Select, Date, Time --}}
                                {!! nl2br(e($answer)) !!}
                            @endif
                        </div>
                    </div>
                @endforeach

                <p class="text-sm text-right text-gray-500 pt-4">
                    {{ \Carbon\Carbon::parse($response->created_at)->format('d/m/y, H:i') }} dikirimkan
                </p>
            </div>

        </div>
    </div>
</x-app-layout>
