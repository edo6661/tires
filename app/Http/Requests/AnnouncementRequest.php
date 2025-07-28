<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class AnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'is_active' => 'boolean',
            'published_at' => 'nullable|date',
            'translations.en.title' => 'required|string|max:255',
            'translations.en.content' => 'required|string',
            'translations.ja.title' => 'required|string|max:255',
            'translations.ja.content' => 'required|string',
        ];
    }
    public function messages(): array
    {
        return [
            'translations.en.title.required' => 'Judul dalam bahasa Inggris wajib diisi.',
            'translations.en.title.string' => 'Judul dalam bahasa Inggris harus berupa teks.',
            'translations.en.title.max' => 'Judul dalam bahasa Inggris maksimal 255 karakter.',
            'translations.en.content.required' => 'Konten dalam bahasa Inggris wajib diisi.',
            'translations.en.content.string' => 'Konten dalam bahasa Inggris harus berupa teks.',
            'translations.ja.title.required' => 'Judul dalam bahasa Jepang wajib diisi.',
            'translations.ja.title.string' => 'Judul dalam bahasa Jepang harus berupa teks.',
            'translations.ja.title.max' => 'Judul dalam bahasa Jepang maksimal 255 karakter.',
            'translations.ja.content.required' => 'Konten dalam bahasa Jepang wajib diisi.',
            'translations.ja.content.string' => 'Konten dalam bahasa Jepang harus berupa teks.',
            'is_active.boolean' => 'Status aktif harus berupa true atau false.',
            'published_at.date' => 'Tanggal publikasi harus berupa tanggal yang valid.',
        ];
    }
    /**
     * Prepare data for validation
     */
    protected function prepareForValidation()
    {
        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => $this->boolean('is_active')
            ]);
        }
        if (empty($this->published_at)) {
            $this->merge([
                'published_at' => now()
            ]);
        }
    }
    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'translations.en.title' => 'judul bahasa Inggris',
            'translations.en.content' => 'konten bahasa Inggris',
            'translations.ja.title' => 'judul bahasa Jepang',
            'translations.ja.content' => 'konten bahasa Jepang',
            'is_active' => 'status aktif',
            'published_at' => 'tanggal publikasi',
        ];
    }
}
