<?php

namespace App\Exports;

use App\Models\Form;
use Illuminate\Support\Str;
use App\Models\FormResponse;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FormResponsesExport implements FromQuery, WithHeadings, WithMapping
{
    protected $form;
    protected $fields;

    public function __construct(Form $form)
    {
        $this->form = $form;
        // Ambil field dan keyBy 'name' untuk mapping yang lebih mudah
        $this->fields = collect($form->form_fields)->keyBy('name');
    }

    /**
     * Menentukan query untuk mengambil data dari database.
     */
    public function query()
    {
        return FormResponse::query()->where('form_id', $this->form->id)->orderBy('created_at', 'asc');
    }

    /**
     * Menentukan header (judul kolom) untuk file Excel.
     */
   public function headings(): array
    {
        // Ambil label, lalu bersihkan tag HTML dari setiap label menggunakan map()
        $headings = $this->fields->pluck('label')->map(function ($label) {
            return strip_tags($label);
        })->toArray();

        // Tambahkan kolom "Tanggal Kirim" di awal
        array_unshift($headings, 'Tanggal Kirim');

        return $headings;
    }

    /**
     * Memetakan setiap baris data ke kolom yang sesuai di Excel.
     *
     * @param FormResponse $response
     */
    public function map($response): array
    {
        $answers = $response->response_data ?? [];
        $rowData = [];

        // Kolom pertama adalah timestamp
        $rowData[] = $response->created_at->format('Y-m-d H:i:s');

        // Loop melalui semua field form untuk memastikan urutan kolom benar
        foreach ($this->fields as $fieldName => $field) {
            $answer = $answers[$fieldName] ?? ''; // Default jawaban kosong jika tidak ada

            // Jika jawaban adalah array (misal. dari checkbox), gabungkan jadi satu string
            if (is_array($answer)) {
                $answer = implode(', ', $answer);
            }

            // Jika tipe file, berikan link langsung (opsional, atau nama file saja)
            if ($field['type'] === 'file' && !empty($answer)) {
                 $answer = url(Storage::url($answer));
            }

            // Hapus tag HTML dari jawaban untuk kebersihan data
            $rowData[] = strip_tags($answer);
        }

        return $rowData;
    }
}
