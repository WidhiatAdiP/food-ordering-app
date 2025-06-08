<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::select('id', 'name', 'price', 'image')->get();
        return response(['data' => $items]);
    }

    public function show($id)
    {
        $item = Item::findOrFail($id);
        return response(['data' => $item]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|max:100',
            'price'      => 'required|integer',
            'image_file' => 'nullable|mimes:jpg,png|max:2048',
        ]);

        $data = $request->only(['name', 'price']);

        if ($request->file('image_file')) {
            $file     = $request->file('image_file');
            $filename = $file->getClientOriginalName();
            $newName  = Carbon::now()->timestamp . '_' . $filename;

            Storage::disk('public')->putFileAs('items', $file, $newName);
            $data['image'] = $newName;
        }

        $item = Item::create($data);

        return response(['data' => $item]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'       => 'required|max:100',
            'price'      => 'required|integer',
            'image_file' => 'nullable|mimes:jpg,png|max:2048',
        ]);

        $item = Item::findOrFail($id);

        $data = $request->only(['name', 'price']);

        if ($request->file('image_file')) {
            // Hapus gambar lama jika ada
            if ($item->image) {
                Storage::disk('public')->delete('items/' . $item->image);
            }

            // Proses gambar baru
            $file     = $request->file('image_file');
            $filename = $file->getClientOriginalName();
            $newName  = Carbon::now()->timestamp . '_' . $filename;

            Storage::disk('public')->putFileAs('items', $file, $newName);
            $data['image'] = $newName;
        }

        $item->update($data);

        return response([
            'data'    => $item,
            'message' => 'Item updated successfully.',
        ]);
    }
}
