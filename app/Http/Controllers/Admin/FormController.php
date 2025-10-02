<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FormController extends Controller
{
    /**
     * Menampilkan daftar form milik pengguna.
     */
    public function index()
    {
        $forms = Form::whereBelongsTo(Auth::user())
            ->latest()
            ->paginate(10);

        return view('forms.index', compact('forms'));
    }

    /**
     * Menampilkan halaman untuk membuat form baru.
     * Mengarah ke view gabungan 'forms.form'.
     */
    public function create()
    {
        return view('forms.form');
    }

    /**
     * Menyimpan form baru ke database.
     */
    public function show(Form $form)
{
    // Otorisasi
    if ($form->user->isNot(Auth::user())) {
        abort(403, 'Unauthorized');
    }

    // DIUBAH KEMBALI: Arahkan ke view 'forms.form' untuk langsung mengedit
    return view('forms.form', compact('form'));
}
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'            => 'required|string',
            'description'      => 'nullable|string',
            'form_fields'      => 'nullable|string',
            'is_public'        => 'sometimes|boolean',
            'theme_color'      => 'nullable|string|max:7',
            'background_color' => 'nullable|string|max:7',
            'font_family'      => 'nullable|string',
            'header_image'     => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        // Handle upload header image jika ada
        if ($request->hasFile('header_image')) {
            $path = $request->file('header_image')->store('form-headers', 'public');
            $data['header_image'] = $path;
        }

        // Proses data form
        $data['slug'] = Str::slug($data['title']) . '-' . Str::random(6);
        $data['form_fields'] = $data['form_fields']
            ? json_decode($data['form_fields'], true)
            : [];

        // Buat form melalui relasi user
        $form = Auth::user()->forms()->create($data);

        // Arahkan ke halaman edit agar pengguna bisa langsung melanjutkan
        return redirect()->route('forms.edit', $form)->with('success', 'Form berhasil dibuat. Anda bisa melanjutkan mengedit.');
    }

    /**
     * Menampilkan halaman untuk mengedit form.
     * Mengarah ke view gabungan 'forms.form'.
     */
    public function edit(Form $form)
    {
        // Otorisasi: pastikan form milik user yang sedang login
        if ($form->user->isNot(Auth::user())) {
            abort(403, 'Unauthorized');
        }

        return view('forms.form', compact('form'));
    }

    /**
     * Memperbarui form yang ada di database.
     */
    public function update(Request $request, Form $form)
    {
        // Otorisasi: pastikan form milik user yang sedang login
        if ($form->user->isNot(Auth::user())) {
            abort(403, 'Unauthorized');
        }

        $data = $request->validate([
            'title'            => 'required|string',
            'description'      => 'nullable|string',
            'form_fields'      => 'nullable|string',
            'is_public'        => 'sometimes|boolean',
            // Validasi untuk tema
            'theme_color'      => 'nullable|string|max:7',
            'background_color' => 'nullable|string|max:7',
            'font_family'      => 'nullable|string',
            'header_image'     => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        // Handle upload header image jika ada file baru
        if ($request->hasFile('header_image')) {
            // Hapus gambar lama jika ada
            if ($form->header_image) {
                Storage::disk('public')->delete($form->header_image);
            }
            // Simpan gambar baru
            $path = $request->file('header_image')->store('form-headers', 'public');
            $data['header_image'] = $path;
        }

        $data['form_fields'] = $data['form_fields']
            ? json_decode($data['form_fields'], true)
            : [];

        $form->update($data);

        // Kembali ke halaman edit dengan pesan sukses
        return redirect()->route('forms.edit', $form)->with('success', 'Form berhasil diperbarui');
    }

    /**
     * Menampilkan form ke publik.
     */
    public function publicShow($slug)
    {
        $form = Form::where('slug', $slug)
            ->where('is_public', true)
            ->firstOrFail();

        return view('forms.public_show', compact('form'));
    }

    /**

    /**
     * Menampilkan response dari sebuah form.
     */
    public function responses(Form $form)
    {
        if ($form->user->isNot(Auth::user())) {
            abort(403, 'Unauthorized');
        }

        $responses = $form->responses()->latest()->paginate(20);
        return view('forms.responses', compact('form', 'responses'));
    }
}
