<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Jawaban: ') }} <span class="italic">{!! strip_tags($form->title) !!}</span>
        </h2>
    </x-slot>

    <div class="py-6 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- Navigasi Tab (Meniru Google Form) --}}
            <div class="flex bg-white rounded-lg shadow-sm border-b border-gray-200">
                <span class="px-6 py-3 text-sm font-medium text-indigo-600 border-b-2 border-indigo-600">
                    Ringkasan & Pertanyaan
                </span>
                {{-- Pastikan responses->first() tidak null sebelum membuat link Individual --}}
                @if ($responses->first())
                    <a href="{{ route('forms.responses.show', [$form, $responses->first()]) }}"
                        class="px-6 py-3 text-sm font-medium text-gray-600 hover:text-indigo-600">
                        Individual
                    </a>
                @endif
            </div>

            {{-- Kartu Ringkasan Statistik --}}
            <div class="bg-white rounded-lg shadow-sm p-6 text-center border-l-4 border-indigo-500">
                <h3 class="text-4xl font-extrabold text-gray-900">{{ $totalResponses }}</h3>
                <p class="text-sm text-gray-500">jawaban</p>
            </div>

            {{-- Kartu Detail Data dalam Tabel (Ringkasan Data Mentah) --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Detail Data Jawaban</h3>

                    @php
                        // Ambil 5 kolom pertama untuk ringkasan di tabel
                        $displayFields = $fields->take(5);
                    @endphp

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        No
                                    </th>
                                    {{-- Kolom untuk 5 pertanyaan pertama --}}
                                    @foreach ($displayFields as $field)
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ Str::limit(strip_tags($field['label']), 30) }}
                                        </th>
                                    @endforeach
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tanggal Kirim
                                    </th>
                                    <th class="relative px-6 py-3">
                                        <span class="sr-only">Aksi</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($responses as $response)
                                    <tr>
                                        {{-- Ganti ID dengan nomor urut --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $loop->iteration }}
                                        </td>

                                        {{-- Tampilkan jawaban untuk 5 kolom pertama --}}
                                        @foreach ($displayFields as $field)
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 max-w-xs truncate">
                                                @php
                                                    $answers = $response->response_data ?? [];
                                                    $answer = $answers[$field['name']] ?? '-';
                                                @endphp

                                                @if (is_array($answer))
                                                    {{ Str::limit(implode(', ', $answer), 50) }}
                                                @elseif ($field['type'] === 'file' && $answer !== '-')
                                                    <a href="{{ Storage::url($answer) }}" target="_blank"
                                                        class="text-indigo-600 hover:text-indigo-900">Lihat File</a>
                                                @else
                                                    {{ Str::limit(strip_tags($answer), 50) }}
                                                @endif
                                            </td>
                                        @endforeach

                                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
    {{ $response->created_at->translatedFormat('d F Y H:i') }}
</td>

                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('forms.responses.show', [$form, $response]) }}"
                                                class="text-indigo-600 hover:text-indigo-900">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $displayFields->count() + 3 }}"
                                            class="px-6 py-4 text-center text-sm text-gray-500">
                                            Belum ada jawaban yang terkumpul.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $responses->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
