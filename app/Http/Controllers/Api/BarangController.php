<?php

namespace App\Http\Controllers\Api;

use App\Models\Barang;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BarangResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BarangController extends Controller
{    
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        //get data_barang
        $barangs = Barang::latest()->paginate(5);

        //return collection of posts as a resource
        return new BarangResource(true, 'List Data Barang', $barangs);
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'nama_barang'         => 'required',
            'jenis_barang'        => 'required',
            'keterangan_barang'   => 'required',
            'image'               => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/barangs', $image->hashName());

        //create barang
        $barang = Barang::create([
            'nama_barang'       => $request->nama_barang,
            'jenis_barang'      => $request->jenis_barang,
            'keterangan_barang' => $request->keterangan_barang,
            'image'             => $image->hashName(),
            
        ]);

        //return response
        return new BarangResource(true, 'Data Barang Berhasil Ditambahkan!', $barang);
    }

    /**
     * show
     *
     * @param  mixed $barang
     * @return void
     */
    public function show(Barang $barang)
    {
        //return single post as a resource
        return new BarangResource(true, 'Data Barang Ditemukan!', $barang);
    }

    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $barang
     * @return void
     */
    public function update(Request $request, Barang $barang)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'nama_barang'         => 'required',
            'jenis_barang'        => 'required',
            'keterangan_barang'   => 'required',
            
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //check if image is not empty
        if ($request->hasFile('image')) {

            //upload image
            $image = $request->file('image');
            $image->storeAs('public/barangs', $image->hashName());

            //delete old image
            Storage::delete('public/barangs/'.$barang->image);

            //update barang with new image
            $barang->update([
                'nama_barang'       => $request->nama_barang,
                'jenis_barang'      => $request->jenis_barang,
                'keterangan_barang' => $request->keterangan_barang,
                'image'             => $image->hashName(),
            ]);

        } else {

            //update barang without image
            $barang->update([
                'nama_barang'       => $request->nama_barang,
                'jenis_barang'      => $request->jenis_barang,
                'keterangan_barang' => $request->keterangan_barang,
                
            ]);
        }

        //return response
        return new BarangResource(true, 'Data Barang Berhasil Diubah!', $barang);
    }

    /**
     * destroy
     *
     * @param  mixed $barang
     * @return void
     */
    public function destroy(Barang $barang)
    {
        //delete image
        Storage::delete('public/barangs/'.$barang->image);

        //delete barang
        $barang->delete();

        //return response
        return new BarangResource(true, 'Data Barang Berhasil Dihapus!', null);
    }
}