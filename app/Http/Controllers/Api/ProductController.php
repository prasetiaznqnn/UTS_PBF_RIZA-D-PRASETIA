<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return response()->json([
            "status" => true,
            "message" => "data product list",
            "data" => $products
        ], 200);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'price' => 'required|integer',
            'category_id' => 'required',
            'expired_at' => 'required|date',
            'image' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()
            ], 422);
        }

        $category = Category::where('name', $request->category_id)->first();
        if (!$category) {
            return response()->json([
                "status" => false,
                "message" => "Kategori tidak di temukan"
            ], 404);
        }

        $hash_image = $request->file('image')->hashName();
        $request->file('image')->move(public_path('uploads/images/'), $hash_image);
        $image =  "uploads/images/" . $hash_image;

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $category->id,
            'image' => $image,
            'expired_at' => $request->expired_at,
            'modified_by' => auth()->user()->email
        ]);

        return response()->json([
            "status" => true,
            "message" => "data product created",
            "data" => $product
        ], 200);
    }


    public function update(Request $request, string $id)
    {

        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                "status" => false,
                "message" => "data product not found"
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'price' => 'required|integer',
            'category_id' => 'required',
            'expired_at' => 'required|date',
            'image' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()
            ], 422);
        }

        $category = Category::where('name', $request->category_id)->first();
        if (!$category) {
            return response()->json([
                "status" => false,
                "message" => "Kategori tidak di temukan"
            ], 404);
        }

        $old_image = $product->image;
        if ($request->file('image')) {
            $hash_image = $request->file('image')->hashName();
            $request->file('image')->move(public_path('uploads/images/'), $hash_image);
            $image =  "uploads/images/" . $hash_image;
            $product->update([
                'image' => $image
            ]);
        } else {
            $product->update([
                'image' => $old_image
            ]);
        }


        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $category->id,
            'expired_at' => $request->expired_at,
            'modified_by' => auth()->user()->email
        ]);

        return response()->json([
            "status" => true,
            "message" => "data product updated",
            "data" => $product
        ], 200);
    }
    public function destroy(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                "status" => false,
                "message" => "data product not found"
            ], 404);
        }
        $product->delete();
        return response()->json([
            "status" => true,
            "message" => "data product deleted"
        ], 200);
    }
}
