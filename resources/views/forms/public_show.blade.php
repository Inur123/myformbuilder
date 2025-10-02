<x-guest-layout>
    {{-- GAYA BARU: Header berwarna solid untuk Judul & Deskripsi --}}
     <div class="bg-indigo-600 p-6 sm:p-8 text-white rounded-t-xl">
        <h2 class="text-2xl font-bold tracking-tight">
            {{-- DIUBAH: Gunakan {!! !!} agar format teks (bold, italic) bisa tampil --}}
            {!! $form->title !!}
        </h2>
        <p class="mt-2 text-md leading-6 text-indigo-200 prose prose-invert max-w-none">
            {{-- DIUBAH: Gunakan {!! !!} agar format teks (bold, italic, list) bisa tampil --}}
            {!! $form->description !!}
        </p>
    </div>
    {{-- Bagian utama form dengan background putih --}}
    <div class="p-6 sm:p-8 bg-white rounded-b-xl">
        {{-- Notifikasi --}}
        @if (session('success'))
            <div class="mb-4">
                <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                    {{ session('success') }}
                </div>
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-4">
                 <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                    {{ $errors->first() }}
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('forms.public.submit', $form->slug) }}" class="space-y-8">
            @csrf

            @foreach($form->form_fields as $field)
                @php
                    $name = $field['name'];
                    $id = 'field-' . $name;
                    $isRequired = !empty($field['required']);
                @endphp

                <div>
                    {{-- Label untuk semua jenis field --}}
                    <label for="{{ $id }}" class="block text-sm font-medium leading-6 text-gray-900">
                        {{-- DIUBAH: Gunakan {!! !!} agar format teks (bold, italic) dari editor bisa tampil --}}
                        {!! $field['label'] !!}
                        @if($isRequired) <span class="text-red-500">*</span> @endif
                    </label>

                    <div class="mt-2">
                        {{-- ==== INPUT TEXT ==== --}}
                        @if($field['type'] === 'text')
                            <input type="text"
                                   name="{{ $name }}"
                                   id="{{ $id }}"
                                   value="{{ old($name) }}"
                                   class="block w-full border-0 border-b-2 border-gray-200 bg-transparent py-2 px-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 focus:border-indigo-600 @error($name) border-red-500 @enderror"
                                   @if($isRequired) required @endif>

                        {{-- ==== INPUT TEXTAREA ==== --}}
                        @elseif($field['type'] === 'textarea')
                            <textarea name="{{ $name }}"
                                      id="{{ $id }}"
                                      rows="3"
                                      class="block w-full border-0 border-b-2 border-gray-200 bg-transparent py-2 px-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 focus:border-indigo-600 @error($name) border-red-500 @enderror"
                                      @if($isRequired) required @endif>{{ old($name) }}</textarea>

                        {{-- ==== INPUT SELECT ==== --}}
                        @elseif($field['type'] === 'select')
                            <select name="{{ $name }}"
                                    id="{{ $id }}"
                                    class="block w-full border-0 border-b-2 border-gray-200 bg-transparent py-2 px-1 text-gray-900 focus:ring-0 focus:border-indigo-600 @error($name) border-red-500 @enderror"
                                    @if($isRequired) required @endif>
                                <option value="">-- Pilih Salah Satu --</option>
                                @foreach($field['options'] as $option)
                                    <option value="{{ $option }}" @selected(old($name) == $option)>
                                        {{ $option }}
                                    </option>
                                @endforeach
                            </select>

                        {{-- ==== INPUT DATE ==== --}}
                        @elseif($field['type'] === 'date')
                             <input type="date" name="{{ $name }}" id="{{ $id }}" value="{{ old($name) }}"
                                   class="block w-full border-0 border-b-2 border-gray-200 bg-transparent py-2 px-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 focus:border-indigo-600 @error($name) border-red-500 @enderror"
                                   @if($isRequired) required @endif>

                        {{-- ==== INPUT TIME ==== --}}
                        @elseif($field['type'] === 'time')
                            <input type="time" name="{{ $name }}" id="{{ $id }}" value="{{ old($name) }}"
                                   class="block w-full border-0 border-b-2 border-gray-200 bg-transparent py-2 px-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 focus:border-indigo-600 @error($name) border-red-500 @enderror"
                                   @if($isRequired) required @endif>

                        {{-- ==== INPUT RADIO & CHECKBOX ==== --}}
                        @elseif(in_array($field['type'], ['radio', 'checkbox']))
                            <div class="space-y-3 pt-1">
                                @foreach($field['options'] as $key => $option)
                                    @php $optionId = $id . '-' . $key; @endphp
                                    <div class="flex items-center">
                                        <input type="{{ $field['type'] }}"
                                               id="{{ $optionId }}"
                                               name="{{ $name }}{{ $field['type'] === 'checkbox' ? '[]' : '' }}"
                                               value="{{ $option }}"
                                               class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                                               @if($field['type'] === 'radio')
                                                   @checked(old($name) == $option)
                                               @else
                                                   @checked(is_array(old($name)) && in_array($option, old($name)))
                                               @endif>
                                        <label for="{{ $optionId }}" class="ml-3 block text-sm text-gray-900">{{ $option }}</label>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Menampilkan Pesan Error Inline --}}
                    @error($name)
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endforeach

            {{-- Tombol Submit --}}
            <div class="pt-4">
                <button type="submit"
                    class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition ease-in-out duration-150">
                    Kirim
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
