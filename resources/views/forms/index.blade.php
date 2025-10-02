<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Forms') }}
        </h2>
    </x-slot>

    @if (session('success'))
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-2">
            <div class="p-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                {{ session('success') }}
            </div>
        </div>
    @endif

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-2xl overflow-hidden">
                <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-700">Daftar Form</h3>
                    <a href="{{ route('forms.create') }}"
                        class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg shadow hover:bg-indigo-700 transition">
                        + Buat Form Baru
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dibuat</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($forms as $form)
                                <tr>
                                  <td class="px-6 py-4 text-sm font-medium text-gray-800">
    {{ html_entity_decode(strip_tags($form->title)) }}
</td>

<td class="px-6 py-4 text-sm text-gray-600">
    {{ \Illuminate\Support\Str::limit(html_entity_decode(strip_tags($form->description)), 50) }}
</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $form->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm space-x-4 whitespace-nowrap">
                                        {{-- DIUBAH: Tombol 'Lihat' dan 'Edit' digabung menjadi satu --}}
                                        <a href="{{ route('forms.show', $form) }}"
                                            class="text-indigo-600 hover:text-indigo-800 font-medium">Lihat/Edit</a>

                                        <a href="{{ route('forms.public.show', $form->slug) }}" target="_blank"
                                            class="text-green-600 hover:text-green-800 font-medium">
                                            Link Publik
                                        </a>

                                        <form action="{{ route('forms.destroy', $form) }}" method="POST"
                                            class="inline-block"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus form ini beserta semua responsnya?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                        Belum ada form yang dibuat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($forms->hasPages())
                    <div class="px-6 py-4">
                        {{ $forms->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
