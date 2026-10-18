<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API DATA LAMA
|--------------------------------------------------------------------------
*/

Route::get('/data', function () {
    return response()->json([
        'status' => true,
        'data' => DB::table('data_items')->orderBy('id')->get()
    ]);
});

Route::post('/data', function (Request $request) {
    $request->validate([
        'nama' => 'required|string|max:255'
    ]);

    DB::table('data_items')->insert([
        'nama' => $request->nama,
        'created_at' => now(),
        'updated_at' => now()
    ]);

    return response()->json([
        'status' => true,
        'pesan' => 'Data berhasil disimpan'
    ]);
});

Route::delete('/data/{id}', function ($id) {
    $jumlah = DB::table('data_items')->where('id', $id)->delete();

    return response()->json([
        'status' => $jumlah > 0,
        'pesan' => $jumlah > 0
            ? 'Data berhasil dihapus'
            : 'Data tidak ditemukan'
    ]);
});

/*
|--------------------------------------------------------------------------
| API PELANGGAN
|--------------------------------------------------------------------------
*/

Route::get('/pelanggan', function () {
    return response()->json([
        'status' => true,
        'data' => DB::table('pelanggans')->orderBy('nama')->get()
    ]);
});

Route::post('/pelanggan', function (Request $request) {
    $request->validate([
        'nama' => 'required|string|max:255',
        'no_hp' => 'nullable|string|max:30'
    ]);

    $id = DB::table('pelanggans')->insertGetId([
        'nama' => $request->nama,
        'no_hp' => $request->no_hp,
        'created_at' => now(),
        'updated_at' => now()
    ]);

    return response()->json([
        'status' => true,
        'pesan' => 'Pelanggan berhasil ditambahkan',
        'data' => DB::table('pelanggans')->where('id', $id)->first()
    ], 201);
});

Route::put('/pelanggan/{id}', function (Request $request, $id) {
    $request->validate([
        'nama' => 'required|string|max:255',
        'no_hp' => 'nullable|string|max:30'
    ]);

    $jumlah = DB::table('pelanggans')->where('id', $id)->update([
        'nama' => $request->nama,
        'no_hp' => $request->no_hp,
        'updated_at' => now()
    ]);

    return response()->json([
        'status' => $jumlah > 0,
        'pesan' => $jumlah > 0
            ? 'Pelanggan berhasil diperbarui'
            : 'Pelanggan tidak ditemukan atau tidak ada perubahan'
    ]);
});

Route::delete('/pelanggan/{id}', function ($id) {
    $jumlah = DB::table('pelanggans')->where('id', $id)->delete();

    return response()->json([
        'status' => $jumlah > 0,
        'pesan' => $jumlah > 0
            ? 'Pelanggan berhasil dihapus'
            : 'Pelanggan tidak ditemukan'
    ]);
});

/*
|--------------------------------------------------------------------------
| API KENDARAAN / MOTOR
|--------------------------------------------------------------------------
*/

Route::get('/kendaraan', function () {
    $data = DB::table('kendaraans')
        ->join('pelanggans', 'kendaraans.pelanggan_id', '=', 'pelanggans.id')
        ->select(
            'kendaraans.*',
            'pelanggans.nama as nama_pelanggan',
            'pelanggans.no_hp'
        )
        ->orderBy('kendaraans.id', 'desc')
        ->get();

    return response()->json([
        'status' => true,
        'data' => $data
    ]);
});

Route::post('/kendaraan', function (Request $request) {
    $request->validate([
        'pelanggan_id' => 'required|integer|exists:pelanggans,id',
        'nomor_polisi' => 'required|string|max:30|unique:kendaraans,nomor_polisi',
        'merek' => 'required|string|max:100',
        'tipe' => 'nullable|string|max:100'
    ]);

    $id = DB::table('kendaraans')->insertGetId([
        'pelanggan_id' => $request->pelanggan_id,
        'nomor_polisi' => strtoupper($request->nomor_polisi),
        'merek' => $request->merek,
        'tipe' => $request->tipe,
        'created_at' => now(),
        'updated_at' => now()
    ]);

    return response()->json([
        'status' => true,
        'pesan' => 'Kendaraan berhasil ditambahkan',
        'data' => DB::table('kendaraans')->where('id', $id)->first()
    ], 201);
});

Route::put('/kendaraan/{id}', function (Request $request, $id) {
    $request->validate([
        'pelanggan_id' => 'required|integer|exists:pelanggans,id',
        'nomor_polisi' => 'required|string|max:30|unique:kendaraans,nomor_polisi,' . $id,
        'merek' => 'required|string|max:100',
        'tipe' => 'nullable|string|max:100'
    ]);

    $jumlah = DB::table('kendaraans')->where('id', $id)->update([
        'pelanggan_id' => $request->pelanggan_id,
        'nomor_polisi' => strtoupper($request->nomor_polisi),
        'merek' => $request->merek,
        'tipe' => $request->tipe,
        'updated_at' => now()
    ]);

    return response()->json([
        'status' => $jumlah > 0,
        'pesan' => $jumlah > 0
            ? 'Kendaraan berhasil diperbarui'
            : 'Kendaraan tidak ditemukan atau tidak ada perubahan'
    ]);
});

Route::delete('/kendaraan/{id}', function ($id) {
    $jumlah = DB::table('kendaraans')->where('id', $id)->delete();

    return response()->json([
        'status' => $jumlah > 0,
        'pesan' => $jumlah > 0
            ? 'Kendaraan berhasil dihapus'
            : 'Kendaraan tidak ditemukan'
    ]);
});

/*
|--------------------------------------------------------------------------
| API RIWAYAT SERVIS
|--------------------------------------------------------------------------
*/

// Cari riwayat servis berdasarkan nomor polisi.
Route::get('/riwayat-servis/cari/{nomor_polisi}', function ($nomor_polisi) {
    $data = DB::table('riwayat_servis')
        ->join('kendaraans', 'riwayat_servis.kendaraan_id', '=', 'kendaraans.id')
        ->join('pelanggans', 'kendaraans.pelanggan_id', '=', 'pelanggans.id')
        ->where('kendaraans.nomor_polisi', strtoupper($nomor_polisi))
        ->select(
            'riwayat_servis.*',
            'kendaraans.nomor_polisi',
            'kendaraans.merek',
            'kendaraans.tipe',
            'pelanggans.nama as nama_pelanggan',
            'pelanggans.no_hp'
        )
        ->orderBy('riwayat_servis.tanggal_servis', 'desc')
        ->get();

    return response()->json([
        'status' => true,
        'data' => $data
    ]);
});

// Tampilkan seluruh riwayat servis.
Route::get('/riwayat-servis', function () {
    $data = DB::table('riwayat_servis')
        ->join('kendaraans', 'riwayat_servis.kendaraan_id', '=', 'kendaraans.id')
        ->join('pelanggans', 'kendaraans.pelanggan_id', '=', 'pelanggans.id')
        ->select(
            'riwayat_servis.*',
            'kendaraans.nomor_polisi',
            'kendaraans.merek',
            'kendaraans.tipe',
            'pelanggans.nama as nama_pelanggan',
            'pelanggans.no_hp'
        )
        ->orderBy('riwayat_servis.tanggal_servis', 'desc')
        ->get();

    return response()->json([
        'status' => true,
        'data' => $data
    ]);
});

// Tambahkan catatan servis.
Route::post('/riwayat-servis', function (Request $request) {
    $request->validate([
        'kendaraan_id' => 'required|integer|exists:kendaraans,id',
        'tanggal_servis' => 'required|date',
        'keluhan' => 'required|string',
        'tindakan' => 'nullable|string',
        'kilometer' => 'nullable|integer|min:0',
        'biaya' => 'required|numeric|min:0',
        'status' => 'required|in:Menunggu,Dikerjakan,Selesai'
    ]);

    $id = DB::table('riwayat_servis')->insertGetId([
        'kendaraan_id' => $request->kendaraan_id,
        'tanggal_servis' => $request->tanggal_servis,
        'keluhan' => $request->keluhan,
        'tindakan' => $request->tindakan,
        'kilometer' => $request->kilometer,
        'biaya' => $request->biaya,
        'status' => $request->status,
        'created_at' => now(),
        'updated_at' => now()
    ]);

    return response()->json([
        'status' => true,
        'pesan' => 'Riwayat servis berhasil disimpan',
        'data' => DB::table('riwayat_servis')->where('id', $id)->first()
    ], 201);
});

// Perbarui catatan servis.
Route::put('/riwayat-servis/{id}', function (Request $request, $id) {
    $request->validate([
        'kendaraan_id' => 'required|integer|exists:kendaraans,id',
        'tanggal_servis' => 'required|date',
        'keluhan' => 'required|string',
        'tindakan' => 'nullable|string',
        'kilometer' => 'nullable|integer|min:0',
        'biaya' => 'required|numeric|min:0',
        'status' => 'required|in:Menunggu,Dikerjakan,Selesai'
    ]);

    $jumlah = DB::table('riwayat_servis')->where('id', $id)->update([
        'kendaraan_id' => $request->kendaraan_id,
        'tanggal_servis' => $request->tanggal_servis,
        'keluhan' => $request->keluhan,
        'tindakan' => $request->tindakan,
        'kilometer' => $request->kilometer,
        'biaya' => $request->biaya,
        'status' => $request->status,
        'updated_at' => now()
    ]);

    return response()->json([
        'status' => $jumlah > 0,
        'pesan' => $jumlah > 0
            ? 'Riwayat servis berhasil diperbarui'
            : 'Riwayat tidak ditemukan atau tidak ada perubahan'
    ]);
});

// Hapus catatan servis.
Route::delete('/riwayat-servis/{id}', function ($id) {
    $jumlah = DB::table('riwayat_servis')->where('id', $id)->delete();

    return response()->json([
        'status' => $jumlah > 0,
        'pesan' => $jumlah > 0
            ? 'Riwayat servis berhasil dihapus'
            : 'Riwayat servis tidak ditemukan'
    ]);
});