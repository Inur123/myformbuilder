<x-guest-layout>
@push('styles')
    <style>
        /* Impor font yang dipilih secara dinamis dari Google Fonts */
        @import url('https://fonts.googleapis.com/css2?family={{ urlencode($form->font_family ?? 'Inter') }}:wght@400;500;600;700&display=swap');

        :root {
            --theme-color: {{ $form->theme_color ?? '#4F46E5' }};
        }

        body {
            background-color: {{ $form->background_color ?? '#F1F5F9' }} !important;
            /* Terapkan font yang sudah diimpor */
            font-family: '{{ $form->font_family ?? 'Inter' }}', sans-serif;
        }

        /* === KODE PERBAIKAN DITAMBAHKAN DI SINI === */
        .form-question-label p {
            display: inline; /* Membuat paragraf sejajar */
            margin: 0;
            padding: 0;
        }
        /* === BATAS KODE PERBAIKAN === */

        .themed-input {
            color: var(--theme-color);
        }

        .themed-focus-border:focus {
            border-color: var(--theme-color);
        }

        .btn-submit {
            background-color: var(--theme-color);
        }

        .btn-submit:hover {
            filter: brightness(90%);
        }
    </style>
@endpush

    {{-- Pembungkus utama dengan jarak otomatis antar kartu --}}
    <div class="max-w-3xl mx-auto py-8 px-4 sm:px-0 space-y-8">

        {{-- KARTU 1: Header Dekorasi --}}
        <div class="min-h-[12rem] shadow-lg rounded-2xl
            @if ($form->header_image) bg-cover bg-center @endif"
            style="
                @if ($form->header_image) background-image: url('{{ Storage::url($form->header_image) }}');
                @else
                    background-color: var(--theme-color); @endif
            ">
        </div>

        {{-- KARTU 2: Judul & Deskripsi --}}
        <div class="bg-white shadow-lg rounded-2xl p-6 sm:p-8">
            @if (session('success'))
                <x-alert type="success" :message="session('success')" />
            @elseif($errors->any())
                <x-alert type="danger" :message="$errors->all()" />
            @endif

            <div class="mb-6">
                <h2 class="text-2xl font-bold tracking-tight text-gray-900">{!! $form->title !!}</h2>
                <p class="mt-2 text-md leading-6 text-gray-700">{!! $form->description !!}</p>
            </div>
        </div>

        {{-- Form yang membungkus semua kartu input --}}
        <form method="POST" action="{{ route('forms.public.submit', $form->slug) }}" class="space-y-8">
            @csrf

            {{-- Loop untuk membuat KARTU untuk SETIAP input --}}
            @foreach ($form->form_fields as $field)
                <div class="bg-white shadow-lg rounded-2xl p-6 sm:p-8">
                    @php
                        $name = $field['name'];
                        $id = 'field-' . $name;
                        $isRequired = !empty($field['required']);
                    @endphp
                    <div>
                        {{-- PERUBAHAN: Menambahkan class 'form-question-label' --}}
                        <label for="{{ $id }}" class="block text-sm font-medium text-gray-900 form-question-label">
                            {!! $field['label'] !!}
                            @if ($isRequired)
                                {{-- PERUBAHAN: Menambahkan margin kiri 'ml-1' --}}
                                <span class="text-red-500 ml-1">*</span>
                            @endif
                        </label>
                        <div class="mt-2">
                            @if ($field['type'] === 'text')
                                <input type="text" name="{{ $name }}" id="{{ $id }}"
                                    value="{{ old($name) }}"
                                    class="block w-full border-0 border-b-2 border-gray-200 bg-transparent py-2 px-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 themed-focus-border"
                                    @if ($isRequired) required @endif>
                            @elseif($field['type'] === 'textarea')
                                <textarea name="{{ $name }}" id="{{ $id }}" rows="3"
                                    class="block w-full border-0 border-b-2 border-gray-200 bg-transparent py-2 px-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 themed-focus-border"
                                    @if ($isRequired) required @endif>{{ old($name) }}</textarea>
                            @elseif($field['type'] === 'select')
                                <select name="{{ $name }}" id="{{ $id }}"
                                    class="block w-full border-0 border-b-2 border-gray-200 bg-transparent py-2 px-1 text-gray-900 focus:ring-0 themed-focus-border"
                                    @if ($isRequired) required @endif>
                                    <option value="">-- Pilih Salah Satu --</option>
                                    @foreach ($field['options'] as $option)
                                        <option value="{{ $option }}" @selected(old($name) == $option)>
                                            {{ $option }}</option>
                                    @endforeach
                                </select>
                            @elseif($field['type'] === 'date')
                                <input type="date" name="{{ $name }}" id="{{ $id }}"
                                    value="{{ old($name) }}"
                                    class="block w-full border-0 border-b-2 border-gray-200 bg-transparent py-2 px-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 themed-focus-border"
                                    @if ($isRequired) required @endif>
                            @elseif($field['type'] === 'time')
                                <input type="time" name="{{ $name }}" id="{{ $id }}"
                                    value="{{ old($name) }}"
                                    class="block w-full border-0 border-b-2 border-gray-200 bg-transparent py-2 px-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 themed-focus-border"
                                    @if ($isRequired) required @endif>
                            @elseif(in_array($field['type'], ['radio', 'checkbox']))
                                <div class="space-y-3 pt-1">
                                    @foreach ($field['options'] as $key => $option)
                                        <div class="flex items-center">
                                            <input type="{{ $field['type'] }}" id="{{ 'option-' . $key }}"
                                                name="{{ $name }}{{ $field['type'] === 'checkbox' ? '[]' : '' }}"
                                                value="{{ $option }}"
                                                class="h-4 w-4 rounded border-gray-300 focus:ring-indigo-600"
                                                @if ($field['type'] === 'radio') @checked(old($name) == $option)
                                                @else @checked(is_array(old($name)) && in_array($option, old($name))) @endif>
                                            <label for="{{ 'option-' . $key }}"
                                                class="ml-3 block text-sm text-gray-900">{{ $option }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        @error($name)
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            @endforeach

            <div class="pt-4 flex items-center justify-between">
                {{-- Tombol Kirim (Submit) --}}
                <button type="submit"
                    class="btn-submit rounded-md px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition ease-in-out duration-150">
                    Kirim
                </button>

                {{-- Tombol Kosongkan Formulir (Reset) --}}
                <button type="reset" class="text-sm font-semibold text-gray-800 hover:text-gray-900">
                    Kosongkan formulir
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
