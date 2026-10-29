<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Support\Str;

// Resource controller: index, create, store, edit, update, destroy (show tidak dipakai)
class CategoryController extends Controller
{
    public function index()
    {
        // withCount menambah kolom reports_count tanpa mengambil semua laporannya
        $categories = Category::withCount('reports')->orderBy('name')->get();

        return view('admin.categories.index', ['categories' => $categories]);
    }

    public function create()
    {
        return view('admin.categories.create', ['category' => new Category()]);
    }

    public function store(CategoryRequest $request)
    {
        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'icon' => $request->icon,
            'is_sensitive' => $request->boolean('is_sensitive'),
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori "' . $request->name . '" ditambahkan.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', ['category' => $category]);
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'icon' => $request->icon,
            'is_sensitive' => $request->boolean('is_sensitive'),
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori "' . $category->name . '" diperbarui.');
    }

    public function destroy(Category $category)
    {
        // Kategori yang sudah dipakai laporan tidak boleh dihapus, supaya laporannya tidak kehilangan kategori
        if ($category->reports()->exists()) {
            return back()->with('error', 'Kategori "' . $category->name . '" tidak bisa dihapus karena sudah dipakai '
                . $category->reports()->count() . ' laporan.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori "' . $category->name . '" dihapus.');
    }
}
