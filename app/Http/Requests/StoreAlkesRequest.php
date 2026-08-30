<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAlkesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama_barang' => 'required|string|max:255',
            'kode_inventaris' => 'nullable|string',
            'nomor_seri' => 'nullable|string',
            'nomenklatur_id' => 'nullable|exists:nomenklatur,id',
            'merk' => 'nullable|string',
            'tipe' => 'nullable|string',
            'tahun_pengadaan' => 'nullable|string',
            'jumlah' => 'nullable|integer|min:1',
            'ruangan_id' => 'required|exists:ruangan,id',
            'status' => 'required',
            'kondisi' => 'required',
            'aspak_status' => 'nullable|string',
            'kib_status' => 'nullable|boolean',
            'keterangan' => 'nullable|string',
        ];
    }
}
