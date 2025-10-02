<?php

namespace App\Http\Controllers\Admin;

use App\Models\Form;
use Hashids\Hashids;
use Illuminate\Support\Str;
use App\Models\FormResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Exports\FormResponsesExport;
use Maatwebsite\Excel\Facades\Excel;

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
            // 'is_public' bisa dihapus dari sini atau dibiarkan, tidak masalah
            'theme_color'      => 'nullable|string|max:7',
            'background_color' => 'nullable|string|max:7',
            'font_family'      => 'nullable|string',
            'header_image'     => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'thank_you_message' => 'nullable|string',
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

        // === SOLUSI DITERAPKAN DI SINI ===
        $data['is_public'] = $request->has('is_public');

        // Buat form melalui relasi user
        $form = Auth::user()->forms()->create($data);

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
        // Otorisasi
        if ($form->user->isNot(Auth::user())) {
            abort(403, 'Unauthorized');
        }

        $data = $request->validate([
            'title'            => 'required|string',
            'description'      => 'nullable|string',
            'form_fields'      => 'nullable|string',
            'theme_color'      => 'nullable|string|max:7',
            'background_color' => 'nullable|string|max:7',
            'font_family'      => 'nullable|string',
            'header_image'     => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'thank_you_message' => 'nullable|string',
        ]);

        // ... (kode handle header_image Anda sudah benar) ...
        if ($request->hasFile('header_image')) {
            if ($form->header_image) {
                Storage::disk('public')->delete($form->header_image);
            }
            $path = $request->file('header_image')->store('form-headers', 'public');
            $data['header_image'] = $path;
        }

        $data['form_fields'] = $data['form_fields']
            ? json_decode($data['form_fields'], true)
            : [];

        // === SOLUSI DITERAPKAN DI SINI ===
        $data['is_public'] = $request->has('is_public');

        $form->update($data);

        return redirect()->route('forms.edit', $form)->with('success', 'Form berhasil diperbarui');
    }

    /**
     * Menampilkan form ke publik.
     */
    public function publicShow($hash)
    {
        $hashids = new Hashids('secret-key', 10);
        $ids = $hashids->decode($hash);

        if (empty($ids)) {
            abort(404);
        }

        $form = Form::where('id', $ids[0])
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

        // Ambil semua responses untuk form ini
        $responses = $form->responses()->latest()->paginate(20);

        // Ambil field (pertanyaan) dari form
        $fields = collect($form->form_fields);

        // Count total jawaban
        $totalResponses = $responses->total();

        // Pass ke view index
        return view('forms.responses.index', compact('form', 'responses', 'fields', 'totalResponses'));
    }

    /**
     * Menampilkan detail jawaban individu.
     */
    public function showResponseDetail(Form $form, FormResponse $response)
    {
        // Otorisasi: Form harus milik user yang sedang login
        if ($form->user->isNot(Auth::user())) {
            abort(403, 'Unauthorized');
        }

        // Pastikan response milik form ini
        if ($response->form_id !== $form->id) {
            abort(404);
        }

        // Ambil data jawaban dan fields
        $answers = $response->response_data ?? [];
        $fields = collect($form->form_fields)->keyBy('name');

        // Untuk navigasi Individual (halaman sebelumnya/selanjutnya)
        $currentResponseIndex = $form->responses()->orderBy('created_at')->pluck('id')->search($response->id) + 1;
        $totalResponses = $form->responses()->count();

        return view('forms.responses.show', compact('form', 'response', 'answers', 'fields', 'currentResponseIndex', 'totalResponses'));
    }

    /**
     * Menghapus jawaban individu dari form (FormResponse).
     */
    /**
 * Menghapus form beserta semua responsnya.
 */
public function destroy(Form $form)
{
    // Otorisasi: pastikan form milik user yang sedang login
    if ($form->user->isNot(Auth::user())) {
        abort(403, 'Unauthorized');
    }

    // Hapus semua respons terkait form ini
    foreach ($form->responses as $response) {
        $answers = $response->response_data ?? [];
        foreach ($answers as $answer) {
            if (is_string($answer) && Str::startsWith($answer, 'forms/')) {
                Storage::disk('public')->delete($answer);
            }
        }
        $response->delete();
    }

    // Hapus header image form jika ada
    if ($form->header_image) {
        Storage::disk('public')->delete($form->header_image);
    }

    // Hapus form
    $form->delete();

    return redirect()->route('forms.index')->with('success', 'Form beserta semua responsnya berhasil dihapus.');
}

    public function destroyResponse(Form $form, FormResponse $response)
    {
        // Otorisasi: Form harus milik user yang sedang login
        if ($form->user->isNot(Auth::user())) {
            abort(403, 'Unauthorized');
        }

        // Pastikan response milik form ini
        if ($response->form_id !== $form->id) {
            abort(404);
        }

        // Hapus file yang terkait jika ada
        $answers = $response->response_data ?? [];
        foreach ($answers as $answer) {
            // Hapus hanya jika path file yang valid (diawali 'forms/')
            if (is_string($answer) && Str::startsWith($answer, 'forms/')) {
                Storage::disk('public')->delete($answer);
            }
        }

        // Hapus data respons dari database
        $response->delete();

        // Redirect ke halaman index jawaban
       return redirect()->route('forms.responses', $form)
        ->with('success', 'Jawaban berhasil dihapus.');
    }

    public function exportResponses(Form $form)
    {
        // Otorisasi: Pastikan hanya pemilik form yang bisa mengunduh
        if ($form->user->isNot(Auth::user())) {
            abort(403, 'Unauthorized');
        }

        // Buat nama file yang deskriptif
        $fileName = 'jawaban-' . Str::slug($form->title) . '-' . now()->format('Y-m-d') . '.xlsx';

        // Panggil Laravel Excel untuk mendownload file
        return Excel::download(new FormResponsesExport($form), $fileName);
    }
}
