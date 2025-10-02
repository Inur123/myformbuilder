<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{-- Judul dinamis: Edit atau Buat Baru --}}
            @if (isset($form))
                {{-- Menggunakan strip_tags agar judul HTML tidak merusak tampilan header --}}
                {{ __('Edit Form: ') }} <span class="italic">{!! $form->title ? strip_tags($form->title) : '' !!}</span>
            @else
                {{ __('Buat Form Baru') }}
            @endif
        </h2>
    </x-slot>


    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Form dinamis dengan enctype untuk upload file --}}
            <form method="POST" action="{{ isset($form) ? route('forms.update', $form) : route('forms.store') }}"
                id="formEditor" enctype="multipart/form-data">
                @csrf
                @if (isset($form))
                    @method('PUT')
                @endif

                {{-- Layout 2 kolom: Builder di kiri, Tema di kanan --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                    {{-- Kolom Kiri: Builder Formulir --}}
                    <div class="lg:col-span-2 space-y-6">
                        {{-- Bagian Detail Form (Judul & Deskripsi) --}}
                        <div class="bg-white shadow-sm rounded-lg p-6 space-y-4">
                            @if ($errors->any())
                                <div class="mb-4 p-3 bg-red-50 border border-red-100 text-red-700 rounded">
                                    <ul class="list-disc list-inside">
                                        @foreach ($errors->all() as $err)
                                            <li>{{ $err }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700">Judul Form</label>
                                <textarea id="title" name="title" rows="1"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('title', $form->title ?? '') }}</textarea>
                            </div>
                            <div>
                                <label for="description"
                                    class="block text-sm font-medium text-gray-700">Deskripsi</label>
                                <textarea id="description" name="description" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $form->description ?? '') }}</textarea>
                            </div>
                        </div>

                        {{-- Form Builder Dinamis --}}
                        <div id="formBuilder" class="space-y-4">
                            {{-- Pertanyaan akan dirender di sini oleh JavaScript --}}
                        </div>

                        <div class="mt-4">
                            <button type="button" id="addFieldBtn"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 font-semibold text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                Tambah Pertanyaan
                            </button>
                        </div>
                    </div>

                    {{-- Kolom Kanan (Sidebar): Pengaturan Tema --}}
                    <div class="lg:col-span-1">
                        <div class="bg-white shadow-sm rounded-lg p-6 space-y-6 sticky top-6">
                            <h3 class="text-lg font-medium text-gray-900 border-b pb-3">🎨 Tema</h3>

                            {{-- Gambar Header --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Gambar Header</label>
                                @if (isset($form) && $form->header_image)
                                    <img src="{{ Storage::url($form->header_image) }}" alt="Header"
                                        class="mt-2 rounded-md h-32 w-full object-cover">
                                @endif
                                <input type="file" name="header_image"
                                    class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            </div>

                            {{-- Warna Tema --}}
                            <div>
                                <label for="theme_color" class="block text-sm font-medium text-gray-700">Warna
                                    Tema</label>
                                <input type="color" name="theme_color" id="theme_color"
                                    value="{{ old('theme_color', $form->theme_color ?? '#4F46E5') }}"
                                    class="mt-1 h-10 w-full rounded-md border-gray-300">
                            </div>

                            {{-- Warna Latar Belakang --}}
                            <div>
                                <label for="background_color" class="block text-sm font-medium text-gray-700">Warna
                                    Latar Belakang</label>
                                <input type="color" name="background_color" id="background_color"
                                    value="{{ old('background_color', $form->background_color ?? '#F3F4F6') }}"
                                    class="mt-1 h-10 w-full rounded-md border-gray-300">
                            </div>
                            {{-- Gaya Teks --}}
                            <div>
                                <label for="font_family" class="block text-sm font-medium text-gray-700">Jenis
                                    Huruf</label>
                                <select name="font_family" id="font_family"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    {{-- Daftar Pilihan Font Baru --}}
                                    <option value="Inter" @selected(old('font_family', $form->font_family ?? 'Inter') == 'Inter')>Inter</option>
                                    <option value="Poppins" @selected(old('font_family', $form->font_family ?? 'Inter') == 'Poppins')>Poppins</option>
                                    <option value="Roboto" @selected(old('font_family', $form->font_family ?? 'Inter') == 'Roboto')>Roboto</option>
                                    <option value="Lato" @selected(old('font_family', $form->font_family ?? 'Inter') == 'Lato')>Lato</option>
                                    <option value="Montserrat" @selected(old('font_family', $form->font_family ?? 'Inter') == 'Montserrat')>Montserrat</option>
                                    <option value="Open Sans" @selected(old('font_family', $form->font_family ?? 'Inter') == 'Open Sans')>Open Sans</option>
                                    <option value="Noto Sans" @selected(old('font_family', $form->font_family ?? 'Inter') == 'Noto Sans')>Noto Sans</option>
                                    <option value="Merriweather" @selected(old('font_family', $form->font_family ?? 'Inter') == 'Merriweather')>Merriweather (Serif)
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label for="thank_you_message" class="block text-sm font-medium text-gray-700">Pesan
                                    Terima Kasih</label>
                                <textarea id="thank_you_message" name="thank_you_message" rows="4"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('thank_you_message', $form->thank_you_message ?? '') }}</textarea>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Hidden input & Aksi Form Utama --}}
                <input type="hidden" name="form_fields" id="form_fields_input"
                    value="{{ old('form_fields', isset($form) ? json_encode($form->form_fields) : '[]') }}">
                <div class="mt-8 bg-white shadow-sm rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="is_public" value="1"
                                {{ old('is_public', $form->is_public ?? 1) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700">Jadikan form ini publik</span>
                        </label>
                        <div class="flex items-center gap-4">
                            <a href="{{ route('forms.index') }}"
                                class="text-sm text-gray-600 hover:underline">Batal</a>
                            <button type="submit"
                                class="px-5 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-semibold">
                                {{ isset($form) ? 'Simpan Perubahan' : 'Simpan Form' }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Template untuk kartu pertanyaan --}}
    <template id="fieldCardTemplate">
        <div class="bg-white shadow-sm rounded-lg p-6 border-l-4 border-indigo-500">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-1">
                    <textarea class="field-label-editor"></textarea>
                </div>
                <div class="md:col-span-1">
                    <select
                        class="field-type w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        onchange="updateField(this.dataset.index, 'type', this.value)">
                        <option value="text">Jawaban Singkat (Text)</option>
                        <option value="textarea">Paragraf (Textarea)</option>
                        <option value="radio">Pilihan Ganda (Radio)</option>
                        <option value="checkbox">Kotak Centang (Checkbox)</option>
                        <option value="select">Drop-down</option>
                        <option value="date">Tanggal</option>
                        <option value="time">Waktu</option>
                        {{-- **BARU:** Opsi Upload File ditambahkan di sini --}}
                        <option value="file">Upload File (File)</option>
                    </select>
                </div>
                <div class="options-wrap md:col-span-2" style="display:none;">
                    <textarea placeholder="Tulis pilihan jawaban, pisahkan dengan koma. Contoh: Pilihan 1, Pilihan 2"
                        class="field-options w-full mt-2 rounded-md border-gray-300 shadow-sm text-sm" rows="3"
                        oninput="updateField(this.dataset.index, 'options', this.value)"></textarea>
                </div>
            </div>
            <div class="mt-6 pt-4 border-t flex justify-end items-center gap-6">
                <label class="inline-flex items-center text-sm text-gray-600">
                    <input type="checkbox"
                        class="field-required rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                        onchange="updateField(this.dataset.index, 'required', this.checked)">
                    <span class="ml-2">Wajib diisi</span>
                </label>
                <button type="button" class="text-gray-400 hover:text-red-600" title="Hapus Pertanyaan"
                    onclick="removeField(this.dataset.index)">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        </div>
    </template>

    {{-- Script JavaScript untuk integrasi TinyMCE --}}
    <script>
        (function() {
            let fields = [];
            const formBuilder = document.getElementById('formBuilder');
            const addFieldBtn = document.getElementById('addFieldBtn');
            const template = document.getElementById('fieldCardTemplate');
            const formFieldsInput = document.getElementById('form_fields_input');

            function renderList() {
                fields.forEach((field, index) => {
                    const editor = tinymce.get(`editor-${index}`);
                    if (editor) {
                        editor.remove();
                    }
                });
                formBuilder.innerHTML = '';
                fields.forEach((field, index) => {
                    const card = template.content.cloneNode(true);
                    const editorTextarea = card.querySelector('.field-label-editor');
                    const typeSelect = card.querySelector('.field-type');
                    const optionsWrap = card.querySelector('.options-wrap');
                    const optionsInput = card.querySelector('.field-options');
                    const requiredCheckbox = card.querySelector('.field-required');
                    const deleteBtn = card.querySelector('button[title="Hapus Pertanyaan"]');
                    const editorId = `editor-${index}`;
                    editorTextarea.id = editorId;
                    editorTextarea.value = field.label;
                    typeSelect.dataset.index = index;
                    optionsInput.dataset.index = index;
                    requiredCheckbox.dataset.index = index;
                    deleteBtn.dataset.index = index;
                    typeSelect.value = field.type;
                    requiredCheckbox.checked = field.required;
                    if (hasOptions(field.type)) {
                        optionsWrap.style.display = 'block';
                        optionsInput.value = Array.isArray(field.options) ? field.options.join(', ') : '';
                    } else {
                        optionsWrap.style.display = 'none';
                    }
                    formBuilder.appendChild(card);
                    tinymce.init({
                        selector: `#${editorId}`,
                        height: 150,
                        menubar: false,
                        plugins: 'lists link autolink textcolor', // plugin untuk warna teks
                        toolbar: 'bold italic underline | forecolor | link | removeformat', // tombol untuk ganti warna
                        setup: function(editor) {
                            editor.on('input change', function() {
                                updateField(index, 'label', editor.getContent());
                            });
                        }
                    });

                });
                updateHiddenInput();
            }

            window.updateField = function(index, key, value) {
                const field = fields[index];
                if (!field) return;
                if (key === 'options') {
                    field[key] = value.split(',').map(s => s.trim()).filter(Boolean);
                } else {
                    field[key] = value;
                }
                if (key === 'type') {
                    renderList();
                } else {
                    updateHiddenInput();
                }
            }

            window.removeField = function(index) {
                if (confirm('Apakah Anda yakin ingin menghapus pertanyaan ini?')) {
                    const editor = tinymce.get(`editor-${index}`);
                    if (editor) {
                        editor.remove();
                    }
                    fields.splice(index, 1);
                    renderList();
                }
            }

            function addField() {
                const baseName = 'pertanyaan_baru';
                let name = baseName;
                let i = 1;
                while (fields.some(f => f.name === name)) {
                    name = `${baseName}_${++i}`;
                }
                fields.push({
                    label: '',
                    name: name,
                    type: 'text',
                    required: false,
                    options: []
                });
                renderList();
            }

            function hasOptions(type) {
                // 'file' tidak termasuk di sini, sehingga kotak opsi pilihan akan otomatis tersembunyi.
                return ['radio', 'checkbox', 'select'].includes(type);
            }

            function updateHiddenInput() {
                formFieldsInput.value = JSON.stringify(fields);
            }
            addFieldBtn.addEventListener('click', addField);

            try {
                const existingData = formFieldsInput.value;
                fields = existingData && existingData !== '[]' ? JSON.parse(existingData) : [];
                if (fields.length === 0 && !'{{ isset($form) }}') {
                    addField();
                }
            } catch (e) {
                console.error("Gagal memuat data form:", e);
                fields = [];
                if (!'{{ isset($form) }}') {
                    addField();
                }
            }

            renderList();

            // Inisialisasi TinyMCE untuk Judul dan Deskripsi
            tinymce.init({
                selector: 'textarea#title',
                height: 80,
                menubar: false,
                plugins: 'autolink textcolor', // tambahkan textcolor
                toolbar: 'bold italic underline | forecolor | removeformat', // tambahkan forecolor
                setup: function(editor) {
                    editor.on('init', function() {
                        this.save();
                    });
                    editor.on('change', function() {
                        this.save();
                    });
                }
            });

            tinymce.init({
                selector: 'textarea#description',
                height: 200,
                menubar: false,
                plugins: 'lists link autolink textcolor', // tambahkan textcolor
                toolbar: 'bold italic underline | forecolor | bullist numlist | link | removeformat', // tambahkan forecolor
                setup: function(editor) {
                    editor.on('init', function() {
                        this.save();
                    });
                    editor.on('change', function() {
                        this.save();
                    });
                }
            });

            tinymce.init({
                selector: 'textarea#thank_you_message',
                height: 150,
                menubar: false,
                plugins: 'lists link autolink textcolor', // tambahkan textcolor
                toolbar: 'bold italic underline | forecolor | bullist numlist | link | removeformat', // tambahkan forecolor
                setup: function(editor) {
                    editor.on('init change input', function() {
                        this.save();
                    });
                }
            });

        })();
    </script>
</x-app-layout>
