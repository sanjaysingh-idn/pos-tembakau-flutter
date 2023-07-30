<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->get();
        return response([
            'products' => $products
        ], 200);
    }

    public function store(Request $request)
    {
        // Validation
        $attr = $request->validate([
            'name'          => 'required|string|unique:products,name',
            'category'      => 'required|numeric',
            'desc'          => 'required|string',
            'image'         => 'image|mimes:jpg,jpeg,png,bmp,gif,svg,webp|max:1024',
            'priceBuy'      => 'required|numeric',
            'priceSell'     => 'required|numeric',
            'stock'         => 'required|numeric',
        ]);

        $productData = [
            'name'      => $attr['name'],
            'category'  => $attr['category'],
            'desc'      => $attr['desc'],
            'priceBuy'  => $attr['priceBuy'],
            'priceSell' => $attr['priceSell'],
            'stock'     => $attr['stock'],
        ];

        if ($request->hasFile('image')) {
            $productData['image'] = $request->file('image')->store('image');
        }

        $product = Product::create($productData);

        return response()->json([
            'product' => $product,
            'message' => 'Produk berhasil diupload',
        ], 200);
    }

    public function update(Request $request, $id)
    {
        // Validation
        $attr = $request->validate([
            'name'          => 'required|string|unique:products,name,' . $id,
            'category'      => 'required|numeric',
            'desc'          => 'required|string',
            'image'         => 'image|mimes:jpg,jpeg,png,bmp,gif,svg,webp|max:1024',
            'priceBuy'      => 'required|numeric',
            'priceSell'     => 'required|numeric',
            'stock'         => 'required|numeric',
        ]);

        $productData = [
            'name'      => $attr['name'],
            'category'  => $attr['category'],
            'desc'      => $attr['desc'],
            'priceBuy'  => $attr['priceBuy'],
            'priceSell' => $attr['priceSell'],
            'stock'     => $attr['stock'],
        ];

        // Handle image update if provided
        if ($request->hasFile('image')) {
            $productData['image'] = $request->file('image')->store('image');
        }

        // Find the product by ID
        $product = Product::findOrFail($id);

        // Update the product data
        $product->update($productData);

        return response()->json([
            'product' => $product,
            'message' => 'Produk berhasil diupdate',
        ], 200);
    }

    public function destroy($id)
    {
        $product = Product::find($id);

        if (!empty($product->image)) {
            Storage::delete($product->image);
        }

        $product->delete();

        return response()->json([
            'message' => 'Product berhasil dihapus.'
        ]);
    }
}
