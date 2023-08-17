<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockReport;
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
            'discount'      => 'numeric',
            'discountPrice' => 'numeric',
            'stock'         => 'required|numeric',
        ]);

        $productData = [
            'name'          => $attr['name'],
            'category'      => $attr['category'],
            'desc'          => $attr['desc'],
            'priceBuy'      => $attr['priceBuy'],
            'priceSell'     => $attr['priceSell'],
            'discount'      => $attr['discount'],
            'discountPrice' => $attr['discountPrice'],
            'stock'         => $attr['stock'],
        ];

        if ($request->hasFile('image')) {
            $productData['image'] = $request->file('image')->store('image');
        }

        $product = Product::create($productData);

        // Create a new StockReport entry
        StockReport::create([
            'product_id' => $product->id,
            'stock_first' => $product->stock,
            'stock_now' => $product->stock,
        ]);

        return response()->json([
            'product' => $product,
            'message' => 'Produk berhasil diupload',
        ], 200);
    }

    public function editproduct(Request $request, $id)
    {
        // Validation
        $attr = $request->validate([
            'name'          => 'required|string',
            'category'      => 'required|numeric',
            'desc'          => 'required|string',
            'image'         => 'image|mimes:jpg,jpeg,png,bmp,gif,svg,webp|max:1024',
            'priceBuy'      => 'required|numeric',
            'priceSell'     => 'required|numeric',
            'discount'      => 'numeric',
            'discountPrice' => 'numeric',
            'stock'         => 'required|numeric',
        ]);

        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Update product data
        $product->name          = $attr['name'];
        $product->category      = $attr['category'];
        $product->desc          = $attr['desc'];
        $product->priceBuy      = $attr['priceBuy'];
        $product->priceSell     = $attr['priceSell'];
        $product->discount      = $attr['discount'];
        $product->discountPrice = $attr['discountPrice'];
        $product->stock         = $attr['stock'];


        if ($request->hasFile('image')) {
            // Delete the old image file (optional if you want to update the image)
            Storage::delete($product->image);
            $product->image = $request->file('image')->store('image');
        }

        $product->save();

        return response()->json([
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
