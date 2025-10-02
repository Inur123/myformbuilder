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
                // gunakan nama unik untuk tiap pertanyaan (misal key)
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

        return redirect()->back()->with('success','Terima kasih, jawaban Anda telah tersimpan.');
    }
}
