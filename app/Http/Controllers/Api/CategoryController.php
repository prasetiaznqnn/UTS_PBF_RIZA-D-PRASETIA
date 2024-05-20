<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return response()->json([
            "status" => true,
            "data" => $categories
        ], 200);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()
            ], 422);
        }

        $category = Category::create([
            'name' => $request->name
        ]);

        if (!$category) {
            return response()->json([
                "status" => false,
                "message" => "Gagal menambahkan kategori"
            ], 500);
        }

        return response()->json([
            "status" => true,
            "data" => $category
        ], 200);
    }

    public function update(Request $request, string $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                "status" => false,
                "message" => "Kategori tidak ditemukan"
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()
            ], 422);
        }

        $category->update([
            'name' => $request->name
        ]);

        return response()->json([
            "status" => true,
            "message" => "Kategori diperbarui",
            "data" => $category
        ], 200);
    }

    public function destroy(string $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                "status" => false,
                "message" => "Kategori tidak ditemukan"
            ], 404);
        }

        // hapus data product yang terdapat id category
        $product = $category->products();
        $product->delete();
        $category->delete();

        return response()->json([
            "status" => true,
            "message" => "Kategori di hapus dan semua product yang terdapat id category di hapus",
        ], 200);
    }
}
