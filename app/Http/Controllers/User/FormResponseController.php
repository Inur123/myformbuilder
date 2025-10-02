<?php

namespace App\Http\Controllers\User;

use App\Models\Form;
use App\Models\FormResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FormResponseController extends Controller
{
      public function store(Request $request, $slug)
    {
        $form = Form::where('slug', $slug)->where('is_public', true)->firstOrFail();

        $fields = $form->form_fields ?? [];
        $rules = [];

        // bangun rules dinamis berdasarkan definisi fields
        foreach ($fields as $field) {
            if (!empty($field['required'])) {
                $rules[$field['name']] = 'required';
            }
        }

        $validated = $request->validate($rules);

        // simpan semua input (bukan hanya yang tervalidasi) — lebih fleksibel
        $all = [];
        foreach ($fields as $field) {
            $key = $field['name'];
            $all[$key] = $request->input($key);
        }

        FormResponse::create([
            'form_id' => $form->id,
            'response_data' => $all,
        ]);

        // LAMA: return redirect()->back()->with('success','Terima kasih, jawaban Anda telah tersimpan.');

        // BARU: Redirect ke halaman "Terima Kasih" yang sudah kita siapkan
        return redirect()->route('forms.public.thanks', ['slug' => $form->slug]);
    }

    /**
     * Menampilkan halaman "Terima Kasih" setelah user submit form.
     */
  public function thanks($slug)
{
    $form = Form::where('slug', $slug)->where('is_public', true)->firstOrFail();
    $hash = $form->hashid; // <-- pastikan ini ada

    return view('forms.public_thanks', compact('form', 'hash'));
}

}
