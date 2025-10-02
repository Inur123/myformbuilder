<?php

namespace App\Http\Controllers\User;

use App\Models\Form;
use App\Models\FormResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage; // Tambahkan ini untuk Storage

class FormResponseController extends Controller
{
    public function store(Request $request, $slug)
    {
        $form = Form::where('slug', $slug)->where('is_public', true)->firstOrFail();

        $formFields = collect($form->form_fields); // Konversi ke Collection untuk kemudahan
        $rules = [];
        $answers = [];

        // 1. Bangun Rules dan Siapkan Jawaban
        foreach ($formFields as $field) {
            $key = $field['name'];
            $rules[$key] = $field['required'] ? 'required' : 'nullable';

            // Menambahkan aturan validasi khusus untuk file
            if ($field['type'] === 'file') {
                // Aturan dasar untuk file, Anda bisa menyesuaikan mime, max size, dll.
                $rules[$key] .= '|file|max:5120|mimes:pdf,doc,docx,jpg,jpeg,png';
            } elseif ($field['type'] === 'checkbox') {
                 // Checkbox harus divalidasi sebagai array
                 $rules[$key] = $field['required'] ? 'required|array' : 'nullable|array';
            }
        }

        // 2. Validasi semua input (termasuk file)
        $validated = $request->validate($rules);


        // 3. Proses dan Simpan Jawaban (Termasuk File)
        foreach ($formFields as $field) {
            $key = $field['name'];

            if ($field['type'] === 'file') {
                // Jika field adalah file dan file terkirim
                if ($request->hasFile($key) && $request->file($key)->isValid()) {
                    // Simpan file ke disk 'public' di folder 'forms/{slug}'
                    // store() mengembalikan path file (misal: forms/judul-form/randomhash.jpg)
                    $path = $request->file($key)->store('forms/' . $form->slug, 'public');
                    $answers[$key] = $path; // Simpan path ini di database
                } else {
                    $answers[$key] = null; // Tidak ada file diunggah
                }
            } elseif ($field['type'] === 'checkbox') {
                // Simpan checkbox sebagai array (atau array kosong jika tidak ada)
                $answers[$key] = $request->input($key, []);
            } else {
                // Simpan input non-file
                $answers[$key] = $request->input($key);
            }
        }

        // 4. Simpan ke Database
        FormResponse::create([
            'form_id' => $form->id,
            // Simpan array jawaban yang sudah termasuk path file sebagai JSON
            'response_data' => $answers,
        ]);

        // Redirect ke halaman "Terima Kasih"
        return redirect()->route('forms.public.thanks', ['slug' => $form->slug]);
    }

    /**
     * Menampilkan halaman "Terima Kasih" setelah user submit form.
     */
    public function thanks($slug)
    {
        $form = Form::where('slug', $slug)->where('is_public', true)->firstOrFail();
        // Asumsi 'hashid' adalah kolom yang valid di model Form
        $hash = $form->hashid ?? null;

        return view('forms.public_thanks', compact('form', 'hash'));
    }
}
