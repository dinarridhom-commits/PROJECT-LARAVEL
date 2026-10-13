<?php
namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $query = Produk::with('kategori');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%")
                  ->orWhere('kode_produk', 'like', "%{$search}%")
                  ->orWhereHas('kategori', function($k) use ($search) {
                      $k->where('nama_kategori', 'like', "%{$search}%");
                  });
            });
        }
        $title = 'Daftar Produk';

        $data = $query->get();
            
        return view('produk.show', compact('data', 'title')); 
    }

    public function create()
    {
        $kategori = Kategori::all();
        return view('produk.add', [
            'title' => 'Tambah Data'
        ], compact('kategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required',
            'harga'       => 'required|numeric',
            'stok'        => 'required|numeric',
            'kode_produk' => 'nullable|string',
            'kategori_id' => 'nullable',
            'deskripsi'   => 'nullable|string',
            'gambar'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'nama_produk.required' => 'Nama produk wajib diisi',
            'harga.required'       => 'Harga wajib diisi',
            'stok.required'        => 'Stok wajib diisi',
            'gambar.image'         => 'File harus berupa gambar',
            'gambar.max'           => 'Ukuran gambar maksimal 2MB',
        ]);

        $kodeProduk = $request->kode_produk;
        if (empty($kodeProduk)) {
            $kodeProduk = 'PRD-' . rand(1000, 9999);
        }

        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('produk', 'public');
        }

        Produk::create([
            'kode_produk' => $kodeProduk,
            'nama_produk' => $request->nama_produk,
            'harga'       => $request->harga,
            'stok'        => $request->stok,
            'deskripsi'   => $request->deskripsi,
            'kategori_id' => $request->kategori_id,
            'gambar'      => $gambarPath,
        ]);

        return redirect('/produk')->with('message', 'Data berhasil ditambahkan');  
    }

    public function show($id)
    {
        $produk = Produk::with('kategori')->findOrFail($id);

        return view('produk.detail', [
            'title' => 'Detail Data'
        ], compact('produk'));
    }

    public function edit($id)
    {
        $data = Produk::findOrFail($id);
        $kategori = Kategori::all();

        return view('produk.edit', [
            'title' => 'Edit Data'
        ], compact('data', 'kategori'));   
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_produk' => 'required',
            'harga'       => 'required|numeric',
            'stok'        => 'required|numeric',
            'kode_produk' => 'nullable|string',
            'kategori_id' => 'nullable',
            'deskripsi'   => 'nullable|string',
            'gambar'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'nama_produk.required' => 'Nama produk wajib diisi',
            'harga.required'       => 'Harga wajib diisi',
            'stok.required'        => 'Stok wajib diisi',
            'gambar.image'         => 'File harus berupa gambar',
            'gambar.max'           => 'Ukuran gambar maksimal 2MB',
        ]);

        $produk = Produk::findOrFail($id);

        $kodeProduk = $request->kode_produk ?? $produk->kode_produk;
        if (empty($kodeProduk)) {
            $kodeProduk = 'PRD-' . rand(1000, 9999);
        }

        $gambarPath = $produk->gambar;
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($produk->gambar && \Illuminate\Support\Facades\Storage::disk('public')->exists($produk->gambar)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($produk->gambar);
            }
            $gambarPath = $request->file('gambar')->store('produk', 'public');
        }

        $produk->update([
            'kode_produk' => $kodeProduk,
            'nama_produk' => $request->nama_produk,
            'harga'       => $request->harga,
            'stok'        => $request->stok,
            'deskripsi'   => $request->deskripsi,
            'kategori_id' => $request->kategori_id,
            'gambar'      => $gambarPath,
        ]);

        return redirect('/produk')->with('message', 'Data berhasil diperbarui');
    }

    public function destroy($id)
    {
        $item = Produk::findOrFail($id);
        if ($item->gambar && \Illuminate\Support\Facades\Storage::disk('public')->exists($item->gambar)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($item->gambar);
        }
        $item->delete();

        return redirect('/produk')->with('message', 'Data berhasil dihapus');
    }
}



