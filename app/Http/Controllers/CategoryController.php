<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $category = Category::orderBy('name', 'asc')->get();
        return response()->json([
            'categories' => $category
        ], 200);
    }

    public function store(Request $request)
    {
        // Validation
        $attr = $request->validate([
            'name'   => 'required|string|unique:categories,name',
        ]);

        $category = Category::create([
            'name' => $attr['name'],
        ]);

        return response()->json([
            'category' => $category,
            'message' => 'Kategori berhasil dibuat',
        ], 200);
    }

    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response([
                'message' => 'Kategori tidak ditemukan.'
            ], 403);
        }

        $category->delete();

        return response()->json([
            'message' => 'Kategori berhasil dihapus.'
        ]);
    }
}
